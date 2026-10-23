<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import FormField from '@/components/editor/FormField.vue';
import type { ResumeLocale, TemplateSlug, TemplateSummary } from '@/cv/types';
import { store } from '@/routes/resumes';
import TemplatePicker from './TemplatePicker.vue';

const open = defineModel<boolean>('open', { required: true });

const props = defineProps<{
    templates: TemplateSummary[];
    /** Preselected template, e.g. one chosen on the landing page. */
    template?: TemplateSlug | null;
}>();

const form = useForm<{
    title: string;
    template: TemplateSlug;
    locale: ResumeLocale;
}>({
    title: '',
    template: props.templates[0]?.slug ?? 'classic',
    locale: 'id',
});

watch(open, (isOpen) => {
    if (isOpen && props.template) {
        form.template = props.template;
    }
});

function submit(): void {
    form.submit(store(), {
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[92svh] overflow-y-auto sm:max-w-4xl">
            <DialogHeader>
                <DialogTitle
                    class="font-display text-2xl font-medium tracking-tight"
                    >Buat CV baru</DialogTitle
                >
                <DialogDescription>
                    Nama dan email akun Anda diisikan otomatis. Template dan
                    bahasa dapat diganti kapan saja tanpa kehilangan isi.
                </DialogDescription>
            </DialogHeader>

            <form id="new-resume" class="grid gap-5" @submit.prevent="submit">
                <FormField
                    label="Judul CV"
                    for="new-title"
                    optional
                    hint="Hanya untuk Anda, tidak tercetak. Mis. CV untuk posisi analis."
                    :error="form.errors.title"
                >
                    <Input
                        id="new-title"
                        v-model="form.title"
                        maxlength="120"
                        placeholder="CV Baru"
                    />
                </FormField>

                <fieldset class="grid gap-2">
                    <legend
                        class="mb-1.5 text-[13px] font-medium text-neutral-800"
                    >
                        Bahasa dokumen
                    </legend>
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.locale"
                                type="radio"
                                value="id"
                                class="accent-neutral-900"
                            />
                            Bahasa Indonesia
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.locale"
                                type="radio"
                                value="en"
                                class="accent-neutral-900"
                            />
                            English
                        </label>
                    </div>
                    <p class="text-xs text-neutral-500">
                        Mengatur judul bagian dan format tanggal pada CV.
                    </p>
                </fieldset>

                <fieldset class="grid gap-2">
                    <legend
                        class="mb-1.5 text-[13px] font-medium text-neutral-800"
                    >
                        Template
                    </legend>
                    <TemplatePicker
                        v-model="form.template"
                        :templates="templates"
                    />
                    <p v-if="form.errors.template" class="text-xs text-red-600">
                        {{ form.errors.template }}
                    </p>
                </fieldset>
            </form>

            <DialogFooter class="gap-2">
                <Button variant="outline" type="button" @click="open = false"
                    >Batal</Button
                >
                <Button
                    type="submit"
                    form="new-resume"
                    :disabled="form.processing"
                >
                    <Spinner v-if="form.processing" />
                    Buat dan mulai mengisi
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
