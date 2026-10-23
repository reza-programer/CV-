<script setup lang="ts">
/** Eyebrow, serif title and lead paragraph that open each landing page section. */
defineProps<{
    eyebrow: string;
    title: string;
    lead?: string;
    titleId?: string;
}>();
</script>

<template>
    <div class="max-w-2xl">
        <p class="eyebrow">{{ eyebrow }}</p>
        <h2
            :id="titleId"
            class="mt-3 font-display text-[2rem] leading-[1.15] font-medium tracking-tight text-ink sm:text-[2.5rem]"
        >
            {{ title }}
        </h2>
        <p
            v-if="lead"
            class="mt-4 text-base leading-relaxed text-neutral-600 sm:text-[17px]"
        >
            {{ lead }}
        </p>
        <slot />
    </div>
</template>
