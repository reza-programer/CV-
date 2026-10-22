<script setup lang="ts">
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import { sampleResume } from '@/cv/sample';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';

/** Radio group of templates, each shown with a real rendering of example content. */
const model = defineModel<TemplateSlug>({ required: true });

defineProps<{ templates: TemplateSummary[]; name?: string }>();
</script>

<template>
    <div
        role="radiogroup"
        aria-label="Template"
        class="grid grid-cols-2 gap-3 sm:grid-cols-4"
    >
        <label
            v-for="template in templates"
            :key="template.slug"
            class="group grid min-w-0 cursor-pointer grid-cols-1 content-start gap-2 rounded-md p-1.5 outline-none"
            :class="
                model === template.slug
                    ? 'bg-neutral-100'
                    : 'hover:bg-neutral-50'
            "
        >
            <input
                v-model="model"
                type="radio"
                :name="name ?? 'template'"
                :value="template.slug"
                class="sr-only"
            />
            <span
                class="block overflow-hidden rounded-sm border bg-white"
                :class="
                    model === template.slug
                        ? 'border-neutral-900 ring-1 ring-neutral-900'
                        : 'border-neutral-200 group-has-[:focus-visible]:border-neutral-500'
                "
            >
                <CvThumbnail :data="sampleResume(template.slug)" />
            </span>
            <span class="px-0.5">
                <span class="block text-sm font-medium text-neutral-900">{{
                    template.name
                }}</span>
                <span class="line-clamp-2 block text-xs text-neutral-500">{{
                    template.description
                }}</span>
            </span>
        </label>
    </div>
</template>
