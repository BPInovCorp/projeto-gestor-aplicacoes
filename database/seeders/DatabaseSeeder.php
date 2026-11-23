<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ReferenceDataSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@gestao.test'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
            ]
        );
        
        $admin->assignRole('administrador');
    }
}