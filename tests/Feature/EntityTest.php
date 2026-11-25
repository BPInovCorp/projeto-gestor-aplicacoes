<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Entity;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityTest extends TestCase
{
    use RefreshDatabase;

    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->country = Country::firstOrCreate(['code' => 'PT'], ['name' => 'Portugal']);
    }

    public function test_admin_can_create_entity_as_client_and_supplier()
    {
        $admin = User::factory()->create();
        assert($admin instanceof User);
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->post(route('entities.store'), [
            'name' => 'Entidade Teste',
            'is_client' => true,
            'is_supplier' => true,
            'nif' => '123456789',
            'country_id' => $this->country->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('entities.index'));
        $this->assertDatabaseHas('entities', ['name' => 'Entidade Teste']);
    }

    public function test_entity_must_be_either_client_or_supplier()
    {
        $admin = User::factory()->create();
        assert($admin instanceof User);
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->post(route('entities.store'), [
            'name' => 'Erro',
            'is_client' => false,
            'is_supplier' => false,
            'country_id' => $this->country->id,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('is_client');
    }
}