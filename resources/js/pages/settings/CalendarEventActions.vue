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

interface CalendarEventAction {
    id: number;
    name: string;
    is_active: boolean;
}

const props = defineProps<{
    actions: {
        data: CalendarEventAction[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Configurações', href: '#' },
    { title: 'Ações de Calendário', href: '/configuracoes/acoes-calendario' },
];

const search = ref(props.filters.search || '');
const isModalOpen = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    name: '',
    is_active: true as boolean,
});

watch(
    search,
    debounce((value: string) => {
        router.get('/configuracoes/acoes-calendario', { search: value }, { preserveState: true, replace: true });
    }, 300)
);

const openCreateModal = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (actionItem: CalendarEventAction) => {
    editingId.value = actionItem.id;
    form.name = actionItem.name;
    form.is_active = Boolean(actionItem.is_active);
    form.clearErrors();
    isModalOpen.value = true;
};

const deleteAction = (actionItem: CalendarEventAction) => {
    if (confirm(`Tem a certeza que deseja apagar a ação ${actionItem.name}?`)) {
        router.delete(route('settings.calendar-event-actions.destroy', { calendar_event_action: actionItem.id }), { preserveScroll: true });
    }
};

const submit = () => {
    if (editingId.value) {
        form.put(route('settings.calendar-event-actions.update', { calendar_event_action: editingId.value }), {
            onSuccess: () => { isModalOpen.value = false; },
        });
    } else {
        form.post(route('settings.calendar-event-actions.store'), {
            onSuccess: () => { isModalOpen.value = false; },
        });
    }
};
</script>

<template>
    <Head title="Ações de Calendário" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                    <CardTitle class="text-xl">Ações de Calendário</CardTitle>
                    <Button @click="openCreateModal" class="gap-2">
                        <Plus class="h-4 w-4" /> Nova Ação
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
                                    <th class="p-4 font-medium">Estado</th>
                                    <th class="p-4 text-right font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="actionItem in actions.data" :key="actionItem.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">{{ actionItem.name }}</td>
                                    <td class="p-4">
                                        <span v-if="actionItem.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Ativo
                                        </span>
                                        <span v-else class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Inativo
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <Button @click="openEditModal(actionItem)" variant="ghost" size="icon" title="Editar">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button @click="deleteAction(actionItem)" variant="ghost" size="icon" class="text-destructive hover:bg-destructive/10" title="Apagar">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="actions.data.length === 0">
                                    <td colspan="3" class="p-8 text-center text-muted-foreground">Nenhuma ação de evento encontrada.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="actions.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Página {{ actions.current_page }} de {{ actions.last_page }}</p>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in actions.links" :key="key">
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
                <DialogTitle>{{ editingId ? 'Editar Ação' : 'Nova Ação' }}</DialogTitle>
                <DialogDescription>
                    {{ editingId ? 'Altere os dados da ação abaixo.' : 'Preencha os dados para adicionar uma nova ação.' }}
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Nome da Ação</Label>
                    <Input id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
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