<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { t } from '@/composables/useI18n';

type Publisher = {
    id: number;
    title: string;
    address: string | null;
    contact_person: string | null;
    telephone: string | null;
    mobile: string | null;
    status: number;
    books_count: number;
};
const props = defineProps<{
    publishers: {
        data: Publisher[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();
const search = useForm({ search: props.filters.search ?? '' });
const submitSearch = () =>
    search.get('/publishers', { preserveState: true, replace: true });
const remove = (id: number) => {
    if (window.confirm(t('Delete this publisher?')))
        router.delete(`/publishers/${id}`);
};
</script>

<template>
    <Head :title="$t('Publishers')" />
    <main class="space-y-6 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-muted-foreground">
                    {{ $t('Catalog setup') }}
                </p>
                <h1 class="mt-1 text-2xl font-semibold">
                    {{ $t('Publishers') }}
                </h1>
            </div>
            <Link
                href="/publishers/create"
                class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground"
                >{{ $t('Add publisher') }}</Link
            >
        </header>
        <form class="flex max-w-lg gap-2" @submit.prevent="submitSearch">
            <input
                v-model="search.search"
                type="search"
                :placeholder="$t('Find a publisher')"
                class="h-9 min-w-0 flex-1 rounded-md border bg-background px-3 text-sm"
            /><button class="h-9 rounded-md border px-4 text-sm font-medium">
                {{ $t('Search') }}
            </button>
        </form>
        <div class="overflow-x-auto border-y">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead
                    class="text-xs tracking-wide text-muted-foreground uppercase"
                >
                    <tr>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t('Publisher') }}
                        </th>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t('Contact') }}
                        </th>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t('Telephone / Mobile') }}
                        </th>
                        <th class="py-3 pr-4 font-medium">{{ $t('Books') }}</th>
                        <th class="py-3 pr-4 font-medium">
                            {{ $t('Status') }}
                        </th>
                        <th class="py-3 text-right font-medium">
                            {{ $t('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr
                        v-for="publisher in publishers.data"
                        :key="publisher.id"
                    >
                        <td class="py-3 pr-4">
                            <div class="font-medium">{{ publisher.title }}</div>
                            <div
                                class="max-w-sm truncate text-xs text-muted-foreground"
                            >
                                {{ publisher.address || '—' }}
                            </div>
                        </td>
                        <td class="py-3 pr-4">
                            {{ publisher.contact_person || '—' }}
                        </td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{
                                [publisher.telephone, publisher.mobile]
                                    .filter(Boolean)
                                    .join(' / ') || '—'
                            }}
                        </td>
                        <td class="py-3 pr-4 tabular-nums">
                            {{ publisher.books_count }}
                        </td>
                        <td class="py-3 pr-4">
                            {{
                                publisher.status ? $t('Active') : $t('Inactive')
                            }}
                        </td>
                        <td class="py-3 text-right">
                            <Link
                                :href="`/publishers/${publisher.id}/edit`"
                                class="text-primary hover:underline"
                                >{{ $t('Edit') }}</Link
                            ><button
                                class="ml-4 text-destructive hover:underline"
                                @click="remove(publisher.id)"
                            >
                                {{ $t('Delete') }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="publishers.data.length === 0">
                        <td
                            colspan="6"
                            class="py-10 text-center text-muted-foreground"
                        >
                            {{ $t('No publishers found.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav
            v-if="publishers.links.length > 3"
            class="flex flex-wrap gap-1"
            aria-label="Pagination"
        >
            <Link
                v-for="link in publishers.links"
                :key="link.label"
                :href="link.url || '# '"
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
