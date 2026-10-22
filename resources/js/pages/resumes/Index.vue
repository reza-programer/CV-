<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { ref } from 'vue';
import NewResumeDialog from '@/components/resumes/NewResumeDialog.vue';
import ResumeCard from '@/components/resumes/ResumeCard.vue';
import { Button } from '@/components/ui/button';
import type { TemplateSummary } from '@/cv/types';
import type { ResumeListItem } from '@/types/resume';

defineProps<{
    resumes: ResumeListItem[];
    templates: TemplateSummary[];
}>();

const creating = ref(false);
</script>

<template>
    <Head title="CV Saya" />

    <div class="py-8">
        <div
            class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-neutral-200 pb-6"
        >
            <div>
                <h1
                    class="text-xl font-semibold tracking-tight text-neutral-900"
                >
                    CV Saya
                </h1>
                <p class="mt-1 text-sm text-neutral-500">
                    {{
                        resumes.length
                            ? `${resumes.length} dokumen`
                            : 'Belum ada dokumen'
                    }}
                </p>
            </div>
            <Button @click="creating = true">
                <Plus />
                Buat CV
            </Button>
        </div>

        <div
            v-if="resumes.length"
            class="grid grid-cols-2 gap-x-5 gap-y-8 sm:grid-cols-3 lg:grid-cols-4"
        >
            <ResumeCard
                v-for="resume in resumes"
                :key="resume.id"
                :resume="resume"
            />
        </div>

        <div v-else class="grid max-w-md gap-3 py-10">
            <h2 class="text-base font-medium text-neutral-900">
                Mulai CV pertama Anda
            </h2>
            <p class="text-sm leading-relaxed text-neutral-600">
                Pilih template, isi data diri dan pengalaman, lalu lihat
                hasilnya langsung dalam format A4. CV tersimpan otomatis dan
                dapat diunduh sebagai PDF kapan saja.
            </p>
            <Button class="justify-self-start" @click="creating = true">
                <Plus />
                Buat CV
            </Button>
        </div>

        <NewResumeDialog v-model:open="creating" :templates="templates" />
    </div>
</template>
