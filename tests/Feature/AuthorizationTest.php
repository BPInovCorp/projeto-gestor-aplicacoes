<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_administrator_has_all_permissions()
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $this->assertTrue($admin->hasPermissionTo('entidades.criar'));
        $this->assertTrue($admin->hasPermissionTo('faturas_fornecedores.pagar'));
        $this->assertTrue($admin->hasPermissionTo('utilizadores.eliminar'));
    }

    public function test_commercial_user_has_specific_permissions()
    {
        $comercial = User::factory()->create();
        $comercial->assignRole('gestor_comercial');

        $this->assertTrue($comercial->hasPermissionTo('propostas.criar'));
        $this->assertTrue($comercial->hasPermissionTo('entidades.criar'));
        $this->assertFalse($comercial->hasPermissionTo('faturas_fornecedores.pagar'));
        $this->assertFalse($comercial->hasPermissionTo('utilizadores.eliminar'));
    }

    public function test_financial_user_has_specific_permissions()
    {
        $financeiro = User::factory()->create();
        $financeiro->assignRole('financeiro');

        $this->assertTrue($financeiro->hasPermissionTo('faturas_fornecedores.pagar'));
        $this->assertTrue($financeiro->hasPermissionTo('contas_bancarias.gerir'));
        $this->assertFalse($financeiro->hasPermissionTo('utilizadores.eliminar'));
        $this->assertFalse($financeiro->hasPermissionTo('propostas.criar'));
    }
}