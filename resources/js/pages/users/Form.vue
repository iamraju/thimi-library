<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    user?: {
        id: number;
        name: string;
        email: string;
        role: 'superadmin' | 'librarian' | 'reader';
    };
}>();
const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    role: props.user?.role ?? 'reader',
    password: '',
    password_confirmation: '',
});
const submit = () =>
    props.user ? form.put(`/users/${props.user.id}`) : form.post('/users');
</script>

<template>
    <Head :title="user ? 'Edit user' : 'New user'" />
    <main class="max-w-2xl space-y-6 p-4 md:p-6">
        <header>
            <Link
                href="/users"
                class="text-sm text-muted-foreground hover:text-foreground"
                >Users</Link
            >
            <h1 class="mt-2 text-2xl font-semibold">
                {{ user ? 'Edit user' : 'Add user' }}
            </h1>
        </header>
        <form class="grid gap-5 sm:grid-cols-2" @submit.prevent="submit">
            <label class="grid gap-2 text-sm font-medium"
                >Full name<input
                    v-model="form.name"
                    required
                    maxlength="255"
                    autocomplete="name"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.name"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Email<input
                    v-model="form.email"
                    required
                    type="email"
                    autocomplete="email"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.email"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Role<select
                    v-model="form.role"
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option value="reader">Reader</option>
                    <option value="librarian">Librarian</option>
                    <option value="superadmin">Superadmin</option></select
                ><InputError :message="form.errors.role"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ user ? 'New password (optional)' : 'Password'
                }}<PasswordInput
                    v-model="form.password"
                    :required="!user"
                    autocomplete="new-password"
                    class="font-normal" /><InputError
                    :message="form.errors.password"
            /></label>
            <label class="grid gap-2 text-sm font-medium sm:col-span-2"
                >Confirm password<PasswordInput
                    v-model="form.password_confirmation"
                    :required="!user"
                    autocomplete="new-password"
                    class="font-normal" /><InputError
                    :message="form.errors.password_confirmation"
            /></label>
            <div class="flex gap-3 sm:col-span-2">
                <Button :disabled="form.processing">{{
                    form.processing ? 'Saving…' : 'Save user'
                }}</Button
                ><Button as-child variant="outline"
                    ><Link href="/users">Cancel</Link></Button
                >
            </div>
        </form>
    </main>
</template>
