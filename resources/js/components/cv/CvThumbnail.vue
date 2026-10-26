<script setup lang="ts">
import { useIntersectionObserver, useResizeObserver } from '@vueuse/core';
import { computed, ref } from 'vue';
import CvDocument from '@/cv/CvDocument.vue';
import type { ResumeData } from '@/cv/types';

/**
 * First page of a CV, rendered by the real template and scaled to its box.
 * The document is laid out only once the box nears the screen, so a gallery
 * of a hundred templates does not measure a hundred CVs on load.
 */
defineProps<{ data: ResumeData }>();

const PAGE_WIDTH = (210 / 25.4) * 96;

const box = ref<HTMLElement | null>(null);
const width = ref(0);
const near = ref(false);

useResizeObserver(box, ([entry]) => {
    width.value = entry.contentRect.width;
});

const { stop } = useIntersectionObserver(
    box,
    ([entry]) => {
        if (entry?.isIntersecting) {
            near.value = true;
            stop();
        }
    },
    { rootMargin: '600px 0px' },
);

const scale = computed(() =>
    width.value > 0 ? width.value / PAGE_WIDTH : 0.3,
);
</script>

<template>
    <div
        ref="box"
        class="cv-thumbnail pointer-events-none aspect-[210/297] w-full overflow-hidden bg-white select-none"
        aria-hidden="true"
        inert
    >
        <CvDocument v-if="near" :data="data" :scale="scale" :page-limit="1" />
    </div>
</template>
