<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import { Button } from '@/components/ui/button';
import { dashboard, home, login, register } from '@/routes';

const page = usePage();
const signedIn = computed(() => !!page.props.auth?.user);

const links = [
    { href: '#cara-kerja', label: 'Cara kerja' },
    { href: '#template', label: 'Template' },
    { href: '#ats', label: 'Ramah ATS' },
    { href: '#faq', label: 'FAQ' },
];
</script>

<template>
    <header class="sticky top-0 z-30 border-b border-neutral-200 bg-white">
        <div
            class="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4 sm:px-6"
        >
            <Link :href="home()" aria-label="CV by kezottt_, ke beranda">
                <AppLogo />
            </Link>

            <nav
                class="hidden items-center gap-1 text-sm md:flex"
                aria-label="Bagian halaman"
            >
                <a
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    class="rounded-md px-3 py-2 text-neutral-600 transition-colors hover:text-ink"
                    >{{ link.label }}</a
                >
            </nav>

            <div class="ml-auto flex items-center gap-2 text-sm">
                <Button v-if="signedIn" as-child>
                    <Link :href="dashboard()">CV Saya</Link>
                </Button>
                <template v-else>
                    <Link
                        :href="login()"
                        class="rounded-md px-3 py-2 text-neutral-700 transition-colors hover:text-ink"
                        >Masuk</Link
                    >
                    <Button as-child>
                        <Link :href="register()">Buat CV</Link>
                    </Button>
                </template>
            </div>
        </div>
    </header>
</template>
