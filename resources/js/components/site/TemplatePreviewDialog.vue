<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed } from 'vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import TemplateBadges from '@/components/resumes/TemplateBadges.vue';
import { CHOSEN_TEMPLATE_KEY } from '@/components/resumes/templateFilters';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { sampleResume } from '@/cv/sample';
import { isColoured, isMultiColumn } from '@/cv/templates';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';

/**
 * Larger look at one template from the landing gallery, with a way to start
 * a CV with it. The choice survives sign-up through session storage and is
 * picked up by the dashboard.
 */
const slug = defineModel<TemplateSlug | null>({ required: true });

const props = defineProps<{
    templates: TemplateSummary[];
    startHref: string;
}>();

const index = computed(() =>
    props.templates.findIndex((template) => template.slug === slug.value),
);
const template = computed(() => props.templates[index.value] ?? null);

const facts = computed(() => {
    if (!template.value) {
        return [];
    }

    const columns = isMultiColumn(template.value.slug)
        ? 'Dua kolom: tampilan lebih grafis. Sebagian ATS lama dapat membaca urutan kolom kurang tepat, jadi cocok untuk dikirim langsung ke perekrut.'
        : 'Satu kolom: dibaca dari atas ke bawah, paling aman untuk portal lowongan dan sistem ATS.';
    const colour = isColoured(template.value.slug)
        ? 'Warna aksen dapat dipilih: navy, teal, hijau, merah marun, ungu, atau arang.'
        : 'Hitam-putih, tetap rapi saat dicetak di printer kantor.';

    return [
        columns,
        colour,
        'Isi CV yang sama dapat dipindah ke template lain kapan saja.',
    ];
});

function onOpenChange(open: boolean): void {
    if (!open) {
        slug.value = null;
    }
}

function go(offset: number): void {
    const count = props.templates.length;
    slug.value =
        props.templates[(index.value + offset + count) % count]?.slug ?? null;
}

function useTemplate(): void {
    if (!template.value) {
        return;
    }

    try {
        sessionStorage.setItem(CHOSEN_TEMPLATE_KEY, template.value.slug);
    } catch {
        // Storage can be unavailable (private mode); the dashboard then opens normally.
    }

    router.visit(props.startHref);
}
</script>

<template>
    <Dialog :open="slug !== null" @update:open="onOpenChange">
        <DialogContent
            class="max-h-[94svh] gap-0 overflow-y-auto p-0 sm:max-w-4xl"
        >
            <div
                v-if="template"
                class="grid md:grid-cols-[minmax(0,_1fr)_minmax(0,_20rem)]"
            >
                <div class="bg-desk px-6 py-8 sm:px-12 sm:py-10">
                    <div class="mx-auto max-w-[26rem] paper">
                        <CvThumbnail :data="sampleResume(template.slug)" />
                    </div>
                </div>

                <div class="flex flex-col gap-6 p-6 sm:p-8">
                    <div>
                        <p class="eyebrow">
                            Template {{ index + 1 }} dari {{ templates.length }}
                        </p>
                        <DialogTitle
                            class="mt-2 flex flex-wrap items-center gap-2 font-display text-2xl font-medium tracking-tight text-ink"
                        >
                            {{ template.name }}
                        </DialogTitle>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            <TemplateBadges :slug="template.slug" />
                        </div>
                        <DialogDescription
                            class="mt-3 text-sm leading-relaxed text-neutral-600"
                        >
                            {{ template.description }}
                        </DialogDescription>
                    </div>

                    <ul class="grid gap-3 border-t border-neutral-200 pt-5">
                        <li
                            v-for="fact in facts"
                            :key="fact"
                            class="text-sm leading-relaxed text-neutral-600"
                        >
                            {{ fact }}
                        </li>
                    </ul>

                    <div class="mt-auto grid gap-3">
                        <Button size="lg" @click="useTemplate"
                            >Pakai template ini</Button
                        >
                        <div class="flex items-center justify-between gap-2">
                            <Button variant="ghost" size="sm" @click="go(-1)">
                                <ChevronLeft />
                                Sebelumnya
                            </Button>
                            <Button variant="ghost" size="sm" @click="go(1)">
                                Berikutnya
                                <ChevronRight />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
