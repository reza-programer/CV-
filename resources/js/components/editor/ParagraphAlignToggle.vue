<script setup lang="ts">
import { AlignJustify, AlignLeft } from '@lucide/vue';
import type { ParagraphAlign } from '@/cv/types';

/**
 * Word-style paragraph alignment for the profile and bullet points.
 */
const model = defineModel<ParagraphAlign>({ required: true });

const options: Array<{
    value: ParagraphAlign;
    label: string;
    icon: typeof AlignLeft;
}> = [
    { value: 'left', label: 'Rata kiri', icon: AlignLeft },
    { value: 'justify', label: 'Rata kanan-kiri', icon: AlignJustify },
];
</script>

<template>
    <div
        role="radiogroup"
        aria-label="Perataan paragraf"
        class="flex h-9 shrink-0 items-center rounded-md border border-input bg-white p-0.5 shadow-xs"
    >
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            role="radio"
            :aria-checked="model === option.value"
            :aria-label="option.label"
            :title="`${option.label} untuk profil dan poin`"
            class="flex h-full w-8 items-center justify-center rounded-[4px] text-neutral-500 outline-none hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-300"
            :class="{
                'bg-neutral-100 text-neutral-900': model === option.value,
            }"
            @click="model = option.value"
        >
            <component :is="option.icon" class="size-4" />
        </button>
    </div>
</template>
