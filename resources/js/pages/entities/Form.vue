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
import axios, { isAxiosError } from 'axios';
import { LoaderCircle, Search } from 'lucide-vue-next';
import { ref } from 'vue';

interface Country {
    id: number;
    name: string;
    code: string;
}

interface ViesResponse {
    valido: boolean;
    nome?: string | null;
    morada?: string | null;
    codigo_postal?: string | null;
    localidade?: string | null;
    mensagem?: string;
}

interface ViesMensagem {
    texto: string;
    sucesso: boolean;
}

interface Props {
    entity?: {
        id: number;
        name: string;
        is_client: boolean;
        is_supplier: boolean;
        nif: string | null;
        address: string | null;
        postal_code: string | null;
        city: string | null;
        country_id: number;
        phone: string | null;
        mobile: string | null;
        website: string | null;
        email: string | null;
        notes: string | null;
        is_active: boolean;
    };
    countries: Country[];
}

const props = defineProps<Props>();
const isEditing = !!props.entity;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Entidades', href: '/entidades' },
    { title: isEditing ? 'Editar Entidade' : 'Nova Entidade', href: '#' },
];

const form = useForm({
    name: props.entity?.name ?? '',
    is_client: props.entity?.is_client ?? true,
    is_supplier: props.entity?.is_supplier ?? false,
    nif: props.entity?.nif ?? '',
    address: props.entity?.address ?? '',
    postal_code: props.entity?.postal_code ?? '',
    city: props.entity?.city ?? '',
    country_id: props.entity?.country_id ?? (props.countries.find(c => c.name === 'Portugal')?.id || ''),
    phone: props.entity?.phone ?? '',
    mobile: props.entity?.mobile ?? '',
    website: props.entity?.website ?? '',
    email: props.entity?.email ?? '',
    notes: props.entity?.notes ?? '',
    is_active: props.entity?.is_active ?? true,
});

const isCheckingVies = ref(false);
const viesMensagem = ref<ViesMensagem | null>(null);

const consultarVies = async () => {
    viesMensagem.value = null;

    if (!form.nif) {
        viesMensagem.value = { texto: 'Introduza um NIF antes de consultar.', sucesso: false };
        return;
    }

    const selectedCountry = props.countries.find((c) => c.id === Number(form.country_id));
    if (!selectedCountry?.code) {
        viesMensagem.value = { texto: 'Selecione um país válido.', sucesso: false };
        return;
    }

    isCheckingVies.value = true;

    try {
        const response = await axios.post<ViesResponse>(route('vies.validar'), {
            codigo_pais: selectedCountry.code,
            nif: form.nif,
        });

        if (response.data.valido) {
            if (response.data.nome) {
                form.name = response.data.nome;
            }
            if (response.data.morada) {
                form.address = response.data.morada;
            }
            if (response.data.codigo_postal) {
                form.postal_code = response.data.codigo_postal;
            }
            if (response.data.localidade) {
                form.city = response.data.localidade;
            }
            viesMensagem.value = { texto: response.data.mensagem ?? 'NIF validado com sucesso.', sucesso: true };
        }
    } catch (error: unknown) {
        if (isAxiosError<{ mensagem?: string; message?: string }>(error) && error.response?.data) {
            const texto = error.response.data.mensagem ?? error.response.data.message ?? 'Erro ao consultar o serviço VIES.';
            viesMensagem.value = { texto, sucesso: false };
        } else {
            viesMensagem.value = { texto: 'Erro de comunicação ao validar no VIES.', sucesso: false };
        }
    } finally {
        isCheckingVies.value = false;
    }
};

const submit = () => {
    if (isEditing && props.entity) {
        form.put(route('entities.update', { entity: props.entity.id }));
    } else {
        form.post(route('entities.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Entidade' : 'Nova Entidade'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card class="max-w-4xl">
                <CardHeader>
                    <CardTitle class="text-xl">{{ isEditing ? 'Editar Entidade' : 'Nova Entidade' }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-full grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-2">
                                <Checkbox id="is_client" v-model:checked="form.is_client" />
                                <Label for="is_client" class="font-normal">Cliente</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Checkbox id="is_supplier" v-model:checked="form.is_supplier" />
                                <Label for="is_supplier" class="font-normal">Fornecedor</Label>
                            </div>
                            <InputError :message="form.errors.is_client" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="name">Nome / Razão Social</Label>
                            <Input id="name" v-model="form.name" required autofocus />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nif">NIF</Label>
                            <div class="flex gap-2">
                                <Input id="nif" v-model="form.nif" class="flex-1" />
                                <Button
                                    type="button"
                                    variant="outline"
                                    :disabled="isCheckingVies || !form.nif"
                                    @click="consultarVies"
                                >
                                    <LoaderCircle v-if="isCheckingVies" class="mr-2 h-4 w-4 animate-spin" />
                                    <Search v-else class="mr-2 h-4 w-4" />
                                    VIES
                                </Button>
                            </div>
                            <p v-if="viesMensagem" :class="viesMensagem.sucesso ? 'text-xs text-green-600 dark:text-green-400' : 'text-xs text-destructive'">
                                {{ viesMensagem.texto }}
                            </p>
                            <InputError :message="form.errors.nif" />
                        </div>

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="address">Morada</Label>
                            <Input id="address" v-model="form.address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="postal_code">Código Postal</Label>
                            <Input id="postal_code" v-model="form.postal_code" placeholder="XXXX-XXX" />
                            <InputError :message="form.errors.postal_code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="city">Localidade</Label>
                            <Input id="city" v-model="form.city" />
                            <InputError :message="form.errors.city" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="country_id">País</Label>
                            <select id="country_id" v-model="form.country_id" required class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm">
                                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <InputError :message="form.errors.country_id" />
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

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="website">Website</Label>
                            <Input id="website" v-model="form.website" />
                            <InputError :message="form.errors.website" />
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

                        <div class="flex items-center space-x-2">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" class="font-normal">Ativo</Label>
                        </div>

                        <div class="col-span-full flex justify-end gap-2 border-t pt-4">
                            <Link :href="route('entities.index')">
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