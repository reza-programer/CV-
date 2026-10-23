<script setup lang="ts">
import { isColoured, isMultiColumn } from '@/cv/templates';

/** Small labels that tell coloured and two-column templates apart. */
defineProps<{ slug: string }>();
</script>

<template>
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
