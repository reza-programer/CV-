<script setup lang="ts">
import { ArrowDown, ArrowUp } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { buildBlocks } from '@/cv/blocks';
import { SECTION_TITLES } from '@/cv/format';
import type { SectionKey } from '@/cv/types';
import EditorSection from '../EditorSection.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

/** Sections with content; empty ones are listed but greyed out because they are not printed. */
const filled = computed(
    () =>
        new Set(
            buildBlocks(editor.resume).flatMap((block) =>
                block.kind === 'heading' ? [block.section] : [],
            ),
        ),
);

function move(index: number, offset: number): void {
    const order = editor.resume.section_order;
    const target = index + offset;

    if (target < 0 || target >= order.length) {
        return;
    }

    [order[index], order[target]] = [order[target], order[index]];
}

const title = (section: SectionKey) =>
    SECTION_TITLES[editor.resume.locale][section];
</script>

<template>
    <EditorSection
        id="section-order"
        title="Urutan bagian"
        description="Atur urutan bagian pada dokumen. Bagian yang kosong tidak dicetak."
        :default-open="false"
    >
        <ol
            class="divide-y divide-neutral-100 rounded-md border border-neutral-200"
        >
            <li
                v-for="(section, index) in editor.resume.section_order"
                :key="section"
                class="flex items-center gap-2 py-1 pr-1 pl-3"
            >
                <span class="w-5 text-xs text-neutral-400 tabular-nums">{{
                    index + 1
                }}</span>
                <span
                    class="flex-1 text-sm"
                    :class="
                        filled.has(section)
                            ? 'text-neutral-900'
                            : 'text-neutral-400'
                    "
                >
                    {{ title(section) }}
                    <span v-if="!filled.has(section)" class="text-xs"
                        >· kosong</span
                    >
                </span>
                <Button
                    variant="ghost"
                    size="icon-sm"
                    :disabled="index === 0"
                    :aria-label="`Naikkan ${title(section)}`"
                    @click="move(index, -1)"
                >
                    <ArrowUp />
                </Button>
                <Button
                    variant="ghost"
                    size="icon-sm"
                    :disabled="index === editor.resume.section_order.length - 1"
                    :aria-label="`Turunkan ${title(section)}`"
                    @click="move(index, 1)"
                >
                    <ArrowDown />
                </Button>
            </li>
        </ol>
    </EditorSection>
</template>
