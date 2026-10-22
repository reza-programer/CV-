<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { emptyLanguage } from '@/cv/factories';
import type { LanguageItem } from '@/cv/types';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

const summary = (item: LanguageItem) => ({
    title: item.name,
    subtitle: item.proficiency,
});

const levels = computed(() =>
    editor.resume.locale === 'en'
        ? [
              'Native',
              'Fluent',
              'Professional working proficiency',
              'Intermediate',
              'Basic',
          ]
        : ['Penutur asli', 'Fasih', 'Profesional', 'Menengah', 'Dasar'],
);

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) =>
        key.startsWith('languages.'),
    ),
);
</script>

<template>
    <EditorSection
        id="section-languages"
        title="Bahasa"
        :count="editor.resume.languages.length"
        :has-error="hasError"
    >
        <datalist id="language-levels">
            <option v-for="level in levels" :key="level" :value="level" />
        </datalist>

        <EntryList
            list="languages"
            :items="editor.resume.languages"
            add-label="Tambah bahasa"
            empty-text="Belum ada bahasa."
            :create="emptyLanguage"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <div class="grid gap-4 sm:grid-cols-2">
                    <FormField
                        label="Bahasa"
                        :for="`languages-${index}-name`"
                        :error="editor.error(`languages.${index}.name`)"
                    >
                        <Input
                            :id="`languages-${index}-name`"
                            v-model="item.name"
                            placeholder="mis. Inggris"
                        />
                    </FormField>
                    <FormField
                        label="Tingkat kemahiran"
                        optional
                        :for="`languages-${index}-level`"
                        :error="editor.error(`languages.${index}.proficiency`)"
                        hint="Boleh disertai skor, mis. TOEFL ITP 550"
                    >
                        <Input
                            :id="`languages-${index}-level`"
                            v-model="item.proficiency"
                            list="language-levels"
                        />
                    </FormField>
                </div>
            </template>
        </EntryList>
    </EditorSection>
</template>
