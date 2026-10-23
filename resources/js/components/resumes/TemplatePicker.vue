<script setup lang="ts">
import { computed, ref } from 'vue';
import PaperTile from '@/components/cv/PaperTile.vue';
import { sampleResume } from '@/cv/sample';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';
import TemplateBadges from './TemplateBadges.vue';
import TemplateFilterBar from './TemplateFilterBar.vue';
import type { TemplateFilter } from './templateFilters';
import { filterTemplates } from './templateFilters';

/** Radio group of templates, each shown with a real rendering of example content. */
const model = defineModel<TemplateSlug>({ required: true });

const props = defineProps<{ templates: TemplateSummary[]; name?: string }>();

const filter = ref<TemplateFilter>('all');
const visible = computed(() => filterTemplates(props.templates, filter.value));
</script>

<template>
    <div class="grid gap-4">
        <TemplateFilterBar v-model="filter" :templates="templates" />

        <div
            role="radiogroup"
            aria-label="Template"
            class="grid grid-cols-2 gap-x-4 gap-y-6 sm:grid-cols-4"
        >
            <label
                v-for="template in visible"
                :key="template.slug"
                class="group grid min-w-0 cursor-pointer grid-cols-1 content-start gap-2.5"
            >
                <input
                    v-model="model"
                    type="radio"
                    :name="name ?? 'template'"
                    :value="template.slug"
                    class="peer sr-only"
                />
                <PaperTile
                    :data="sampleResume(template.slug)"
                    :selected="model === template.slug"
                    class="group-has-[:focus-visible]:ring-2 group-has-[:focus-visible]:ring-neutral-400"
                />
                <span>
                    <span
                        class="flex flex-wrap items-center gap-1.5 text-sm font-medium text-ink"
                    >
                        {{ template.name }}
                        <TemplateBadges :slug="template.slug" />
                    </span>
                    <span
                        class="mt-0.5 line-clamp-2 block text-xs leading-snug text-neutral-500"
                        >{{ template.description }}</span
                    >
                </span>
            </label>
        </div>

        <p class="text-xs leading-relaxed text-neutral-500">
            Template berlabel
            <span class="font-medium text-neutral-700">Kreatif</span>
            memakai dua kolom: tampil lebih menarik, tetapi sebagian sistem ATS
            lama membacanya kurang urut. Untuk melamar lewat portal lowongan,
            template satu kolom paling aman.
        </p>
    </div>
</template>
