<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';
import { dashboard } from '@/routes';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <div class="flex min-h-svh flex-col bg-white">
        <header class="border-b border-neutral-200">
            <div
                class="mx-auto flex h-14 max-w-6xl items-center gap-6 px-4 sm:px-6"
            >
                <Link :href="dashboard()" aria-label="CVForge, ke CV Saya">
                    <AppLogo />
                </Link>

                <nav
                    class="flex h-full items-stretch"
                    aria-label="Navigasi utama"
                >
                    <Link
                        :href="dashboard()"
                        class="flex items-center border-b-2 px-1 text-sm"
                        :class="
                            isCurrentUrl(dashboard())
                                ? 'border-neutral-900 font-medium text-neutral-900'
                                : 'border-transparent text-neutral-600 hover:text-neutral-900'
                        "
                    >
                        CV Saya
                    </Link>
                </nav>

                <DropdownMenu>
                    <DropdownMenuTrigger
                        class="ml-auto flex items-center gap-2 rounded-md px-1.5 py-1 text-sm text-neutral-700 hover:bg-neutral-100 focus-visible:ring-2 focus-visible:ring-neutral-300 focus-visible:outline-none"
                    >
                        <Avatar class="size-7 rounded-full">
                            <AvatarFallback
                                class="rounded-full bg-neutral-200 text-xs font-medium text-neutral-800"
                            >
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span class="hidden max-w-40 truncate sm:inline">{{
                            user.name
                        }}</span>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <UserMenuContent :user="user" />
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 sm:px-6">
            <slot />
        </main>

        <Toaster position="bottom-right" />
    </div>
</template>
