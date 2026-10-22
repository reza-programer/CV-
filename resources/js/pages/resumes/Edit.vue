<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Eye } from '@lucide/vue';
import { useEventListener } from '@vueuse/core';
import CvPreview from '@/components/cv/CvPreview.vue';
import EditorToolbar from '@/components/editor/EditorToolbar.vue';
import CertificationSection from '@/components/editor/sections/CertificationSection.vue';
import EducationSection from '@/components/editor/sections/EducationSection.vue';
import ExperienceSection from '@/components/editor/sections/ExperienceSection.vue';
import LanguageSection from '@/components/editor/sections/LanguageSection.vue';
import PersonalSection from '@/components/editor/sections/PersonalSection.vue';
import ProjectSection from '@/components/editor/sections/ProjectSection.vue';
import SectionOrderSection from '@/components/editor/sections/SectionOrderSection.vue';
import SkillSection from '@/components/editor/sections/SkillSection.vue';
import SummarySection from '@/components/editor/sections/SummarySection.vue';
import { provideEditor } from '@/components/editor/useEditorContext';
import { Toaster } from '@/components/ui/sonner';
import { useResumeEditor } from '@/composables/useResumeEditor';
import type { ResumeData, TemplateSummary } from '@/cv/types';

const props = defineProps<{
    resume: ResumeData;
    templates: TemplateSummary[];
    savedAt: string | null;
    assistantEnabled: boolean;
}>();

const editor = useResumeEditor(props.resume, props.savedAt);

provideEditor({
    ...editor,
    assistantEnabled: props.assistantEnabled,
    resumeId: props.resume.id!,
});

useEventListener(window, 'keydown', (event: KeyboardEvent) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
        event.preventDefault();
        void editor.save();
    }
});

function scrollToPreview(): void {
    document.getElementById('preview')?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <Head :title="editor.resume.title || 'Editor CV'" />

    <div class="flex min-h-svh flex-col bg-white lg:h-svh">
        <EditorToolbar :templates="templates" />

        <div
            class="flex-1 lg:grid lg:min-h-0 lg:grid-cols-[minmax(440px,_560px)_minmax(0,_1fr)]"
        >
            <main
                class="lg:overflow-y-auto lg:border-r lg:border-neutral-200"
                aria-label="Isi CV"
            >
                <PersonalSection />
                <SummarySection />
                <ExperienceSection list="experiences" />
                <ExperienceSection list="activities" />
                <EducationSection />
                <ProjectSection />
                <SkillSection />
                <CertificationSection />
                <LanguageSection />
                <SectionOrderSection />
                <p class="px-5 py-6 text-xs text-neutral-500">
                    Perubahan disimpan otomatis. Bagian yang tidak diisi tidak
                    akan muncul di PDF.
                </p>
            </main>

            <CvPreview
                id="preview"
                :data="editor.resume"
                class="min-h-[70svh] border-t border-neutral-200 lg:h-full lg:min-h-0 lg:border-t-0"
                aria-label="Pratinjau dokumen"
            />
        </div>

        <button
            type="button"
            class="fixed right-4 bottom-4 z-30 flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white shadow-lg lg:hidden"
            @click="scrollToPreview"
        >
            <Eye class="size-4" />
            Pratinjau
        </button>

        <Toaster position="bottom-left" />
    </div>
</template>
