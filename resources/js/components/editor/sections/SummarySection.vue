<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea';
import SummaryAssistant from '../assistant/SummaryAssistant.vue';
import EditorSection from '../EditorSection.vue';
import { useEditor } from '../useEditorContext';

const MAX = 2000;
const editor = useEditor();
</script>

<template>
    <EditorSection
        id="section-summary"
        title="Profil singkat"
        description="2–4 kalimat tentang keahlian utama dan arah karier Anda."
        :has-error="!!editor.error('summary')"
    >
        <div class="grid gap-1.5">
            <div class="flex items-center justify-between">
                <label
                    for="summary"
                    class="text-[13px] font-medium text-neutral-800"
                    >Ringkasan profesional</label
                >
                <SummaryAssistant v-if="editor.assistantEnabled" />
            </div>
            <Textarea
                id="summary"
                v-model="editor.resume.summary"
                :maxlength="MAX"
                rows="5"
                class="min-h-28 text-sm leading-relaxed"
                placeholder="Contoh struktur: peran dan lama pengalaman, keahlian yang paling relevan, lalu jenis pekerjaan yang Anda tuju."
                :aria-invalid="editor.error('summary') ? true : undefined"
            />
            <div class="flex justify-between text-xs">
                <span class="text-red-600">{{ editor.error('summary') }}</span>
                <span class="text-neutral-400 tabular-nums"
                    >{{ editor.resume.summary.length }}/{{ MAX }}</span
                >
            </div>
        </div>
    </EditorSection>
</template>
