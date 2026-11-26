<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Contact {
    id: number;
    number: number;
    first_name: string;
    last_name: string;
    phone: string | null;
    mobile: string | null;
    email: string | null;
    entity?: { id: number; name: string } | null;
    role?: { id: number; name: string } | null;
}

const props = defineProps<{
    contacts: {
        data: Contact[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Contactos', href: '/contactos' },
];

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value: string) => {
        router.get('/contactos', { search: value }, { preserveState: true, replace: true });
    }, 300)
);

const deleteContact = (contact: Contact) => {
    if (confirm(`Tem a certeza que deseja apagar o contacto ${contact.first_name} ${contact.last_name}?`)) {
        router.delete(route('contacts.destroy', { contact: contact.id }), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Contactos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                    <CardTitle class="text-xl">Contactos</CardTitle>
                    <Link :href="route('contacts.create')">
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" /> Novo Contacto
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent class="pt-6">
                    <div class="mb-4 w-full md:w-1/3">
                        <Input v-model="search" placeholder="Pesquisar por nome, email ou entidade..." type="search" />
                    </div>

                    <div class="rounded-md border">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                    <th class="p-4 font-medium">Nome</th>
                                    <th class="p-4 font-medium">Apelido</th>
                                    <th class="p-4 font-medium">Função</th>
                                    <th class="p-4 font-medium">Entidade</th>
                                    <th class="p-4 font-medium">Telefone</th>
                                    <th class="p-4 font-medium">Telemóvel</th>
                                    <th class="p-4 font-medium">Email</th>
                                    <th class="p-4 text-right font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="contact in contacts.data" :key="contact.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4 font-medium">{{ contact.first_name }}</td>
                                    <td class="p-4">{{ contact.last_name }}</td>
                                    <td class="p-4">{{ contact.role?.name || '-' }}</td>
                                    <td class="p-4">{{ contact.entity?.name || '-' }}</td>
                                    <td class="p-4">{{ contact.phone || '-' }}</td>
                                    <td class="p-4">{{ contact.mobile || '-' }}</td>
                                    <td class="p-4">{{ contact.email || '-' }}</td>
                                    <td class="p-4 text-right">
                                        <Link :href="route('contacts.edit', { contact: contact.id })">
                                            <Button variant="ghost" size="icon" title="Editar">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button @click="deleteContact(contact)" variant="ghost" size="icon" class="text-destructive hover:bg-destructive/10" title="Apagar">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="contacts.data.length === 0">
                                    <td colspan="8" class="p-8 text-center text-muted-foreground">Nenhum contacto encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="contacts.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Página {{ contacts.current_page }} de {{ contacts.last_page }}</p>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in contacts.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors hover:bg-muted"
                                    :class="link.active ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'bg-background'"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span v-else class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border bg-muted/50 px-3 text-sm text-muted-foreground" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>