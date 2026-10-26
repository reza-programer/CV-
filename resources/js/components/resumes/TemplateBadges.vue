<script setup lang="ts">
import { isColoured, isMultiColumn } from '@/cv/templates';

/** Small labels for recommended, coloured and two-column templates. */
defineProps<{ slug: string; recommended?: boolean }>();
</script>

<template>
    <span
        v-if="recommended"
        class="rounded-sm bg-emerald-50 px-1 text-[10px] font-medium text-emerald-700 ring-1 ring-emerald-200 ring-inset"
        title="Paling aman untuk melamar: satu kolom, teks asli, terbaca sistem ATS"
        >Rekomendasi</span
    >
    <span
        v-if="isMultiColumn(slug)"
        class="rounded-sm bg-neutral-900 px-1 text-[10px] font-normal text-white"
        title="Dua kolom: lebih menarik, tetapi sebagian ATS lama membacanya kurang urut"
        >Kreatif</span
    >
    <span
        v-if="isColoured(slug)"
        class="rounded-sm border border-neutral-300 px-1 text-[10px] font-normal text-neutral-600"
        >Berwarna</span
    >
</template>
