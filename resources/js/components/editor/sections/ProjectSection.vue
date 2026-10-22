<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { emptyProject } from '@/cv/factories';
import { formatRange, joinParts } from '@/cv/format';
import type { ProjectItem } from '@/cv/types';
import HighlightsAssistant from '../assistant/HighlightsAssistant.vue';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import HighlightsInput from '../HighlightsInput.vue';
import PeriodFields from '../PeriodFields.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

const summary = (item: ProjectItem) => ({
    title: item.name,
    subtitle: joinParts(
        [
            item.role,
            formatRange(
                item.start_date,
                item.end_date,
                item.is_current,
                editor.resume.locale,
            ),
        ],
        ' · ',
    ),
});

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) => key.startsWith('projects.')),
);
</script>

<template>
    <EditorSection
        id="section-projects"
        title="Proyek"
        description="Proyek kerja, kuliah, atau pribadi yang menunjukkan keahlian Anda."
        :count="editor.resume.projects.length"
        :has-error="hasError"
    >
        <EntryList
            list="projects"
            :items="editor.resume.projects"
            add-label="Tambah proyek"
            empty-text="Belum ada proyek."
            :create="emptyProject"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <div class="grid gap-4 sm:grid-cols-2">
                    <FormField
                        label="Nama proyek"
                        :for="`projects-${index}-name`"
                        :error="editor.error(`projects.${index}.name`)"
                    >
                        <Input
                            :id="`projects-${index}-name`"
                            v-model="item.name"
                        />
                    </FormField>
                    <FormField
                        label="Peran"
                        optional
                        :for="`projects-${index}-role`"
                        :error="editor.error(`projects.${index}.role`)"
                    >
                        <Input
                            :id="`projects-${index}-role`"
                            v-model="item.role"
                            placeholder="mis. Pengembang utama"
                        />
                    </FormField>
                    <FormField
                        label="Tautan"
                        optional
                        :for="`projects-${index}-url`"
                        :error="editor.error(`projects.${index}.url`)"
                        class="sm:col-span-2"
                    >
                        <Input
                            :id="`projects-${index}-url`"
                            v-model="item.url"
                            placeholder="github.com/nama/proyek"
                            inputmode="url"
                        />
                    </FormField>
                </div>

                <PeriodFields
                    :id="`projects-${index}`"
                    :item="item"
                    :error-path="`projects.${index}`"
                />

                <HighlightsInput
                    :id="`projects-${index}-highlights`"
                    v-model="item.highlights"
                    :error-path="`projects.${index}.highlights`"
                    label="Apa yang Anda kerjakan dan hasilnya"
                >
                    <template v-if="editor.assistantEnabled" #actions>
                        <HighlightsAssistant
                            v-model="item.highlights"
                            :position="item.role"
                            :organization="item.name"
                        />
                    </template>
                </HighlightsInput>
            </template>
        </EntryList>
    </EditorSection>
</template>
