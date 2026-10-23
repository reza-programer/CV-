<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const signedIn = computed(() => !!page.props.auth?.user);
const creator = computed(() => page.props.creator);
const year = new Date().getFullYear();

const LINK_NAMES = {
    instagram: 'Instagram',
    linkedin: 'LinkedIn',
    email: 'Email',
} as const;
</script>

<template>
    <footer class="border-t border-neutral-200">
        <div
            class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:grid-cols-2 sm:px-6 lg:grid-cols-[minmax(0,_1fr)_auto_auto_auto] lg:gap-16"
        >
            <div class="max-w-xs">
                <AppLogo />
                <p class="mt-4 text-sm font-medium text-ink">
                    Buat CV Profesional, Raih Peluangmu
                </p>
                <p class="mt-1.5 text-sm leading-relaxed text-neutral-600">
                    Pembuat CV ATS-friendly dalam format A4, dengan PDF berteks
                    asli.
                </p>
            </div>

            <nav aria-label="Produk" class="text-sm">
                <p class="font-medium text-ink">Produk</p>
                <ul class="mt-4 grid gap-2.5 text-neutral-600">
                    <li>
                        <a href="#template" class="hover:text-ink">Template</a>
                    </li>
                    <li>
                        <a href="#cara-kerja" class="hover:text-ink"
                            >Cara kerja</a
                        >
                    </li>
                    <li><a href="#ats" class="hover:text-ink">Ramah ATS</a></li>
                    <li><a href="#faq" class="hover:text-ink">FAQ</a></li>
                </ul>
            </nav>

            <nav aria-label="Akun" class="text-sm">
                <p class="font-medium text-ink">Akun</p>
                <ul class="mt-4 grid gap-2.5 text-neutral-600">
                    <template v-if="signedIn">
                        <li>
                            <Link :href="dashboard()" class="hover:text-ink"
                                >CV Saya</Link
                            >
                        </li>
                    </template>
                    <template v-else>
                        <li>
                            <Link :href="login()" class="hover:text-ink"
                                >Masuk</Link
                            >
                        </li>
                        <li>
                            <Link :href="register()" class="hover:text-ink"
                                >Daftar</Link
                            >
                        </li>
                    </template>
                </ul>
            </nav>

            <div v-if="creator?.links.length" class="text-sm">
                <p class="font-medium text-ink">Pembuat</p>
                <ul class="mt-4 grid gap-2.5">
                    <li v-for="link in creator.links" :key="link.kind">
                        <a
                            :href="link.href"
                            class="group grid text-neutral-600 hover:text-ink"
                            :target="
                                link.kind === 'email' ? undefined : '_blank'
                            "
                            :rel="
                                link.kind === 'email'
                                    ? undefined
                                    : 'noopener noreferrer'
                            "
                        >
                            <span class="text-xs text-neutral-400">{{
                                LINK_NAMES[link.kind]
                            }}</span>
                            <span class="break-all group-hover:underline">{{
                                link.label
                            }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-neutral-200">
            <p
                class="mx-auto flex max-w-6xl flex-wrap justify-between gap-2 px-4 py-6 text-xs text-neutral-500 sm:px-6"
            >
                <span>© {{ year }} CV by kezottt_</span>
                <span v-if="creator?.name">Dibuat oleh {{ creator.name }}</span>
            </p>
        </div>
    </footer>
</template>
