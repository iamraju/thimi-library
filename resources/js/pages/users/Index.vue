<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';

type User = {
    id: number;
    name: string;
    email: string;
    role: 'superadmin' | 'librarian' | 'reader';
    created_at: string;
};
const props = defineProps<{
    users: {
        data: User[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();
const search = useForm({ search: props.filters.search ?? '' });
const submitSearch = () =>
    search.get('/users', { preserveState: true, replace: true });
const remove = (user: User) => {
    if (window.confirm(`Delete ${user.name}?`))
        router.delete(`/users/${user.id}`);
};
</script>

<template>
    <Head title="User management" />
    <main class="space-y-6 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm text-muted-foreground">Access control</p>
                <h1 class="mt-1 text-2xl font-semibold">Users</h1>
            </div>
            <Link
                href="/users/create"
                class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground"
                >Add user</Link
            >
        </header>
        <form class="flex max-w-lg gap-2" @submit.prevent="submitSearch">
            <input
                v-model="search.search"
                type="search"
                placeholder="Find by name or email"
                class="h-9 min-w-0 flex-1 rounded-md border bg-background px-3 text-sm"
            /><button class="h-9 rounded-md border px-4 text-sm font-medium">
                Search
            </button>
        </form>
        <div class="overflow-x-auto border-y">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead
                    class="text-xs tracking-wide text-muted-foreground uppercase"
                >
                    <tr>
                        <th class="py-3 pr-4 font-medium">Name</th>
                        <th class="py-3 pr-4 font-medium">Email</th>
                        <th class="py-3 pr-4 font-medium">Role</th>
                        <th class="py-3 pr-4 font-medium">Added</th>
                        <th class="py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="py-3 pr-4 font-medium">{{ user.name }}</td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{ user.email }}
                        </td>
                        <td class="py-3 pr-4 capitalize">{{ user.role }}</td>
                        <td class="py-3 pr-4 text-muted-foreground">
                            {{ new Date(user.created_at).toLocaleDateString() }}
                        </td>
                        <td class="py-3 text-right">
                            <Link
                                :href="`/users/${user.id}/edit`"
                                class="text-primary hover:underline"
                                >Edit</Link
                            ><button
                                class="ml-4 text-destructive hover:underline"
                                @click="remove(user)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td
                            colspan="5"
                            class="py-10 text-center text-muted-foreground"
                        >
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav
            v-if="users.links.length > 3"
            class="flex flex-wrap gap-1"
            aria-label="Pagination"
        >
            <Link
                v-for="link in users.links"
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
