<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\VatRate;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function criarUtilizador(string ...$permissoes): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        assert($user instanceof User);

        if (!empty($permissoes)) {
            $user->givePermissionTo($permissoes);
        }

        return $user;
    }

    private function criarTaxaIva(): VatRate
    {
        return VatRate::create([
            'name' => 'IVA Normal',
            'value' => 23.00,
            'is_active' => true,
        ]);
    }

    public function test_utilizador_nao_autenticado_e_redirecionado_ao_aceder_a_artigos(): void
    {
        $response = $this->get(route('articles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_utilizador_sem_permissao_recebe_forbidden(): void
    {
        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->get(route('articles.index'));

        $response->assertForbidden();
    }

    public function test_utilizador_com_permissao_pode_listar_artigos(): void
    {
        $user = $this->criarUtilizador('artigos.ler');

        $response = $this->actingAs($user)->get(route('articles.index'));

        $response->assertOk();
    }

    public function test_utilizador_com_permissao_pode_criar_artigo_com_fotografia(): void
    {
        Storage::fake('local');

        $user = $this->criarUtilizador('artigos.criar', 'artigos.ler');
        $vat = $this->criarTaxaIva();
        $file = UploadedFile::fake()->image('produto.jpg');

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'reference' => 'ART001',
            'name' => 'Artigo Teste',
            'description' => 'Descrição do artigo',
            'price' => 99.99,
            'vat_rate_id' => $vat->id,
            'photo' => $file,
            'notes' => 'Observações',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'reference' => 'ART001',
            'name' => 'Artigo Teste',
            'price' => 99.99,
            'vat_rate_id' => $vat->id,
        ]);

        $article = Article::where('reference', 'ART001')->firstOrFail();
        $this->assertNotNull($article->photo_path);
        $this->assertTrue(Storage::disk('local')->exists($article->photo_path));
    }

    public function test_validacao_rejeita_preco_negativo_e_campos_obrigatorios(): void
    {
        $user = $this->criarUtilizador('artigos.criar');

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'price' => -10,
        ]);

        $response->assertSessionHasErrors(['reference', 'name', 'price', 'vat_rate_id']);
    }

    public function test_validacao_rejeita_referencia_duplicada(): void
    {
        $user = $this->criarUtilizador('artigos.criar');
        $vat = $this->criarTaxaIva();

        Article::create([
            'reference' => 'ART001',
            'name' => 'Artigo 1',
            'price' => 10,
            'vat_rate_id' => $vat->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'reference' => 'ART001',
            'name' => 'Artigo 2',
            'price' => 20,
            'vat_rate_id' => $vat->id,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors(['reference']);
    }

    public function test_utilizador_com_permissao_pode_atualizar_artigo(): void
    {
        $user = $this->criarUtilizador('artigos.atualizar', 'artigos.ler');
        $vat = $this->criarTaxaIva();

        $article = Article::create([
            'reference' => 'ART001',
            'name' => 'Artigo Antigo',
            'price' => 10.00,
            'vat_rate_id' => $vat->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('articles.update', $article), [
            'reference' => 'ART001',
            'name' => 'Artigo Atualizado',
            'price' => 15.50,
            'vat_rate_id' => $vat->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'name' => 'Artigo Atualizado',
            'price' => 15.50,
        ]);
    }

    public function test_utilizador_com_permissao_pode_eliminar_artigo_e_remover_fotografia(): void
    {
        Storage::fake('local');

        $user = $this->criarUtilizador('artigos.eliminar');
        $vat = $this->criarTaxaIva();
        $file = UploadedFile::fake()->image('produto.jpg');
        $path = $file->store('articles');

        $article = Article::create([
            'reference' => 'ART001',
            'name' => 'Artigo',
            'price' => 10,
            'vat_rate_id' => $vat->id,
            'photo_path' => $path,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('articles.destroy', $article));

        $response->assertRedirect();
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
        $this->assertFalse(Storage::disk('local')->exists($path));
    }

    public function test_utilizador_pode_aceder_a_fotografia_protegida(): void
    {
        Storage::fake('local');

        $user = $this->criarUtilizador('artigos.ler');
        $vat = $this->criarTaxaIva();
        $file = UploadedFile::fake()->image('produto.jpg');
        $path = $file->store('articles');

        $article = Article::create([
            'reference' => 'ART001',
            'name' => 'Artigo',
            'price' => 10,
            'vat_rate_id' => $vat->id,
            'photo_path' => $path,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('articles.photo', $article));

        $response->assertOk();
    }
}