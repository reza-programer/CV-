<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import EditorSection from '../EditorSection.vue';
import FormField from '../FormField.vue';
import PhotoField from '../PhotoField.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();
const personal = editor.resume.personal;

const fields = [
    {
        key: 'full_name',
        label: 'Nama lengkap',
        placeholder: 'Nama sesuai identitas',
        autocomplete: 'name',
        wide: true,
    },
    {
        key: 'headline',
        label: 'Jabatan atau headline',
        placeholder: 'mis. Backend Engineer',
        autocomplete: 'organization-title',
        wide: true,
        optional: true,
    },
    {
        key: 'email',
        label: 'Email',
        placeholder: 'nama@email.com',
        autocomplete: 'email',
        type: 'email',
    },
    {
        key: 'phone',
        label: 'Telepon',
        placeholder: '+62 812 3456 7890',
        autocomplete: 'tel',
        type: 'tel',
    },
    {
        key: 'location',
        label: 'Domisili',
        placeholder: 'Kota, Provinsi',
        autocomplete: 'address-level2',
    },
    {
        key: 'linkedin',
        label: 'LinkedIn',
        placeholder: 'linkedin.com/in/nama',
        autocomplete: 'url',
        optional: true,
    },
    {
        key: 'website',
        label: 'Website atau portofolio',
        placeholder: 'github.com/nama',
        autocomplete: 'url',
        optional: true,
        wide: true,
    },
] as const;

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) => key.startsWith('personal.')),
);
</script>

<template>
    <EditorSection
        id="section-personal"
        title="Data diri dan kontak"
        :has-error="hasError"
    >
        <div class="grid gap-5">
            <PhotoField />

            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    v-for="field in fields"
                    :key="field.key"
                    :label="field.label"
                    :for="`personal-${field.key}`"
                    :optional="'optional' in field && field.optional"
                    :error="editor.error(`personal.${field.key}`)"
                    :class="{ 'sm:col-span-2': 'wide' in field && field.wide }"
                >
                    <Input
                        :id="`personal-${field.key}`"
                        v-model="personal[field.key]"
                        :type="'type' in field ? field.type : 'text'"
                        :autocomplete="field.autocomplete"
                        :placeholder="field.placeholder"
                        :aria-invalid="
                            editor.error(`personal.${field.key}`)
                                ? true
                                : undefined
                        "
                    />
                </FormField>
            </div>
        </div>
    </EditorSection>
</template>
