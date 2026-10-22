<script setup lang="ts">
import { computed } from 'vue';
import type { EntryView } from '../../blocks';
import { hasText } from '../../format';

/**
 * Inline run of secondary facts ("Jakarta · Jan 2021 – Sekarang · IPK 3,80"),
 * with the entry link last. Each template picks which facts to pass.
 */
const props = withDefaults(
    defineProps<{
        entry: EntryView;
        fields?: Array<'subtitle' | 'location' | 'dates' | 'details' | 'link'>;
        separator?: string;
    }>(),
    {
        fields: () => ['location', 'dates', 'details', 'link'],
        separator: ' · ',
    },
);

const parts = computed(() => {
    const result: Array<{ text: string; href: string | null }> = [];

    for (const field of props.fields) {
        if (field === 'details') {
            props.entry.details.forEach((text) =>
                result.push({ text, href: null }),
            );
        } else if (field === 'link') {
            if (props.entry.link) {
                result.push({
                    text: props.entry.link.label,
                    href: props.entry.link.href,
                });
            }
        } else if (hasText(props.entry[field])) {
            result.push({ text: props.entry[field], href: null });
        }
    }

    return result;
});
</script>

<template>
    <template v-for="(part, index) in parts" :key="index">
        <span v-if="index > 0">{{ separator }}</span>
        <a v-if="part.href" :href="part.href">{{ part.text }}</a>
        <span v-else>{{ part.text }}</span>
    </template>
</template>
