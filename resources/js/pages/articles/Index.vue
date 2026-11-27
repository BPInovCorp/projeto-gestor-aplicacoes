<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Package, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
    photo_url: string | null;
    vat_rate?: VatRate | null;
}

const props = defineProps<{
    articles: {
        data: Article[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Artigos', href: '/artigos' },
];

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value: string) => {
        router.get('/artigos', { search: value }, { preserveState: true, replace: true });
    }, 300)
);

const deleteArticle = (article: Article) => {
    if (confirm(`Tem a certeza que deseja apagar o artigo ${article.reference} - ${article.name}?`)) {
        router.delete(route('articles.destroy', { article: article.id }), { preserveScroll: true });
    }
};

const formatCurrency = (value: string | number) => {
    return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(Number(value));
};
</script>

<template>
    <Head title="Artigos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4">
                    <CardTitle class="text-xl">Artigos</CardTitle>
                    <Link :href="route('articles.create')">
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" /> Novo Artigo
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent class="pt-6">
                    <div class="mb-4 w-full md:w-1/3">
                        <Input v-model="search" placeholder="Pesquisar por referência, nome ou descrição..." type="search" />
                    </div>

                    <div class="rounded-md border">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-muted/50 text-left text-muted-foreground">
                                    <th class="p-4 font-medium">Foto</th>
                                    <th class="p-4 font-medium">Referência</th>
                                    <th class="p-4 font-medium">Nome</th>
                                    <th class="p-4 font-medium">Descrição</th>
                                    <th class="p-4 font-medium">Preço</th>
                                    <th class="p-4 text-right font-medium">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="article in articles.data" :key="article.id" class="border-b last:border-0 hover:bg-muted/50">
                                    <td class="p-4">
                                        <img
                                            v-if="article.photo_url"
                                            :src="article.photo_url"
                                            :alt="article.name"
                                            class="h-10 w-10 rounded object-cover border"
                                        />
                                        <div v-else class="flex h-10 w-10 items-center justify-center rounded bg-muted text-muted-foreground">
                                            <Package class="h-5 w-5" />
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono font-medium">{{ article.reference }}</td>
                                    <td class="p-4 font-medium">{{ article.name }}</td>
                                    <td class="p-4 text-muted-foreground max-w-xs truncate">{{ article.description || '-' }}</td>
                                    <td class="p-4 font-medium">{{ formatCurrency(article.price) }}</td>
                                    <td class="p-4 text-right">
                                        <Link :href="route('articles.edit', { article: article.id })">
                                            <Button variant="ghost" size="icon" title="Editar">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button @click="deleteArticle(article)" variant="ghost" size="icon" class="text-destructive hover:bg-destructive/10" title="Apagar">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="articles.data.length === 0">
                                    <td colspan="6" class="p-8 text-center text-muted-foreground">Nenhum artigo encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="articles.last_page > 1" class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">Página {{ articles.current_page }} de {{ articles.last_page }}</p>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in articles.links" :key="key">
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