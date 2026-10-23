<script setup lang="ts">
import type { Component } from 'vue';
import { computed } from 'vue';
import type { Block } from './blocks';

/**
 * Inside of one A4 page: an optional full-width top region (first page of
 * column layouts) followed by the columns. Single-column templates get just
 * the main column. Columns are emitted in reading order, so the side column
 * comes first in the PDF text only when it is drawn on the left.
 */
const props = defineProps<{
    blockComponent: Component;
    top: Block[];
    main: Block[];
    /** `null` for single-column templates. */
    aside: Block[] | null;
    asideFirst: boolean;
    /** Tag blocks with their index for measurement. */
    indexed?: boolean;
}>();

const columns = computed(() => {
    if (props.aside === null) {
        return [{ region: 'main' as const, blocks: props.main }];
    }

    const main = { region: 'main' as const, blocks: props.main };
    const aside = { region: 'aside' as const, blocks: props.aside };

    return props.asideFirst ? [aside, main] : [main, aside];
});

/** Only column templates distinguish regions; keep other markup unchanged. */
const regionAttrs = (region: string) =>
    props.aside === null ? {} : { region };
</script>

<template>
    <div v-if="top.length" class="cv-top cv-flow" data-flow="top">
        <component
            :is="blockComponent"
            v-for="block in top"
            :key="block.id"
            :block="block"
            v-bind="regionAttrs('top')"
        />
    </div>
    <div class="cv-columns">
        <div
            v-for="column in columns"
            :key="column.region"
            class="cv-col"
            :class="`cv-col-${column.region}`"
        >
            <div class="cv-flow" :data-flow="column.region">
                <component
                    :is="blockComponent"
                    v-for="(block, index) in column.blocks"
                    :key="block.id"
                    :block="block"
                    :data-index="indexed ? index : undefined"
                    v-bind="regionAttrs(column.region)"
                />
            </div>
        </div>
    </div>
</template>
