<script setup lang="ts">
import { Sparkles } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { useEditor } from '../useEditorContext';
import AssistantNotice from './AssistantNotice.vue';
import JobDescriptionField from './JobDescriptionField.vue';
import { useAssistant } from './useAssistant';

const editor = useEditor();
const open = ref(false);
const draft = ref('');
const { jobDescription, loading, error, result, run, reset } = useAssistant<{
    summary: string;
    notes: string[];
}>('summary');

async function generate(): Promise<void> {
    const response = await run();
    draft.value = response?.summary ?? '';
}

function apply(): void {
    editor.resume.summary = draft.value.trim();
    open.value = false;
}

function onOpenChange(value: boolean): void {
    open.value = value;

    if (!value) {
        reset();
        draft.value = '';
    }
}
</script>

<template>
    <Button
        variant="ghost"
        size="sm"
        class="h-7 px-2 text-xs"
        @click="open = true"
    >
        <Sparkles />
        Bantu tulis
    </Button>

    <Dialog :open="open" @update:open="onOpenChange">
        <DialogContent class="sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>Perbaiki profil singkat</DialogTitle>
                <DialogDescription
                    >Asisten menyusun profil dari isi CV
                    Anda.</DialogDescription
                >
            </DialogHeader>

            <div class="grid gap-4">
                <AssistantNotice />
                <JobDescriptionField v-model="jobDescription" />

                <div v-if="result" class="grid gap-1.5">
                    <label
                        for="assistant-summary"
                        class="text-[13px] font-medium text-neutral-800"
                        >Saran (dapat disunting)</label
                    >
                    <Textarea
                        id="assistant-summary"
                        v-model="draft"
                        rows="6"
                        maxlength="2000"
                        class="text-sm"
                    />
                    <ul
                        v-if="result.notes.length"
                        class="grid gap-1 text-xs text-neutral-600"
                    >
                        <li v-for="note in result.notes" :key="note">
                            – {{ note }}
                        </li>
                    </ul>
                </div>

                <p v-if="error" class="text-sm text-red-600" role="alert">
                    {{ error }}
                </p>
            </div>

            <DialogFooter class="gap-2">
                <Button variant="outline" :disabled="loading" @click="generate">
                    <Spinner v-if="loading" />
                    {{ result ? 'Buat ulang' : 'Buat saran' }}
                </Button>
                <Button :disabled="!result || !draft.trim()" @click="apply"
                    >Gunakan</Button
                >
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
