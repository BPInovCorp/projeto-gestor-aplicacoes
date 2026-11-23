<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { LoaderCircle, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface CalendarEventType {
    id: number;
    name: string;
    color: string | null;
    is_active: boolean;
}

const props = defineProps<{
    types: {
        data: CalendarEventType[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Configurações', href: '#' },
    { title: 'Tipos de Calendário', href: '/configuracoes/tipos-calendario' },
];

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    name: '',
    color: '#3B82F6',
    is_active: true as boolean,
});

watch(
    search,
    debounce((value: string) => {
        router.get('/configuracoes/tipos-calendario', { search: value }, { preserveState: true, replace: true });
    }, 300)
);

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (type: CalendarEventType) => {
    editingId.value = type.id;
    form.name = type.name;
    form.color = type.color || '#3B82F6';
    form.is_active = Boolean(type.is_active);
    form.clearErrors();
    isModalOpen.value = true;
};

const deleteType = (type: CalendarEventType) => {
    if (confirm(`Tem a certeza que deseja apagar o tipo ${type.name}?`)) {
        router.delete(route('settings.calendar-event-types.destroy', { calendar_event_type: type.id }), { preserveScroll: true });
    }
};

const submit = () => {
    if (editingId.value) {
        form.put(route('settings.calendar-event-types.update', { calendar_event_type: editingId.value }), {
            onSuccess: () => { isModalOpen.value = false; },
        });
    } else {
        form.post(route('settings.calendar-event-types.store'), {
            onSuccess: () => { isModalOpen.value = false; },
        });
    }
};
</script>

<template>
    <Head title="Tipos de Calendário" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                    <CardTitle class="text-xl">Tipos de Calendário</CardTitle>
                    <Button @click="openCreateModal" class="gap-2">
                        <Plus class="h-4 w-4" /> Novo Tipo
                    </Button>
                </CardHeader>
                <CardContent class="pt-6">
                    <div class="mb-4 w-full md:w-1/3">
                        <Input v-model="search" placeholder="Pesquisar por nome..." type="search" />
                    </div>

                    <div class="rounded-md border">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                    <th class="p-4 font-medium">Nome</th>
                                    <th class="p-4 font-medium">Cor</th>
                                    <th class="p-4 font-medium">Estado</th>
                                    <th class="p-4 text-right font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="type in types.data" :key="type.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">{{ type.name }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <span v-if="type.color" class="h-4 w-4 rounded-full border border-black/10" :style="{ backgroundColor: type.color }"></span>
                                            <span class="font-mono text-muted-foreground">{{ type.color || '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span v-if="type.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Ativo
                                        </span>
                                        <span v-else class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Inativo
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <Button @click="openEditModal(type)" variant="ghost" size="icon" title="Editar">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button @click="deleteType(type)" variant="ghost" size="icon" class="text-destructive hover:bg-destructive/10" title="Apagar">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="types.data.length === 0">
                                    <td colspan="4" class="p-8 text-center text-muted-foreground">Nenhum tipo de evento encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="types.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Página {{ types.current_page }} de {{ types.last_page }}</p>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in types.links" :key="key">
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

    <Dialog v-model:open="isModalOpen">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ editingId ? 'Editar Tipo de Evento' : 'Novo Tipo de Evento' }}</DialogTitle>
                <DialogDescription>
                    {{ editingId ? 'Altere os dados do tipo de evento abaixo.' : 'Preencha os dados para adicionar um novo tipo.' }}
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Nome do Tipo</Label>
                    <Input id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="color">Cor (HEX)</Label>
                    <div class="flex gap-2">
                        <Input id="colorPicker" type="color" v-model="form.color" class="w-12 p-1 h-10" />
                        <Input id="colorText" type="text" v-model="form.color" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$" class="flex-1 font-mono uppercase" placeholder="#000000" />
                    </div>
                    <InputError :message="form.errors.color" />
                </div>
                <div class="flex items-center space-x-2 pt-2">
                    <Checkbox id="is_active" v-model:checked="form.is_active" />
                    <Label for="is_active" class="font-normal">Ativo</Label>
                </div>
                <InputError :message="form.errors.is_active" />

                <div class="mt-4 flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="isModalOpen = false">Cancelar</Button>
                    <Button type="submit" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Guardar
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>