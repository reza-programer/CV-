<script setup lang="ts">
import { Eye, PencilLine } from '@lucide/vue';
import { useEventListener, useMediaQuery } from '@vueuse/core';
import { nextTick, ref } from 'vue';
import CvPreview from '@/components/cv/CvPreview.vue';
import CertificationSection from '@/components/editor/sections/CertificationSection.vue';
import EducationSection from '@/components/editor/sections/EducationSection.vue';
import ExperienceSection from '@/components/editor/sections/ExperienceSection.vue';
import LanguageSection from '@/components/editor/sections/LanguageSection.vue';
import PersonalSection from '@/components/editor/sections/PersonalSection.vue';
import ProjectSection from '@/components/editor/sections/ProjectSection.vue';
import SectionOrderSection from '@/components/editor/sections/SectionOrderSection.vue';
import SkillSection from '@/components/editor/sections/SkillSection.vue';
import SummarySection from '@/components/editor/sections/SummarySection.vue';
import { Toaster } from '@/components/ui/sonner';
import { SHOW_FORM_EVENT, useEditor } from './useEditorContext';

/**
 * The form and the live A4 preview: side by side on large screens. On phones
 * and tablets they are two views switched from a bar at the bottom, so the
 * preview is one tap away instead of below every form section.
 */
const editor = useEditor();

const wide = useMediaQuery('(min-width: 1024px)');
const view = ref<'form' | 'preview'>('form');

/** Where the visitor was in the form, restored when they come back to it. */
let formScroll = 0;

async function show(next: 'form' | 'preview'): Promise<void> {
    if (next === view.value) {
        window.scrollTo({ top: 0, behavior: 'smooth' });

        return;
    }

    if (next === 'preview') {
        formScroll = window.scrollY;
    }

    view.value = next;
    await nextTick();
    // The toolbar stays above the preview, so its template and colour pickers are in reach.
    window.scrollTo({ top: next === 'form' ? formScroll : 0 });
}

useEventListener(window, SHOW_FORM_EVENT, () => {
    view.value = 'form';
});

const bottomOffset = { bottom: 88 };
</script>

<template>
    <div
        class="flex-1 max-lg:pb-20 lg:grid lg:min-h-0 lg:grid-cols-[minmax(440px,_560px)_minmax(0,_1fr)]"
    >
        <main
            v-show="wide || view === 'form'"
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
                {{
                    editor.guest
                        ? 'Bagian yang tidak diisi tidak akan muncul di PDF.'
                        : 'Perubahan disimpan otomatis. Bagian yang tidak diisi tidak akan muncul di PDF.'
                }}
            </p>
        </main>

        <!-- Mounted only while visible, so the pages are measured at their real width. -->
        <CvPreview
            v-if="wide || view === 'preview'"
            id="preview"
            :data="editor.resume"
            class="min-h-[70svh] lg:h-full lg:min-h-0"
            aria-label="Pratinjau dokumen"
        />
    </div>

    <nav
        v-if="!wide"
        class="fixed inset-x-0 bottom-0 z-30 border-t border-neutral-200 bg-white/95 px-4 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] backdrop-blur"
        aria-label="Tampilan editor"
    >
        <div
            class="mx-auto grid max-w-md grid-cols-2 gap-1 rounded-full bg-neutral-100 p-1"
        >
            <button
                v-for="tab in [
                    { key: 'form', label: 'Isi data', icon: PencilLine },
                    { key: 'preview', label: 'Pratinjau', icon: Eye },
                ] as const"
                :key="tab.key"
                type="button"
                :aria-pressed="view === tab.key"
                class="flex h-11 items-center justify-center gap-2 rounded-full text-sm font-medium transition-colors"
                :class="
                    view === tab.key
                        ? 'bg-white text-ink shadow-sm'
                        : 'text-neutral-500 hover:text-ink'
                "
                @click="show(tab.key)"
            >
                <component :is="tab.icon" class="size-4" />
                {{ tab.label }}
            </button>
        </div>
    </nav>

    <Toaster
        position="bottom-left"
        :offset="wide ? undefined : bottomOffset"
        :mobile-offset="wide ? undefined : bottomOffset"
    />
</template>
