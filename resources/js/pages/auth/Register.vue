<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    last_name: '',
    phone_number: '',
    document_type: 'CC',
    document_number: ''

});

const documentTypes = ref([
    { value: 'CC', text: 'Cédula de Ciudadanía' },
    { value: 'TI', text: 'Tarjeta de Identidad' },
    { value: 'CE', text: 'Cédula de Extranjería' },
    { value: 'PS', text: 'Pasaporte' },
])

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Crear una cuenta" description="Llene el siguiente formulario para crear su cuenta.">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid grid-cols-2 items-center gap-4">
                    <Label for="name">Nombres</Label>
                    <Label for="last_name">Apellidos</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Nombres Completos" />
                    <Input id="last_name" type="text" required autofocus :tabindex="1" autocomplete="last_name" v-model="form.last_name" placeholder="Apellidos  Completos" />
                    <InputError :message="form.errors.name" />
                    <InputError :message="form.errors.last_name" />
                </div>
                <div class="grid grid-cols-2 items-center gap-4">
                    <Label for="name">Tipo de Documento</Label>
                    <Label for="last_name">Número de Documento</Label>
                    <select
                        id="document-type"
                        required autofocus :tabindex="1"
                        v-model="form.document_type"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 sm:text-sm p-2"
                    >
                        <option
                        v-for="type in documentTypes"
                        :key="type.value"
                        :value="type.value"
                        >
                        {{ type.text }}
                        </option>
                    </select>
                    <Input id="document_number" type="text" required autofocus :tabindex="1" autocomplete="document_number" v-model="form.document_number" placeholder="Número de Documento" />
                    <InputError :message="form.errors.document_type" />
                    <InputError :message="form.errors.document_number" />
                </div>

                <div class="grid grid-cols-2 items-center gap-4">
                    <Label for="email">Email</Label>
                    <Label for="phone_number">Numero de Celular</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@ejemplo.com" />
                    <Input id="phone_number" type="text" required :tabindex="2" autocomplete="phone_number" v-model="form.phone_number" placeholder="Número de Celular" />
                    <InputError :message="form.errors.email" />
                    <InputError :message="form.errors.phone_number" />
                </div>

                <div class="grid grid-cols-2 items-center gap-4">
                    <Label for="password">Password</Label>
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Crear Cuenta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Ya tienes unacuenta?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Iniciar Sesión</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
