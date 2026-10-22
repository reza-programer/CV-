<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { emptySkillGroup } from '@/cv/factories';
import type { SkillGroupItem } from '@/cv/types';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

const summary = (item: SkillGroupItem) => ({
    title: item.category || item.skills.split(',')[0]?.trim() || '',
    subtitle: item.category ? item.skills : '',
});

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) =>
        key.startsWith('skill_groups.'),
    ),
);
</script>

<template>
    <EditorSection
        id="section-skills"
        title="Keahlian"
        description="Kelompokkan keahlian, mis. Teknis, Perangkat Lunak, Soft skill. Pisahkan dengan koma."
        :count="editor.resume.skill_groups.length"
        :has-error="hasError"
    >
        <EntryList
            list="skill_groups"
            :items="editor.resume.skill_groups"
            add-label="Tambah kelompok keahlian"
            empty-text="Belum ada keahlian."
            :create="emptySkillGroup"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <FormField
                    label="Kategori"
                    optional
                    :for="`skills-${index}-category`"
                    :error="editor.error(`skill_groups.${index}.category`)"
                    hint="Kosongkan untuk satu daftar tanpa judul."
                >
                    <Input
                        :id="`skills-${index}-category`"
                        v-model="item.category"
                        placeholder="mis. Perangkat lunak"
                    />
                </FormField>
                <FormField
                    label="Daftar keahlian"
                    :for="`skills-${index}-skills`"
                    :error="editor.error(`skill_groups.${index}.skills`)"
                >
                    <Textarea
                        :id="`skills-${index}-skills`"
                        v-model="item.skills"
                        rows="2"
                        maxlength="500"
                        class="min-h-16 text-sm"
                        placeholder="mis. Microsoft Excel, Analisis data, Komunikasi"
                    />
                </FormField>
            </template>
        </EntryList>
    </EditorSection>
</template>
