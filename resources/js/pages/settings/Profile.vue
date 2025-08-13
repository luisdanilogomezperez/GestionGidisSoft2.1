<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
    last_name: user.last_name,
    document_type: user.document_type,
    document_number: user.document_number,
    phone_number: user.phone_number,
    is_enable: user.is_enable
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 items-center gap-4">
                        <Label for="name">Name</Label>
                        <Label for="last_name">Last Name</Label>
                        <Input id="name" v-model="form.name" required autocomplete="name" placeholder="Name" />
                        <InputError class="col-span-2 text-red-500" :message="form.errors.name" />
                        <Input id="last_name" v-model="form.last_name" required autocomplete="last_name" placeholder="Last name" />
                        <InputError class="col-span-2 text-red-500" :message="form.errors.last_name" />
                    </div>


                    <div class="grid grid-cols-2 items-center gap-4">
                        <Label for="document_type">Document Type</Label>
                        <Label for="document_number">Document Number</Label>
                        <Input id="document_type" class="mt-1 block w-full" v-model="form.document_type" required autocomplete="document type" placeholder="Document type" />
                        <InputError class="mt-2" :message="form.errors.document_type" />
                        <Input id="document_number" class="mt-1 block w-full" v-model="form.document_number" required autocomplete="document number" placeholder="Document number" />
                        <InputError class="mt-2" :message="form.errors.document_number" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
