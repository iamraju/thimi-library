<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';

type Book = {
    id: number;
    title: string;
    slug: string;
    author_name: string | null;
    book_type: 'new' | 'old';
    cover_image_url: string | null;
    status: number;
    category: { title: string };
    publisher: { title: string };
};
const props = defineProps<{
    books: {
        data: Book[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    categories: { id: number; title: string }[];
    filters: { search?: string; category_id?: string };
}>();
const filters = useForm({
    search: props.filters.search ?? '',
    category_id: props.filters.category_id ?? '',
});
const applyFilters = () =>
    filters.get('/books', { preserveState: true, replace: true });
const remove = (id: number) => {
    if (window.confirm('Delete this book?')) router.delete(`/books/${id}`);
};
const exportUrl = () => {
    const params = new URLSearchParams();
    if (props.filters.search) params.set('search', props.filters.search);
    if (props.filters.category_id)
        params.set('category_id', props.filters.category_id);
    const query = params.toString();
    return `/books/export${query ? `?${query}` : ''}`;
};
</script>

<template>
    <Head title="Books" />
    <main class="space-y-6 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-muted-foreground">Library catalog</p>
                <h1 class="mt-1 text-2xl font-semibold">Books</h1>
            </div>
            <Link
                v-if="$page.props.auth.user?.role !== 'reader'"
                href="/books/create"
                class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground"
                >Add book</Link
            >
        </header>
        <form class="grid gap-2 sm:flex" @submit.prevent="applyFilters">
            <input
                v-model="filters.search"
                type="search"
                placeholder="Title or author"
                class="h-9 min-w-0 rounded-md border bg-background px-3 text-sm sm:w-72"
            /><select
                v-model="filters.category_id"
                class="h-9 rounded-md border bg-background px-3 text-sm"
            >
                <option value="">All categories</option>
                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="String(category.id)"
                >
                    {{ category.title }}
                </option></select
            ><button class="h-9 rounded-md border px-4 text-sm font-medium">
                Filter</button
            ><a
                :href="exportUrl()"
                class="inline-flex h-9 items-center rounded-md border px-4 text-sm font-medium hover:bg-accent"
                >Export to Excel</a
            >
        </form>
        <div class="overflow-x-auto border-y">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead
                    class="text-xs tracking-wide text-muted-foreground uppercase"
                >
                    <tr>
                        <th class="py-3 pr-4 font-medium">Book</th>
                        <th class="py-3 pr-4 font-medium">Type</th>
                        <th class="py-3 pr-4 font-medium">Category</th>
                        <th class="py-3 pr-4 font-medium">Publisher</th>
                        <th class="py-3 pr-4 font-medium">Status</th>
                        <th
                            v-if="$page.props.auth.user?.role !== 'reader'"
                            class="py-3 text-right font-medium"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="book in books.data" :key="book.id">
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="book.cover_image_url"
                                    :src="book.cover_image_url"
                                    alt=""
                                    class="h-12 w-9 rounded object-cover"
                                />
                                <div>
                                    <div class="font-medium">
                                        {{ book.title }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{
                                            book.author_name ||
                                            'Author not specified'
                                        }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{ book.book_type === 'old' ? 'Old' : 'New' }}
                        </td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{ book.category.title }}
                        </td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{ book.publisher.title }}
                        </td>
                        <td class="py-3 pr-4">
                            {{ book.status ? 'Active' : 'Inactive' }}
                        </td>
                        <td
                            v-if="$page.props.auth.user?.role !== 'reader'"
                            class="py-3 text-right"
                        >
                            <Link
                                :href="`/books/${book.id}/edit`"
                                class="text-primary hover:underline"
                                >Edit</Link
                            ><button
                                class="ml-4 text-destructive hover:underline"
                                @click="remove(book.id)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="books.data.length === 0">
                        <td
                            :colspan="
                                $page.props.auth.user?.role === 'reader' ? 5 : 6
                            "
                            class="py-10 text-center text-muted-foreground"
                        >
                            No books found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav
            v-if="books.links.length > 3"
            class="flex flex-wrap gap-1"
            aria-label="Pagination"
        >
            <Link
                v-for="link in books.links"
                :key="link.label"
                :href="link.url || '#'"
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
