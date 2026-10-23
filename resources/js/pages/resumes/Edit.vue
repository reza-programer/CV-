<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useEventListener } from '@vueuse/core';
import EditorToolbar from '@/components/editor/EditorToolbar.vue';
import EditorWorkspace from '@/components/editor/EditorWorkspace.vue';
import { provideEditor } from '@/components/editor/useEditorContext';
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
    guest: false,
    resumeId: props.resume.id!,
});

useEventListener(window, 'keydown', (event: KeyboardEvent) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
        event.preventDefault();
        void editor.save();
    }
});
</script>

<template>
    <Head :title="editor.resume.title || 'Editor CV'" />

    <div class="flex min-h-svh flex-col bg-white lg:h-svh">
        <EditorToolbar :templates="templates" />
        <EditorWorkspace />
    </div>
</template>
