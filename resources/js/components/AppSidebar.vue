<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Calendar, Globe, LayoutGrid, ListChecks, Percent, Users, UserRoundCog } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Painel Principal',
        href: '/painel',
        icon: LayoutGrid,
    },
];

const accessNavItems: NavItem[] = [
    {
        title: 'Utilizadores',
        href: '/utilizadores',
        icon: Users,
    },
];

const settingsNavItems: NavItem[] = [
    {
        title: 'Países',
        href: '/configuracoes/paises',
        icon: Globe,
    },
    {
        title: 'Funções de Contacto',
        href: '/configuracoes/funcoes-contacto',
        icon: UserRoundCog,
    },
    {
        title: 'Taxas de IVA',
        href: '/configuracoes/taxas-iva',
        icon: Percent,
    },
    {
        title: 'Tipos de Calendário',
        href: '/configuracoes/tipos-calendario',
        icon: Calendar,
    },
    {
        title: 'Ações de Calendário',
        href: '/configuracoes/acoes-calendario',
        icon: ListChecks,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" label="Principal" />
            <NavMain :items="accessNavItems" label="Gestão de Acessos" />
            <NavMain :items="settingsNavItems" label="Configurações" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
