<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'entidades.criar', 'entidades.ler', 'entidades.atualizar', 'entidades.eliminar',
            'contactos.criar', 'contactos.ler', 'contactos.atualizar', 'contactos.eliminar',
            'artigos.criar', 'artigos.ler', 'artigos.atualizar', 'artigos.eliminar',
            'propostas.criar', 'propostas.ler', 'propostas.atualizar', 'propostas.eliminar', 'propostas.converter',
            'encomendas.criar', 'encomendas.ler', 'encomendas.atualizar', 'encomendas.eliminar', 'encomendas.converter',
            'faturas_fornecedores.criar', 'faturas_fornecedores.ler', 'faturas_fornecedores.atualizar', 'faturas_fornecedores.pagar',
            'calendario.criar', 'calendario.ler', 'calendario.atualizar', 'calendario.eliminar',
            'contas_bancarias.ler', 'contas_bancarias.gerir',
            'conta_corrente.ler', 'conta_corrente.gerir',
            'ordens_trabalho.criar', 'ordens_trabalho.ler', 'ordens_trabalho.atualizar', 'ordens_trabalho.eliminar',
            'arquivo_digital.ler', 'arquivo_digital.gerir',
            'utilizadores.criar', 'utilizadores.ler', 'utilizadores.atualizar', 'utilizadores.eliminar',
            'permissoes.gerir',
            'empresa.gerir',
            'logs.ler',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $roleAdmin = Role::create(['name' => 'administrador']);
        $roleAdmin->givePermissionTo(Permission::all());

        $roleComercial = Role::create(['name' => 'gestor_comercial']);
        $roleComercial->givePermissionTo([
            'entidades.criar', 'entidades.ler', 'entidades.atualizar', 'entidades.eliminar',
            'contactos.criar', 'contactos.ler', 'contactos.atualizar', 'contactos.eliminar',
            'artigos.criar', 'artigos.ler', 'artigos.atualizar', 'artigos.eliminar',
            'propostas.criar', 'propostas.ler', 'propostas.atualizar', 'propostas.eliminar', 'propostas.converter',
            'encomendas.criar', 'encomendas.ler', 'encomendas.atualizar', 'encomendas.eliminar', 'encomendas.converter',
            'calendario.criar', 'calendario.ler', 'calendario.atualizar', 'calendario.eliminar',
        ]);

        $roleFinanceiro = Role::create(['name' => 'financeiro']);
        $roleFinanceiro->givePermissionTo([
            'faturas_fornecedores.criar', 'faturas_fornecedores.ler', 'faturas_fornecedores.atualizar', 'faturas_fornecedores.pagar',
            'contas_bancarias.ler', 'contas_bancarias.gerir',
            'conta_corrente.ler', 'conta_corrente.gerir',
            'entidades.ler', 'artigos.ler',
        ]);

        $roleOperacional = Role::create(['name' => 'operacional']);
        $roleOperacional->givePermissionTo([
            'artigos.ler',
            'calendario.criar', 'calendario.ler', 'calendario.atualizar',
            'ordens_trabalho.criar', 'ordens_trabalho.ler', 'ordens_trabalho.atualizar', 'ordens_trabalho.eliminar',
        ]);
    }
}