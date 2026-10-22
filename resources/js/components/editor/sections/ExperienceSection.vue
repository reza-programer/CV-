<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { emptyActivity, emptyExperience } from '@/cv/factories';
import { formatRange, joinParts } from '@/cv/format';
import type { ActivityItem, ExperienceItem } from '@/cv/types';
import HighlightsAssistant from '../assistant/HighlightsAssistant.vue';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import HighlightsInput from '../HighlightsInput.vue';
import NativeSelect from '../NativeSelect.vue';
import PeriodFields from '../PeriodFields.vue';
import { useEditor } from '../useEditorContext';

/**
 * Work experience, or internships and organisational roles ("activities").
 * Both share one form; activities add a type that decides which CV section
 * the entry appears under.
 */
const props = defineProps<{ list: 'experiences' | 'activities' }>();

const editor = useEditor();
const isActivities = computed(() => props.list === 'activities');
const items = computed(
    () => editor.resume[props.list] as Array<ExperienceItem | ActivityItem>,
);

const typeOptions = [
    { value: 'internship', label: 'Magang' },
    { value: 'organization', label: 'Organisasi / kepanitiaan / relawan' },
];

const copy = computed(() =>
    isActivities.value
        ? {
              id: 'section-activities',
              title: 'Magang dan organisasi',
              description:
                  'Tampil sebagai bagian "Pengalaman Magang" dan "Pengalaman Organisasi" pada CV.',
              add: 'Tambah magang atau organisasi',
              empty: 'Belum ada pengalaman magang atau organisasi.',
              position: 'Peran atau jabatan',
              positionPlaceholder: 'mis. Ketua Divisi Acara',
              organization: 'Perusahaan atau organisasi',
          }
        : {
              id: 'section-experience',
              title: 'Pengalaman kerja',
              description: 'Urutkan dari yang terbaru.',
              add: 'Tambah pengalaman kerja',
              empty: 'Belum ada pengalaman kerja.',
              position: 'Posisi',
              positionPlaceholder: 'mis. Staf Administrasi',
              organization: 'Perusahaan',
          },
);

const summary = (item: ExperienceItem | ActivityItem) => ({
    title: joinParts([item.position, item.organization], ' · '),
    subtitle: joinParts(
        [
            'type' in item
                ? item.type === 'internship'
                    ? 'Magang'
                    : 'Organisasi'
                : '',
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

const create = () => (isActivities.value ? emptyActivity() : emptyExperience());

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) =>
        key.startsWith(`${props.list}.`),
    ),
);
</script>

<template>
    <EditorSection
        :id="copy.id"
        :title="copy.title"
        :description="copy.description"
        :count="items.length"
        :has-error="hasError"
    >
        <EntryList
            :list="list"
            :items="items"
            :add-label="copy.add"
            :empty-text="copy.empty"
            :create="create"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <FormField
                    v-if="'type' in item"
                    label="Jenis"
                    :for="`${list}-${index}-type`"
                    :error="editor.error(`${list}.${index}.type`)"
                >
                    <NativeSelect
                        :id="`${list}-${index}-type`"
                        v-model="(item as ActivityItem).type"
                        :options="typeOptions"
                    />
                </FormField>

                <div class="grid gap-4 sm:grid-cols-2">
                    <FormField
                        :label="copy.position"
                        :for="`${list}-${index}-position`"
                        :error="editor.error(`${list}.${index}.position`)"
                    >
                        <Input
                            :id="`${list}-${index}-position`"
                            v-model="item.position"
                            :placeholder="copy.positionPlaceholder"
                            :aria-invalid="
                                editor.error(`${list}.${index}.position`)
                                    ? true
                                    : undefined
                            "
                        />
                    </FormField>
                    <FormField
                        :label="copy.organization"
                        :for="`${list}-${index}-organization`"
                        :error="editor.error(`${list}.${index}.organization`)"
                    >
                        <Input
                            :id="`${list}-${index}-organization`"
                            v-model="item.organization"
                        />
                    </FormField>
                    <FormField
                        label="Lokasi"
                        optional
                        :for="`${list}-${index}-location`"
                        :error="editor.error(`${list}.${index}.location`)"
                        class="sm:col-span-2"
                    >
                        <Input
                            :id="`${list}-${index}-location`"
                            v-model="item.location"
                            placeholder="Kota atau Remote"
                        />
                    </FormField>
                </div>

                <PeriodFields
                    :id="`${list}-${index}`"
                    :item="item"
                    :error-path="`${list}.${index}`"
                    :current-label="
                        isActivities ? 'Masih aktif' : 'Masih bekerja di sini'
                    "
                />

                <HighlightsInput
                    :id="`${list}-${index}-highlights`"
                    v-model="item.highlights"
                    :error-path="`${list}.${index}.highlights`"
                >
                    <template v-if="editor.assistantEnabled" #actions>
                        <HighlightsAssistant
                            v-model="item.highlights"
                            :position="item.position"
                            :organization="item.organization"
                        />
                    </template>
                </HighlightsInput>
            </template>
        </EntryList>
    </EditorSection>
</template>
