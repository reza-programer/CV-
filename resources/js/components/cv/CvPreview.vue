<script setup lang="ts">
import { Minus, Plus } from '@lucide/vue';
import { useResizeObserver } from '@vueuse/core';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import CvDocument from '@/cv/CvDocument.vue';
import type { ResumeData } from '@/cv/types';

/**
 * Live A4 preview for the editor: the real document component, scaled to the
 * available width or to a fixed zoom level.
 */
defineProps<{ data: ResumeData }>();

/** 210 mm at 96 CSS px per inch. */
const PAGE_WIDTH = (210 / 25.4) * 96;
const ZOOM_STEPS = [0.5, 0.6, 0.75, 0.9, 1, 1.25];

const viewport = ref<HTMLElement | null>(null);
const available = ref(0);
const zoom = ref<number | null>(null);
const pageCount = ref(1);

useResizeObserver(viewport, ([entry]) => {
    available.value = entry.contentRect.width;
});

const fitScale = computed(() =>
    Math.min(1.1, Math.max(0.3, available.value / PAGE_WIDTH)),
);
const scale = computed(() => zoom.value ?? fitScale.value);

function step(direction: 1 | -1): void {
    const current = scale.value;
    const next =
        direction > 0
            ? ZOOM_STEPS.find((value) => value > current + 0.001)
            : [...ZOOM_STEPS]
                  .reverse()
                  .find((value) => value < current - 0.001);

    zoom.value = next ?? zoom.value;
}
</script>

<template>
    <div class="flex min-h-0 flex-col bg-neutral-100">
        <div
            class="flex h-10 shrink-0 items-center justify-between border-b border-neutral-200 bg-white px-3 text-xs text-neutral-600"
        >
            <span class="tabular-nums">A4 · {{ pageCount }} halaman</span>
            <div class="flex items-center gap-0.5">
                <Button
                    variant="ghost"
                    size="icon-sm"
                    aria-label="Perkecil"
                    :disabled="scale <= ZOOM_STEPS[0]"
                    @click="step(-1)"
                >
                    <Minus />
                </Button>
                <button
                    type="button"
                    class="w-14 rounded px-1 py-1 text-center tabular-nums hover:bg-neutral-100"
                    title="Sesuaikan lebar"
                    @click="zoom = null"
                >
                    {{ zoom === null ? 'Pas' : `${Math.round(scale * 100)}%` }}
                </button>
                <Button
                    variant="ghost"
                    size="icon-sm"
                    aria-label="Perbesar"
                    :disabled="scale >= ZOOM_STEPS[ZOOM_STEPS.length - 1]"
                    @click="step(1)"
                >
                    <Plus />
                </Button>
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-auto px-4 py-6 lg:px-8">
            <div
                ref="viewport"
                class="mx-auto w-full"
                :style="{ maxWidth: zoom === null ? '900px' : undefined }"
            >
                <div
                    class="cv-preview mx-auto"
                    :style="{ width: `${PAGE_WIDTH * scale}px` }"
                >
                    <CvDocument
                        :data="data"
                        :scale="scale"
                        :gap="20"
                        @laid-out="pageCount = $event"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
