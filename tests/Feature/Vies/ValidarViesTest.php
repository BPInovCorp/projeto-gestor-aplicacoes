<?php

namespace Tests\Feature\Vies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ValidarViesTest extends TestCase
{
    use RefreshDatabase;

    private function criarUtilizador(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        assert($user instanceof User);

        return $user;
    }

    public function test_utilizador_nao_autenticado_nao_pode_validar_vies(): void
    {
        $response = $this->postJson(route('vies.validar'), [
            'codigo_pais' => 'PT',
            'nif' => '500000000',
        ]);

        $response->assertUnauthorized();
    }

    public function test_validacao_rejeita_campos_obrigatorios_em_falta(): void
    {
        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->postJson(route('vies.validar'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['codigo_pais', 'nif']);
    }

    public function test_valida_nif_com_sucesso_via_vies(): void
    {
        Http::fake([
            'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/PT/vat/500000000' => Http::response([
                'isValid' => true,
                'name' => 'EMPRESA EXEMPLO LDA',
                'address' => "AVENIDA DA LIBERDADE 100\n1250-096 LISBOA",
            ], 200),
        ]);

        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->postJson(route('vies.validar'), [
            'codigo_pais' => 'PT',
            'nif' => '500000000',
        ]);

        $response->assertOk();
        $response->assertJson([
            'valido' => true,
            'nome' => 'EMPRESA EXEMPLO LDA',
            'morada' => 'AVENIDA DA LIBERDADE 100',
            'codigo_postal' => '1250-096',
            'localidade' => 'LISBOA',
            'mensagem' => 'NIF validado com sucesso.',
        ]);
    }

    public function test_retorna_erro_quando_nif_e_invalido_no_vies(): void
    {
        Http::fake([
            'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/PT/vat/999999999' => Http::response([
                'isValid' => false,
                'name' => '---',
                'address' => '---',
            ], 200),
        ]);

        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->postJson(route('vies.validar'), [
            'codigo_pais' => 'PT',
            'nif' => '999999999',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'valido' => false,
            'mensagem' => 'O NIF indicado não é válido ou não se encontra registado no VIES.',
        ]);
    }

    public function test_retorna_erro_quando_servico_vies_falha(): void
    {
        Http::fake([
            'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/*' => Http::response([], 500),
        ]);

        $user = $this->criarUtilizador();

        $response = $this->actingAs($user)->postJson(route('vies.validar'), [
            'codigo_pais' => 'PT',
            'nif' => '500000000',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'valido' => false,
            'mensagem' => 'O serviço VIES encontra-se indisponível de momento.',
        ]);
    }
}