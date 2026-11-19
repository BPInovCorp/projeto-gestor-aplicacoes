<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { nextTick, ref } from 'vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const codeInput = ref<HTMLInputElement | null>(null);
const recoveryCodeInput = ref<HTMLInputElement | null>(null);

const toggleRecovery = async () => {
    recovery.value = !recovery.value;
    await nextTick();
    if (recovery.value) {
        recoveryCodeInput.value?.focus();
        form.code = '';
    } else {
        codeInput.value?.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <AuthBase title="Autenticação de Dois Fatores" description="Introduza o código de autenticação gerado pela sua aplicação ou um código de recuperação">
        <Head title="Confirmação 2FA" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div v-if="!recovery" class="grid gap-2">
                    <Label for="code">Código de Autenticação</Label>
                    <Input
                        id="code"
                        ref="codeInput"
                        type="text"
                        inputmode="numeric"
                        autofocus
                        autocomplete="one-time-code"
                        v-model="form.code"
                        placeholder="000000"
                    />
                    <InputError :message="form.errors.code" />
                </div>

                <div v-else class="grid gap-2">
                    <Label for="recovery_code">Código de Recuperação</Label>
                    <Input
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        type="text"
                        autocomplete="one-time-code"
                        v-model="form.recovery_code"
                        placeholder="xxxxxxxx-xxxxxxxx"
                    />
                    <InputError :message="form.errors.recovery_code" />
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="button"
                        class="text-sm text-muted-foreground underline cursor-pointer"
                        @click="toggleRecovery"
                    >
                        <span v-if="!recovery">Utilizar um código de recuperação</span>
                        <span v-else>Utilizar um código de autenticação</span>
                    </button>
                </div>

                <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Continuar
                </Button>
            </div>
        </form>
    </AuthBase>
</template>