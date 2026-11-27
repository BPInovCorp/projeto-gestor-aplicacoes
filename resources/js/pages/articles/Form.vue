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
import { LoaderCircle, Package, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

interface VatRate {
    id: number;
    name: string;
    value: string | number;
}

interface Article {
    id: number;
    reference: string;
    name: string;
    description: string | null;
    price: string | number;
    vat_rate_id: number;
    photo_url: string | null;
    notes: string | null;
    is_active: boolean;
}

interface Props {
    article?: Article;
    vatRates: VatRate[];
}

const props = defineProps<Props>();
const isEditing = !!props.article;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Artigos', href: '/artigos' },
    { title: isEditing ? 'Editar Artigo' : 'Novo Artigo', href: '#' },
];

const photoPreview = ref<string | null>(props.article?.photo_url ?? null);

const form = useForm<{
    reference: string;
    name: string;
    description: string;
    price: string | number;
    vat_rate_id: number | string;
    photo: File | null;
    notes: string;
    is_active: boolean;
    _method?: string;
}>({
    reference: props.article?.reference ?? '',
    name: props.article?.name ?? '',
    description: props.article?.description ?? '',
    price: props.article?.price ?? '',
    vat_rate_id: props.article?.vat_rate_id ?? (props.vatRates[0]?.id || ''),
    photo: null,
    notes: props.article?.notes ?? '',
    is_active: props.article?.is_active ?? true,
    _method: isEditing ? 'PUT' : undefined,
});

const onPhotoChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.photo = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (isEditing && props.article) {
        form.post(route('articles.update', { article: props.article.id }), {
            forceFormData: true,
        });
    } else {
        form.post(route('articles.store'), {
            forceFormData: true,
        });
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Artigo' : 'Novo Artigo'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card class="max-w-4xl">
                <CardHeader>
                    <CardTitle class="text-xl">{{ isEditing ? 'Editar Artigo' : 'Novo Artigo' }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="grid gap-2">
                            <Label for="reference">Referência</Label>
                            <Input id="reference" v-model="form.reference" required autofocus />
                            <InputError :message="form.errors.reference" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="name">Nome do Artigo</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="price">Preço (€)</Label>
                            <Input id="price" type="number" step="0.01" min="0" v-model="form.price" required />
                            <InputError :message="form.errors.price" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vat_rate_id">Taxa de IVA</Label>
                            <select
                                id="vat_rate_id"
                                v-model="form.vat_rate_id"
                                required
                                class="flex h-10 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="" disabled>Selecione uma taxa de IVA</option>
                                <option v-for="rate in vatRates" :key="rate.id" :value="rate.id">
                                    {{ rate.name }} ({{ Number(rate.value) }}%)
                                </option>
                            </select>
                            <InputError :message="form.errors.vat_rate_id" />
                        </div>

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="description">Descrição</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-4 md:col-span-2 border rounded-lg p-4 bg-muted/20">
                            <Label>Fotografia do Artigo</Label>
                            <div class="flex items-center gap-6">
                                <div class="h-24 w-24 rounded-lg border bg-muted flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="photoPreview" :src="photoPreview" alt="Pré-visualização" class="h-full w-full object-cover" />
                                    <Package v-else class="h-10 w-10 text-muted-foreground" />
                                </div>
                                <div class="grid gap-2 flex-1">
                                    <div class="flex items-center gap-2">
                                        <label
                                            for="photo"
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 cursor-pointer gap-2"
                                        >
                                            <Upload class="h-4 w-4" />
                                            <span>Selecionar Imagem</span>
                                        </label>
                                        <input id="photo" type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
                                        <span v-if="form.photo" class="text-xs text-muted-foreground">{{ form.photo.name }}</span>
                                    </div>
                                    <p class="text-xs text-muted-foreground">Formatos suportados: JPEG, PNG, JPG ou WEBP (máx. 2MB). Armazenamento protegido.</p>
                                    <InputError :message="form.errors.photo" />
                                </div>
                            </div>
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

                        <div class="flex items-center space-x-2 pt-2">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" class="font-normal cursor-pointer">Ativo</Label>
                        </div>

                        <div class="col-span-full flex justify-end gap-2 border-t pt-4">
                            <Link :href="route('articles.index')">
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