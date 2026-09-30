<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    publisher?: {
        id: number;
        title: string;
        address: string | null;
        telephone: string | null;
        mobile: string | null;
        contact_person: string | null;
        status: number;
    };
}>();
const form = useForm({
    title: props.publisher?.title ?? '',
    address: props.publisher?.address ?? '',
    telephone: props.publisher?.telephone ?? '',
    mobile: props.publisher?.mobile ?? '',
    contact_person: props.publisher?.contact_person ?? '',
    status: props.publisher?.status ?? 1,
});
const submit = () =>
    props.publisher
        ? form.put(`/publishers/${props.publisher.id}`)
        : form.post('/publishers');
</script>

<template>
    <Head :title="publisher ? 'Edit publisher' : 'New publisher'" />
    <main class="max-w-3xl space-y-6 p-4 md:p-6">
        <header>
            <Link
                href="/publishers"
                class="text-sm text-muted-foreground hover:text-foreground"
                >Publishers</Link
            >
            <h1 class="mt-2 text-2xl font-semibold">
                {{ publisher ? 'Edit publisher' : 'Add publisher' }}
            </h1>
        </header>
        <form class="grid gap-5 sm:grid-cols-2" @submit.prevent="submit">
            <label class="grid gap-2 text-sm font-medium sm:col-span-2"
                >Title<input
                    v-model="form.title"
                    required
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.title"
            /></label>
            <label class="grid gap-2 text-sm font-medium sm:col-span-2"
                >Address<input
                    v-model="form.address"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.address"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Telephone<input
                    v-model="form.telephone"
                    maxlength="40"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.telephone"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Mobile<input
                    v-model="form.mobile"
                    maxlength="40"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.mobile"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Contact person<input
                    v-model="form.contact_person"
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.contact_person"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >Status<select
                    v-model.number="form.status"
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option :value="1">Active</option>
                    <option :value="0">Inactive</option></select
                ><InputError :message="form.errors.status"
            /></label>
            <div class="flex gap-3 sm:col-span-2">
                <Button :disabled="form.processing">{{
                    form.processing ? 'Saving…' : 'Save publisher'
                }}</Button
                ><Button as-child variant="outline"
                    ><Link href="/publishers">Cancel</Link></Button
                >
            </div>
        </form>
    </main>
</template>
