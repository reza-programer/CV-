<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { emptyEducation } from '@/cv/factories';
import { formatRange, joinParts } from '@/cv/format';
import type { EducationItem } from '@/cv/types';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import HighlightsInput from '../HighlightsInput.vue';
import PeriodFields from '../PeriodFields.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

const summary = (item: EducationItem) => ({
    title: joinParts(
        [item.institution, joinParts([item.degree, item.field_of_study], ' ')],
        ' · ',
    ),
    subtitle: formatRange(
        item.start_date,
        item.end_date,
        item.is_current,
        editor.resume.locale,
    ),
});

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) =>
        key.startsWith('educations.'),
    ),
);
</script>

<template>
    <EditorSection
        id="section-education"
        title="Pendidikan"
        description="Mulai dari jenjang tertinggi atau terbaru."
        :count="editor.resume.educations.length"
        :has-error="hasError"
    >
        <EntryList
            list="educations"
            :items="editor.resume.educations"
            add-label="Tambah pendidikan"
            empty-text="Belum ada riwayat pendidikan."
            :create="emptyEducation"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <div class="grid gap-4 sm:grid-cols-2">
                    <FormField
                        label="Institusi"
                        :for="`educations-${index}-institution`"
                        :error="editor.error(`educations.${index}.institution`)"
                        class="sm:col-span-2"
                    >
                        <Input
                            :id="`educations-${index}-institution`"
                            v-model="item.institution"
                            placeholder="mis. Universitas Gadjah Mada"
                        />
                    </FormField>
                    <FormField
                        label="Jenjang atau gelar"
                        :for="`educations-${index}-degree`"
                        :error="editor.error(`educations.${index}.degree`)"
                    >
                        <Input
                            :id="`educations-${index}-degree`"
                            v-model="item.degree"
                            placeholder="mis. S1, D3, SMA"
                        />
                    </FormField>
                    <FormField
                        label="Jurusan"
                        optional
                        :for="`educations-${index}-field`"
                        :error="
                            editor.error(`educations.${index}.field_of_study`)
                        "
                    >
                        <Input
                            :id="`educations-${index}-field`"
                            v-model="item.field_of_study"
                            placeholder="mis. Teknik Informatika"
                        />
                    </FormField>
                    <FormField
                        label="Lokasi"
                        optional
                        :for="`educations-${index}-location`"
                        :error="editor.error(`educations.${index}.location`)"
                    >
                        <Input
                            :id="`educations-${index}-location`"
                            v-model="item.location"
                        />
                    </FormField>
                    <FormField
                        label="IPK / nilai"
                        optional
                        :for="`educations-${index}-gpa`"
                        hint="Tulis lengkap, mis. 3,65/4,00"
                        :error="editor.error(`educations.${index}.gpa`)"
                    >
                        <Input
                            :id="`educations-${index}-gpa`"
                            v-model="item.gpa"
                            maxlength="20"
                        />
                    </FormField>
                </div>

                <PeriodFields
                    :id="`educations-${index}`"
                    :item="item"
                    :error-path="`educations.${index}`"
                    current-label="Masih menempuh"
                />

                <HighlightsInput
                    :id="`educations-${index}-highlights`"
                    v-model="item.highlights"
                    :error-path="`educations.${index}.highlights`"
                    label="Prestasi, skripsi, atau kegiatan"
                    placeholder="mis. Skripsi: Sistem rekomendasi berbasis …"
                    :max="6"
                />
            </template>
        </EntryList>
    </EditorSection>
</template>
