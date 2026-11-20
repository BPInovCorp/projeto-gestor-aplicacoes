<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

interface User {
    id: number;
    name: string;
    email: string;
    mobile: string | null;
    is_active: boolean;
    role: string | null;
}

const props = defineProps<{
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel Principal', href: '/painel' },
    { title: 'Utilizadores', href: '/utilizadores' },
];

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value: string) => {
        router.get(
            '/utilizadores',
            { search: value },
            { preserveState: true, replace: true }
        );
    }, 300)
);
</script>

<template>
    <Head title="Utilizadores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                    <CardTitle class="text-xl">Utilizadores</CardTitle>
                    <Link :href="route('utilizadores.create')">
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" /> Novo Utilizador
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent class="pt-6">
                    <div class="mb-4 w-full md:w-1/3">
                        <Input v-model="search" placeholder="Pesquisar por nome ou email..." type="search" />
                    </div>

                    <div class="rounded-md border">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                    <th class="p-4 font-medium">Nome</th>
                                    <th class="p-4 font-medium">Email</th>
                                    <th class="p-4 font-medium">Telemóvel</th>
                                    <th class="p-4 font-medium">Grupo</th>
                                    <th class="p-4 font-medium">Estado</th>
                                    <th class="p-4 text-right font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">{{ user.name }}</td>
                                    <td class="p-4">{{ user.email }}</td>
                                    <td class="p-4">{{ user.mobile || '-' }}</td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            {{ user.role || 'Sem Grupo' }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span v-if="user.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Ativo
                                        </span>
                                        <span v-else class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Inativo
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <Link :href="route('utilizadores.edit', { user: user.id })">
                                            <Button variant="ghost" size="icon" title="Editar">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">Nenhum utilizador encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="users.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            Página {{ users.current_page }} de {{ users.last_page }}
                        </p>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in users.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors hover:bg-muted"
                                    :class="link.active ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'bg-background'"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border bg-muted/50 px-3 text-sm text-muted-foreground"
                                    v-html="link.label"
                                ></span>
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>