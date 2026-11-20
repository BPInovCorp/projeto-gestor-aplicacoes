<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    user?: {
        id: number;
        name: string;
        email: string;
        mobile: string | null;
        is_active: boolean;
        role: string | null;
    };
    roles: { id: number; name: string }[];
}

const props = defineProps<Props>();

const isEditing = !!props.user;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel Principal', href: '/painel' },
    { title: 'Utilizadores', href: '/utilizadores' },
    { title: isEditing ? 'Editar Utilizador' : 'Novo Utilizador', href: '#' },
];

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    mobile: props.user?.mobile ?? '',
    role: props.user?.role ?? '',
    is_active: props.user?.is_active ?? true,
});

const submit = () => {
    if (isEditing && props.user) {
        form.put(route('utilizadores.update', { user: props.user.id }));
    } else {
        form.post(route('utilizadores.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Utilizador' : 'Novo Utilizador'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle class="text-xl">{{ isEditing ? 'Editar Utilizador' : 'Novo Utilizador' }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Nome Completo</Label>
                            <Input id="name" type="text" v-model="form.name" required autofocus />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" required />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="mobile">Telemóvel</Label>
                            <Input id="mobile" type="text" v-model="form.mobile" />
                            <InputError :message="form.errors.mobile" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Grupo de Permissões</Label>
                            <select
                                id="role"
                                v-model="form.role"
                                required
                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled selected>Selecione um grupo...</option>
                                <option v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" class="font-normal">Conta Ativa</Label>
                        </div>
                        <InputError :message="form.errors.is_active" />

                        <div class="mt-4 flex justify-end gap-2 border-t pt-4">
                            <Link :href="route('utilizadores.index')">
                                <Button variant="outline" type="button">Cancelar</Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                                Guardar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>