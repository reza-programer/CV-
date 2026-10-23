<script setup lang="ts">
import { computed } from 'vue';
import { buildBlocks } from '@/cv/blocks';
import type { ResumeData } from '@/cv/types';

/**
 * The CV as plain text, in reading order: roughly what an applicant tracking
 * system extracts from the PDF. Built from the same blocks as the document.
 */
const props = withDefaults(
    defineProps<{ data: ResumeData; limit?: number }>(),
    { limit: 24 },
);

type Line = { text: string; tone: 'strong' | 'heading' | 'plain' | 'muted' };

const lines = computed(() => {
    const out: Line[] = [];

    for (const block of buildBlocks(props.data)) {
        if (block.kind === 'header') {
            out.push({ text: block.name, tone: 'strong' });

            if (block.headline) {
                out.push({ text: block.headline, tone: 'plain' });
            }

            if (block.contacts.length) {
                out.push({
                    text: block.contacts
                        .map((contact) => contact.label)
                        .join(' | '),
                    tone: 'muted',
                });
            }
        } else if (block.kind === 'heading') {
            out.push({ text: block.title.toUpperCase(), tone: 'heading' });
        } else if (block.kind === 'text') {
            out.push({ text: block.text, tone: 'plain' });
        } else if (block.kind === 'entry') {
            const { title, subtitle, dates, location } = block.entry;
            out.push({
                text: [title, subtitle].filter(Boolean).join(', '),
                tone: 'strong',
            });

            if (dates || location) {
                out.push({
                    text: [dates, location].filter(Boolean).join(' · '),
                    tone: 'muted',
                });
            }
        } else if (block.kind === 'bullet') {
            out.push({ text: `- ${block.text}`, tone: 'plain' });
        } else {
            out.push({
                text: block.label
                    ? `${block.label}: ${block.value}`
                    : block.value,
                tone: 'plain',
            });
        }
    }

    return out;
});

const visible = computed(() => lines.value.slice(0, props.limit));
</script>

<template>
    <div
        class="font-mono text-[9.5px] leading-[1.55] break-words whitespace-pre-wrap text-neutral-700"
    >
        <p
            v-for="(line, index) in visible"
            :key="index"
            :class="{
                'font-semibold text-ink': line.tone === 'strong',
                'mt-2.5 font-semibold tracking-wide text-ink-blue':
                    line.tone === 'heading',
                'text-neutral-500': line.tone === 'muted',
            }"
        >
            {{ line.text }}
        </p>
        <p v-if="lines.length > visible.length" class="mt-2 text-neutral-400">
            …
        </p>
    </div>
</template>
