<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_unauthorized_user_cannot_access_user_management()
    {
        $user = User::factory()->create();
        assert($user instanceof User);
        
        $user->assignRole('operacional');

        $response = $this->actingAs($user)->get(route('utilizadores.index'));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_user_management()
    {
        $admin = User::factory()->create();
        assert($admin instanceof User);
        
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->get(route('utilizadores.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_new_user()
    {
        $admin = User::factory()->create();
        assert($admin instanceof User);
        
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->post(route('utilizadores.store'), [
            'name' => 'Novo Utilizador',
            'email' => 'novo@gestao.test',
            'mobile' => '912345678',
            'role' => 'gestor_comercial',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('utilizadores.index'));
        $this->assertDatabaseHas('users', ['email' => 'novo@gestao.test']);
        
        $newUser = User::where('email', 'novo@gestao.test')->first();
        assert($newUser instanceof User);
        
        $this->assertTrue($newUser->hasRole('gestor_comercial'));
    }

    public function test_admin_cannot_deactivate_own_account()
    {
        $admin = User::factory()->create();
        assert($admin instanceof User);
        
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->put(route('utilizadores.update', ['user' => $admin->id]), [
            'name' => 'Admin Atualizado',
            'email' => $admin->email,
            'mobile' => '912345678',
            'role' => 'administrador',
            'is_active' => false,
        ]);

        $response->assertSessionHasErrors('is_active');
        $this->assertTrue((bool) $admin->fresh()->is_active);
    }
}