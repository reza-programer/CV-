<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { emptyCertification } from '@/cv/factories';
import { formatDate, joinParts } from '@/cv/format';
import type { CertificationItem } from '@/cv/types';
import EditorSection from '../EditorSection.vue';
import EntryList from '../EntryList.vue';
import FormField from '../FormField.vue';
import PartialDateInput from '../PartialDateInput.vue';
import { useEditor } from '../useEditorContext';

const editor = useEditor();

const summary = (item: CertificationItem) => ({
    title: item.name,
    subtitle: joinParts(
        [item.issuer, formatDate(item.issued_at, editor.resume.locale)],
        ' · ',
    ),
});

const hasError = computed(() =>
    Object.keys(editor.errors.value).some((key) =>
        key.startsWith('certifications.'),
    ),
);
</script>

<template>
    <EditorSection
        id="section-certifications"
        title="Sertifikasi"
        :count="editor.resume.certifications.length"
        :has-error="hasError"
    >
        <EntryList
            list="certifications"
            :items="editor.resume.certifications"
            add-label="Tambah sertifikasi"
            empty-text="Belum ada sertifikasi."
            :create="emptyCertification"
            :summary="summary"
        >
            <template #item="{ item, index }">
                <div class="grid gap-4 sm:grid-cols-2">
                    <FormField
                        label="Nama sertifikasi"
                        :for="`certifications-${index}-name`"
                        :error="editor.error(`certifications.${index}.name`)"
                        class="sm:col-span-2"
                    >
                        <Input
                            :id="`certifications-${index}-name`"
                            v-model="item.name"
                        />
                    </FormField>
                    <FormField
                        label="Penerbit"
                        :for="`certifications-${index}-issuer`"
                        :error="editor.error(`certifications.${index}.issuer`)"
                    >
                        <Input
                            :id="`certifications-${index}-issuer`"
                            v-model="item.issuer"
                            placeholder="mis. BNSP, Google, Dicoding"
                        />
                    </FormField>
                    <FormField
                        label="ID kredensial"
                        optional
                        :for="`certifications-${index}-credential`"
                        :error="
                            editor.error(
                                `certifications.${index}.credential_id`,
                            )
                        "
                    >
                        <Input
                            :id="`certifications-${index}-credential`"
                            v-model="item.credential_id"
                        />
                    </FormField>
                    <FormField
                        label="Tanggal terbit"
                        :for="`certifications-${index}-issued`"
                        :error="
                            editor.error(`certifications.${index}.issued_at`)
                        "
                    >
                        <PartialDateInput
                            :id="`certifications-${index}-issued`"
                            v-model="item.issued_at"
                        />
                    </FormField>
                    <FormField
                        label="Berlaku hingga"
                        optional
                        :for="`certifications-${index}-expires`"
                        :error="
                            editor.error(`certifications.${index}.expires_at`)
                        "
                    >
                        <PartialDateInput
                            :id="`certifications-${index}-expires`"
                            v-model="item.expires_at"
                        />
                    </FormField>
                    <FormField
                        label="Tautan verifikasi"
                        optional
                        :for="`certifications-${index}-url`"
                        :error="editor.error(`certifications.${index}.url`)"
                        class="sm:col-span-2"
                    >
                        <Input
                            :id="`certifications-${index}-url`"
                            v-model="item.url"
                            inputmode="url"
                        />
                    </FormField>
                </div>
            </template>
        </EntryList>
    </EditorSection>
</template>
