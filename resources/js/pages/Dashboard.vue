<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Building2, Tags, Users } from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

defineProps<{
    role: 'superadmin' | 'librarian' | 'reader';
    stats: {
        books: number;
        active_books: number;
        categories: number;
        publishers: number;
        users: number | null;
    };
    recentBooks: {
        id: number;
        title: string;
        status: number;
        category: { title: string };
        publisher: { title: string };
    }[];
}>();
</script>

<template>
    <Head :title="$t('Dashboard')" />

    <div class="space-y-8 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-muted-foreground capitalize">
                    {{ $t(role) }} {{ $t('workspace') }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                    {{ $t('Library overview') }}
                </h1>
            </div>
            <Link
                v-if="role !== 'reader'"
                href="/books/create"
                class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:opacity-90"
            >
                {{ $t('Add a book') }}
            </Link>
        </header>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="item in [
                    {
                        label: 'Books in collection',
                        value: stats.books,
                        icon: BookOpen,
                    },
                    {
                        label: 'Active titles',
                        value: stats.active_books,
                        icon: BookOpen,
                    },
                    {
                        label: 'Categories',
                        value: stats.categories,
                        icon: Tags,
                    },
                    {
                        label: role === 'superadmin' ? 'People' : 'Publishers',
                        value:
                            role === 'superadmin'
                                ? stats.users
                                : stats.publishers,
                        icon: role === 'superadmin' ? Users : Building2,
                    },
                ]"
                :key="item.label"
                class="border-l-2 border-primary/60 py-2 pl-4"
            >
                <div
                    class="flex items-center gap-2 text-sm text-muted-foreground"
                >
                    <component :is="item.icon" class="size-4" />{{
                        $t(item.label)
                    }}
                </div>
                <p class="mt-2 text-3xl font-semibold tabular-nums">
                    {{ item.value ?? 0 }}
                </p>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-base font-semibold">
                    {{ $t('Recently added books') }}
                </h2>
                <Link
                    href="/books"
                    class="text-sm font-medium text-primary hover:underline"
                    >{{ $t('View collection') }}</Link
                >
            </div>
            <div class="overflow-x-auto border-y">
                <table class="w-full min-w-[600px] text-left text-sm">
                    <thead
                        class="text-xs tracking-wide text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="py-3 pr-4 font-medium">
                                {{ $t('Title') }}
                            </th>
                            <th class="py-3 pr-4 font-medium">
                                {{ $t('Category') }}
                            </th>
                            <th class="py-3 pr-4 font-medium">
                                {{ $t('Publisher') }}
                            </th>
                            <th class="py-3 font-medium">{{ $t('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="book in recentBooks" :key="book.id">
                            <td class="py-3 pr-4 font-medium">
                                {{ book.title }}
                            </td>
                            <td class="py-3 pr-4 text-muted-foreground">
                                {{ book.category.title }}
                            </td>
                            <td class="py-3 pr-4 text-muted-foreground">
                                {{ book.publisher.title }}
                            </td>
                            <td class="py-3">
                                <span
                                    :class="
                                        book.status
                                            ? 'text-emerald-700'
                                            : 'text-muted-foreground'
                                    "
                                    >{{
                                        book.status
                                            ? $t('Active')
                                            : $t('Inactive')
                                    }}</span
                                >
                            </td>
                        </tr>
                        <tr v-if="recentBooks.length === 0">
                            <td
                                colspan="4"
                                class="py-8 text-center text-muted-foreground"
                            >
                                {{ $t('No books have been added yet.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
