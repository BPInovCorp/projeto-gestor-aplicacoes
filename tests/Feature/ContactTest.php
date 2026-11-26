<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Country;
use App\Models\Entity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
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

    private function criarEntidade(): Entity
    {
        $country = Country::create([
            'name' => 'Portugal',
            'code' => 'PT',
            'is_active' => true,
        ]);

        return Entity::create([
            'name' => 'Empresa Teste Lda',
            'is_client' => true,
            'is_supplier' => false,
            'nif' => '500000000',
            'country_id' => $country->id,
            'is_active' => true,
        ]);
    }

    public function test_utilizador_nao_autenticado_e_redirecionado_ao_aceder_a_contactos(): void
    {
        $response = $this->get(route('contacts.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_utilizador_sem_permissao_recebe_forbidden(): void
    {
        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->get(route('contacts.index'));

        $response->assertForbidden();
    }

    public function test_utilizador_com_permissao_pode_listar_contactos(): void
    {
        $user = $this->criarUtilizador('contactos.ler');

        $response = $this->actingAs($user)->get(route('contacts.index'));

        $response->assertOk();
    }

    public function test_utilizador_com_permissao_pode_criar_contacto_com_numero_incremental(): void
    {
        $user = $this->criarUtilizador('contactos.criar', 'contactos.ler');
        $entity = $this->criarEntidade();

        $role = ContactRole::create([
            'name' => 'Diretor Comercial',
            'is_active' => true,
        ]);

        $dados = [
            'entity_id' => $entity->id,
            'contact_role_id' => $role->id,
            'first_name' => 'João',
            'last_name' => 'Silva',
            'phone' => '210000000',
            'mobile' => '910000000',
            'email' => 'joao.silva@exemplo.pt',
            'rgpd_consent' => true,
            'notes' => 'Contacto principal',
            'is_active' => true,
        ];

        $response = $this->actingAs($user)->post(route('contacts.store'), $dados);

        $response->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'entity_id' => $entity->id,
            'contact_role_id' => $role->id,
            'first_name' => 'João',
            'last_name' => 'Silva',
            'number' => 1,
            'rgpd_consent' => true,
        ]);

        $dadosSegundo = array_merge($dados, [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@exemplo.pt',
        ]);

        $this->actingAs($user)->post(route('contacts.store'), $dadosSegundo);

        $this->assertDatabaseHas('contacts', [
            'first_name' => 'Maria',
            'number' => 2,
        ]);
    }

    public function test_validacao_rejeita_contacto_sem_campos_obrigatorios(): void
    {
        $user = $this->criarUtilizador('contactos.criar');

        $response = $this->actingAs($user)->post(route('contacts.store'), []);

        $response->assertSessionHasErrors(['entity_id', 'first_name', 'last_name']);
    }

    public function test_utilizador_com_permissao_pode_atualizar_contacto(): void
    {
        $user = $this->criarUtilizador('contactos.atualizar', 'contactos.ler');
        $entity = $this->criarEntidade();

        $contact = Contact::create([
            'entity_id' => $entity->id,
            'first_name' => 'Carlos',
            'last_name' => 'Ferreira',
            'email' => 'carlos@exemplo.pt',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put(route('contacts.update', $contact), [
            'entity_id' => $entity->id,
            'first_name' => 'Carlos Alberto',
            'last_name' => 'Ferreira',
            'email' => 'carlos.alberto@exemplo.pt',
            'rgpd_consent' => true,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => 'Carlos Alberto',
            'email' => 'carlos.alberto@exemplo.pt',
            'rgpd_consent' => true,
        ]);
    }

    public function test_utilizador_com_permissao_pode_eliminar_contacto(): void
    {
        $user = $this->criarUtilizador('contactos.eliminar');
        $entity = $this->criarEntidade();

        $contact = Contact::create([
            'entity_id' => $entity->id,
            'first_name' => 'Ana',
            'last_name' => 'Mendes',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('contacts.destroy', $contact));

        $response->assertRedirect();
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}