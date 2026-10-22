<script setup lang="ts">
import { useResizeObserver } from '@vueuse/core';
import { computed, ref } from 'vue';
import CvDocument from '@/cv/CvDocument.vue';
import type { ResumeData } from '@/cv/types';

/** First page of a CV, rendered by the real template and scaled to its box. */
defineProps<{ data: ResumeData }>();

const PAGE_WIDTH = (210 / 25.4) * 96;

const box = ref<HTMLElement | null>(null);
const width = ref(0);

useResizeObserver(box, ([entry]) => {
    width.value = entry.contentRect.width;
});

const scale = computed(() =>
    width.value > 0 ? width.value / PAGE_WIDTH : 0.3,
);
</script>

<template>
    <div
        ref="box"
        class="cv-thumbnail pointer-events-none aspect-[210/297] w-full overflow-hidden bg-white select-none"
        aria-hidden="true"
    >
        <CvDocument :data="data" :scale="scale" :page-limit="1" />
    </div>
</template>
