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

interface EntityOption {
    id: number;
    name: string;
}

interface RoleOption {
    id: number;
    name: string;
}

interface Props {
    contact?: {
        id: number;
        number: number;
        entity_id: number;
        contact_role_id: number | null;
        first_name: string;
        last_name: string;
        phone: string | null;
        mobile: string | null;
        email: string | null;
        rgpd_consent: boolean;
        notes: string | null;
        is_active: boolean;
    };
    entities: EntityOption[];
    roles: RoleOption[];
}

const props = defineProps<Props>();
const isEditing = !!props.contact;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Contactos', href: '/contactos' },
    { title: isEditing ? 'Editar Contacto' : 'Novo Contacto', href: '#' },
];

const form = useForm({
    entity_id: props.contact?.entity_id ?? '',
    contact_role_id: props.contact?.contact_role_id ?? '',
    first_name: props.contact?.first_name ?? '',
    last_name: props.contact?.last_name ?? '',
    phone: props.contact?.phone ?? '',
    mobile: props.contact?.mobile ?? '',
    email: props.contact?.email ?? '',
    rgpd_consent: props.contact?.rgpd_consent ?? false,
    notes: props.contact?.notes ?? '',
    is_active: props.contact?.is_active ?? true,
});

const submit = () => {
    if (isEditing && props.contact) {
        form.put(route('contacts.update', { contact: props.contact.id }));
    } else {
        form.post(route('contacts.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Contacto' : 'Novo Contacto'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card class="max-w-4xl">
                <CardHeader>
                    <CardTitle class="text-xl">{{ isEditing ? 'Editar Contacto' : 'Novo Contacto' }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-if="isEditing && props.contact?.number" class="grid gap-2">
                            <Label for="number">Número</Label>
                            <Input id="number" :model-value="props.contact.number" disabled class="bg-muted cursor-not-allowed" />
                        </div>

                        <div class="grid gap-2" :class="{ 'md:col-span-2': !isEditing }">
                            <Label for="entity_id">Entidade</Label>
                            <select
                                id="entity_id"
                                v-model="form.entity_id"
                                required
                                class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="" disabled>Selecione uma entidade</option>
                                <option v-for="entity in entities" :key="entity.id" :value="entity.id">{{ entity.name }}</option>
                            </select>
                            <InputError :message="form.errors.entity_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="first_name">Nome</Label>
                            <Input id="first_name" v-model="form.first_name" required autofocus />
                            <InputError :message="form.errors.first_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="last_name">Apelido</Label>
                            <Input id="last_name" v-model="form.last_name" required />
                            <InputError :message="form.errors.last_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="contact_role_id">Função</Label>
                            <select
                                id="contact_role_id"
                                v-model="form.contact_role_id"
                                class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="">Sem função definida</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                            <InputError :message="form.errors.contact_role_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Telefone</Label>
                            <Input id="phone" v-model="form.phone" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="mobile">Telemóvel</Label>
                            <Input id="mobile" v-model="form.mobile" />
                            <InputError :message="form.errors.mobile" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Consentimento RGPD</Label>
                            <div class="flex items-center space-x-6 pt-2">
                                <div class="flex items-center space-x-2">
                                    <input
                                        id="rgpd_sim"
                                        type="radio"
                                        :value="true"
                                        v-model="form.rgpd_consent"
                                        class="h-4 w-4 border-input text-primary focus:ring-ring"
                                    />
                                    <Label for="rgpd_sim" class="font-normal cursor-pointer">Sim</Label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input
                                        id="rgpd_nao"
                                        type="radio"
                                        :value="false"
                                        v-model="form.rgpd_consent"
                                        class="h-4 w-4 border-input text-primary focus:ring-ring"
                                    />
                                    <Label for="rgpd_nao" class="font-normal cursor-pointer">Não</Label>
                                </div>
                            </div>
                            <InputError :message="form.errors.rgpd_consent" />
                        </div>

                        <div class="flex items-center space-x-2 pt-6">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" class="font-normal cursor-pointer">Ativo</Label>
                        </div>

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="notes">Observações</Label>
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.notes" />
                        </div>

                        <div class="col-span-full flex justify-end gap-2 border-t pt-4">
                            <Link :href="route('contacts.index')">
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