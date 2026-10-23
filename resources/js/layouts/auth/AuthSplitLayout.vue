<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Check } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import { sampleResume } from '@/cv/sample';
import { home } from '@/routes';

/**
 * Sign-in pages: the form on the left, and on large screens an example CV
 * lying on the desk, so the product is visible while signing up.
 */
defineProps<{
    title?: string;
    description?: string;
}>();

const example = sampleResume('professional');
const year = new Date().getFullYear();
const points = [
    'PDF A4 berteks asli, terbaca sistem ATS',
    'Pratinjau langsung, sama dengan hasil cetak',
    'Tersimpan otomatis dan hanya dapat dibuka oleh Anda',
];
</script>

<template>
    <div
        class="grid min-h-svh bg-white lg:grid-cols-[minmax(0,_1fr)_minmax(0,_1fr)]"
    >
        <div class="flex min-w-0 flex-col px-6 py-6 sm:px-10">
            <Link
                :href="home()"
                class="self-start"
                aria-label="CVForge, ke beranda"
            >
                <AppLogo />
            </Link>

            <main class="flex flex-1 items-center justify-center py-12">
                <div class="w-full max-w-sm">
                    <h1
                        v-if="title"
                        class="font-display text-[2rem] leading-tight font-medium tracking-tight text-ink"
                    >
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="mt-2 text-sm leading-relaxed text-neutral-600"
                    >
                        {{ description }}
                    </p>
                    <div class="mt-8">
                        <slot />
                    </div>
                </div>
            </main>

            <p class="text-xs text-neutral-500">© {{ year }} CVForge</p>
        </div>

        <aside
            class="hidden min-w-0 flex-col items-center justify-center bg-desk px-12 py-12 lg:flex"
            aria-label="Contoh CV"
        >
            <div class="w-full max-w-[21rem]">
                <div class="relative">
                    <div
                        class="absolute inset-0 translate-x-3 translate-y-3 paper"
                        aria-hidden="true"
                    />
                    <div class="relative paper">
                        <CvThumbnail :data="example" />
                    </div>
                </div>

                <p
                    class="mt-12 font-display text-2xl leading-snug font-medium tracking-tight text-ink"
                >
                    Yang Anda lihat di pratinjau adalah yang tercetak.
                </p>
                <ul class="mt-5 grid gap-2.5 text-sm text-neutral-700">
                    <li
                        v-for="point in points"
                        :key="point"
                        class="flex items-start gap-2.5"
                    >
                        <Check
                            class="mt-0.5 size-4 shrink-0 text-ink-blue"
                            :stroke-width="2.25"
                        />
                        {{ point }}
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</template>
