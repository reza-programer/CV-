<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const signedIn = computed(() => !!page.props.auth?.user);
const year = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-neutral-200">
        <div
            class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:grid-cols-[minmax(0,_1fr)_auto_auto] sm:gap-16 sm:px-6"
        >
            <div class="max-w-xs">
                <AppLogo />
                <p class="mt-4 text-sm leading-relaxed text-neutral-600">
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
        </div>
        <div class="border-t border-neutral-200">
            <p
                class="mx-auto max-w-6xl px-4 py-6 text-xs text-neutral-500 sm:px-6"
            >
                © {{ year }} CVForge
            </p>
        </div>
    </footer>
</template>
