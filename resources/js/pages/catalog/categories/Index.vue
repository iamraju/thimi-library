<script setup lang="ts">
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { t } from "@/composables/useI18n";

type Category = {
    id: number;
    title: string;
    description: string | null;
    status: number;
    books_count: number;
};
const props = defineProps<{
    categories: {
        data: Category[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();
const search = useForm({ search: props.filters.search ?? "" });
const submitSearch = () =>
    search.get("/categories", { preserveState: true, replace: true });
const remove = (id: number) => {
    if (window.confirm(t("Delete this category?")))
        router.delete(`/categories/${id}`);
};
</script>

<template>
    <Head :title="$t('Categories')" />
    <main class="space-y-6 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-muted-foreground">
                    {{ $t("Catalog setup") }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold">
                    {{ $t("Categories") }}
                </h1>
            </div>
            <Link
                href="/categories/create"
                class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground"
                >{{ $t("Add category") }}</Link
            >
        </header>
        <form class="flex max-w-lg gap-2" @submit.prevent="submitSearch">
            <input
                v-model="search.search"
                type="search"
                :placeholder="$t('Find a category')"
                class="h-9 min-w-0 flex-1 rounded-md border bg-background px-3 text-sm"
            /><button class="h-9 rounded-md border px-4 text-sm font-medium">
                {{ $t("Search") }}
            </button>
        </form>
        <div class="overflow-x-auto border-y">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead
                    class="text-xs tracking-wide text-muted-foreground uppercase"
                >
                    <tr>
                        <th class="py-3 pr-4 font-medium">{{ $t("Title") }}</th>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t("Description") }}
                        </th>
                        <th class="py-3 pr-4 font-medium">{{ $t("Books") }}</th>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t("Status") }}
                        </th>
                        <th class="py-3 text-right font-medium">
                            {{ $t("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="category in categories.data" :key="category.id">
                        <td class="py-3 pr-4 font-medium">
                            {{ category.title }}
                        </td>
                        <td
                            class="max-w-md truncate py-3 pr-4 text-muted-foreground"
                        >
                            {{ category.description || "—" }}
                        </td>
                        <td class="py-3 pr-4 tabular-nums">
                            {{ category.books_count }}
                        </td>
                        <td class="py-3 pr-4">
                            {{
                                category.status ? $t("Active") : $t("Inactive")
                            }}
                        </td>
                        <td class="py-3 text-right">
                            <Link
                                :href="`/categories/${category.id}/edit`"
                                class="text-primary hover:underline"
                                >{{ $t("Edit") }}</Link
                            ><button
                                class="ml-4 text-destructive hover:underline"
                                @click="remove(category.id)"
                            >
                                {{ $t("Delete") }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="categories.data.length === 0">
                        <td
                            colspan="5"
                            class="py-10 text-center text-muted-foreground"
                        >
                            {{ $t("No categories found.") }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav
            v-if="categories.links.length > 3"
            class="flex flex-wrap gap-1"
            aria-label="Pagination"
        >
            <Link
                v-for="link in categories.links"
                :key="link.label"
                :href="link.url || '# '"
                :only="['categories', 'filters']"
                preserve-scroll
                class="rounded border px-3 py-1.5 text-sm"
                :class="
                    link.active
                        ? 'bg-primary text-primary-foreground'
                        : 'text-muted-foreground'
                "
                v-html="link.label"
            />
        </nav>
    </main>
</template>
