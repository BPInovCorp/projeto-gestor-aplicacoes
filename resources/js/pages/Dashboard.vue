<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel Principal',
        href: '/painel',
    },
];
</script>

<template>
    <Head title="Painel Principal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <div class="flex flex-col gap-1">
                <h1 class="text-2xl font-semibold tracking-tight">Painel Principal</h1>
                <p class="text-sm text-muted-foreground">
                    Bem-vindo ao Gestor de Aplicações. A estrutura base está preparada para a integração dos módulos de gestão.
                </p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Segurança e Acessos</CardTitle>
                        <CardDescription>Autenticação, 2FA e Permissões</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Comunicação obrigatória por HTTPS, sessão cifrada e controlo modular preparado.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Ciclo Comercial</CardTitle>
                        <CardDescription>Entidades, Propostas e Encomendas</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Arquitetura desenhada para ligar clientes, artigos, propostas, encomendas e faturação.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Armazenamento Privado</CardTitle>
                        <CardDescription>Documentação fora da pasta pública</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            Anexos e ficheiros confidenciais isolados em disco privado com acesso autenticado.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
