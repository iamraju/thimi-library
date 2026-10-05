<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";

const props = defineProps<{
    category?: {
        id: number;
        title: string;
        description: string | null;
        status: number;
    };
}>();
const form = useForm({
    title: props.category?.title ?? "",
    description: props.category?.description ?? "",
    status: props.category?.status ?? 1,
});
const submit = () =>
    props.category
        ? form.put(`/categories/${props.category.id}`)
        : form.post("/categories");
</script>

<template>
    <Head :title="category ? $t('Edit category') : $t('New category')" />
    <main class="max-w-2xl space-y-6 p-4 md:p-6">
        <header>
            <Link
                href="/categories"
                class="text-sm text-muted-foreground hover:text-foreground"
                >{{ $t("Categories") }}</Link
            >
            <h1 class="mt-2 text-2xl font-semibold">
                {{ category ? $t("Edit category") : $t("Add category") }}
            </h1>
        </header>
        <form class="grid gap-5" @submit.prevent="submit">
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Title")
                }}<input
                    v-model="form.title"
                    required
                    maxlength="255"
                    class="h-10 rounded-md border bg-background px-3 font-normal" /><InputError
                    :message="form.errors.title"
            /></label>
            <label class="grid gap-2 text-sm font-medium"
                >{{ $t("Description")
                }}<textarea
                    v-model="form.description"
                    rows="4"
                    class="rounded-md border bg-background px-3 py-2 font-normal"
                ></textarea
                ><InputError :message="form.errors.description"
            /></label>
            <label class="grid max-w-xs gap-2 text-sm font-medium"
                >{{ $t("Status")
                }}<select
                    v-model.number="form.status"
                    class="h-10 rounded-md border bg-background px-3 font-normal"
                >
                    <option :value="1">{{ $t("Active") }}</option>
                    <option :value="0">{{ $t("Inactive") }}</option></select
                ><InputError :message="form.errors.status"
            /></label>
            <div class="flex gap-3">
                <Button :disabled="form.processing">{{
                    form.processing ? $t("Saving…") : $t("Save category")
                }}</Button
                ><Button as-child variant="outline"
                    ><Link href="/categories">{{ $t("Cancel") }}</Link></Button
                >
            </div>
        </form>
    </main>
</template>
