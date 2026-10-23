<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import NewResumeDialog from '@/components/resumes/NewResumeDialog.vue';
import ResumeCard from '@/components/resumes/ResumeCard.vue';
import { CHOSEN_TEMPLATE_KEY } from '@/components/resumes/templateFilters';
import { Button } from '@/components/ui/button';
import { sampleResume } from '@/cv/sample';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';
import type { ResumeListItem } from '@/types/resume';

const props = defineProps<{
    resumes: ResumeListItem[];
    templates: TemplateSummary[];
}>();

const page = usePage();
const firstName = computed(
    () => page.props.auth.user.name.trim().split(/\s+/)[0] ?? '',
);

const creating = ref(false);
const chosenTemplate = ref<TemplateSlug | null>(null);
const example = sampleResume('professional');

const steps = [
    'Pilih template dan bahasa dokumen.',
    'Isi data diri, pengalaman, dan keahlian; pratinjau A4 berubah seketika.',
    'Unduh PDF berteks asli kapan saja.',
];

function startNew(template: TemplateSlug | null = null): void {
    chosenTemplate.value = template;
    creating.value = true;
}

// A template picked on the landing page before signing in or signing up.
onMounted(() => {
    let slug: string | null = null;

    try {
        slug = sessionStorage.getItem(CHOSEN_TEMPLATE_KEY);
        sessionStorage.removeItem(CHOSEN_TEMPLATE_KEY);
    } catch {
        return;
    }

    const template = props.templates.find((item) => item.slug === slug);

    if (template) {
        startNew(template.slug);
    }
});
</script>

<template>
    <Head title="CV Saya" />

    <div class="py-10 lg:py-12">
        <div
            class="flex flex-wrap items-end justify-between gap-6 border-b border-neutral-200 pb-8"
        >
            <div>
                <p class="eyebrow">Halo, {{ firstName }}</p>
                <h1
                    class="mt-2 font-display text-[2.25rem] leading-tight font-medium tracking-tight text-ink"
                >
                    CV Saya
                </h1>
                <p class="mt-1 text-sm text-neutral-500">
                    {{
                        resumes.length
                            ? `${resumes.length} dokumen · tersimpan otomatis`
                            : 'Belum ada dokumen'
                    }}
                </p>
            </div>
            <Button size="lg" @click="startNew()">
                <Plus />
                Buat CV
            </Button>
        </div>

        <div
            v-if="resumes.length"
            class="mt-10 grid grid-cols-2 gap-x-5 gap-y-10 sm:grid-cols-3 sm:gap-x-6 lg:grid-cols-4"
        >
            <button
                type="button"
                class="group grid min-w-0 grid-cols-1 content-start gap-3 text-left focus-visible:outline-none"
                @click="startNew()"
            >
                <!-- Same proportions as a CV tile: 9% desk margin around an A4 sheet. -->
                <span
                    class="flex aspect-[100/134] flex-col items-center justify-center gap-3 rounded-md border border-dashed border-neutral-300 text-neutral-500 transition-colors group-hover:border-neutral-500 group-hover:text-ink group-focus-visible:ring-2 group-focus-visible:ring-neutral-400"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-full border border-current"
                    >
                        <Plus class="size-5" />
                    </span>
                    <span class="text-sm font-medium">CV baru</span>
                </span>
                <span class="text-sm text-neutral-500"
                    >Mulai dari template pilihan</span
                >
            </button>

            <ResumeCard
                v-for="resume in resumes"
                :key="resume.id"
                :resume="resume"
            />
        </div>

        <div
            v-else
            class="mt-10 grid items-center gap-12 rounded-lg border border-neutral-200 p-6 sm:p-10 lg:grid-cols-[minmax(0,_1fr)_minmax(0,_22rem)]"
        >
            <div class="max-w-lg">
                <h2
                    class="font-display text-[1.75rem] leading-tight font-medium tracking-tight"
                >
                    Mulai CV pertama Anda
                </h2>
                <p class="mt-3 leading-relaxed text-neutral-600">
                    Nama dan email akun Anda diisikan otomatis. Lengkapi sisanya
                    dan lihat hasilnya langsung dalam format A4.
                </p>
                <ol class="mt-8 grid gap-4">
                    <li
                        v-for="(step, index) in steps"
                        :key="step"
                        class="flex gap-4 text-sm leading-relaxed text-neutral-700"
                    >
                        <span
                            class="font-display text-lg leading-none font-medium text-ink-blue tabular-nums"
                            >0{{ index + 1 }}</span
                        >
                        {{ step }}
                    </li>
                </ol>
                <Button size="lg" class="mt-10" @click="startNew()">
                    <Plus />
                    Buat CV
                </Button>
            </div>
            <figure class="hidden rounded-md bg-desk p-8 lg:block">
                <div class="paper">
                    <CvThumbnail :data="example" />
                </div>
                <figcaption class="mt-4 text-center text-xs text-neutral-500">
                    Contoh hasil · Professional ATS
                </figcaption>
            </figure>
        </div>

        <NewResumeDialog
            v-model:open="creating"
            :templates="templates"
            :template="chosenTemplate"
        />
    </div>
</template>
