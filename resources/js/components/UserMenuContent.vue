<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Check, Languages, LogOut, Settings } from '@lucide/vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { useI18n } from '@/composables/useI18n';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();

const { locale, locales } = useI18n();

const setLocale = (code: string) =>
    router.post('/locale', { locale: code }, { preserveScroll: true });
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" :href="edit()" prefetch>
                <Settings class="mr-2 h-4 w-4" />
                {{ $t('Settings') }}
            </Link>
        </DropdownMenuItem>
        <DropdownMenuLabel
            class="flex items-center gap-2 text-xs text-muted-foreground"
        >
            <Languages class="h-4 w-4" />
            {{ $t('Language') }}
        </DropdownMenuLabel>
        <DropdownMenuItem
            v-for="(name, code) in locales()"
            :key="code"
            class="cursor-pointer"
            @click="setLocale(String(code))"
        >
            <Check
                class="mr-2 h-4 w-4"
                :class="{ invisible: locale() !== code }"
            />
            {{ name }}
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full cursor-pointer"
            :href="logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            {{ $t('Log out') }}
        </Link>
    </DropdownMenuItem>
</template>
