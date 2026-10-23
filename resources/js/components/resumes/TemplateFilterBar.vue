<script setup lang="ts">
import type { TemplateSummary } from '@/cv/types';
import type { TemplateFilter } from './templateFilters';
import { TEMPLATE_FILTERS, filterTemplates } from './templateFilters';

/** Segmented filter for the template gallery, with the number of templates per group. */
const model = defineModel<TemplateFilter>({ required: true });

defineProps<{ templates: TemplateSummary[] }>();
</script>

<template>
    <div
        class="grid w-full grid-cols-2 gap-1 rounded-md border border-neutral-200 bg-white p-1 sm:inline-flex sm:w-auto sm:justify-self-start"
        role="group"
        aria-label="Saring template"
    >
        <button
            v-for="filter in TEMPLATE_FILTERS"
            :key="filter.value"
            type="button"
            class="flex items-center justify-center gap-1.5 rounded-[5px] px-3 py-1.5 text-sm whitespace-nowrap transition-colors focus-visible:ring-2 focus-visible:ring-neutral-400 focus-visible:outline-none"
            :class="
                model === filter.value
                    ? 'bg-ink text-white'
                    : 'text-neutral-600 hover:bg-neutral-100 hover:text-ink'
            "
            :aria-pressed="model === filter.value"
            @click="model = filter.value"
        >
            {{ filter.label }}
            <span
                class="text-xs tabular-nums"
                :class="
                    model === filter.value
                        ? 'text-white/70'
                        : 'text-neutral-400'
                "
                >{{ filterTemplates(templates, filter.value).length }}</span
            >
        </button>
    </div>
</template>
