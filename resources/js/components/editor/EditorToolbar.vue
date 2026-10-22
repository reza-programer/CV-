<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, Check, Download } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { TemplateSummary } from '@/cv/types';
import { dashboard } from '@/routes';
import { pdf } from '@/routes/resumes';
import KeywordAssistant from './assistant/KeywordAssistant.vue';
import NativeSelect from './NativeSelect.vue';
import { useEditor } from './useEditorContext';

const props = defineProps<{ templates: TemplateSummary[] }>();

const editor = useEditor();
const downloading = ref(false);

const templateOptions = computed(() => {
    const options = props.templates.map((template) => ({
        value: template.slug,
        label: template.name,
    }));

    // Keep a template the admin has since deactivated selectable for this CV.
    if (!options.some((option) => option.value === editor.resume.template)) {
        options.unshift({
            value: editor.resume.template,
            label: editor.resume.template,
        });
    }

    return options;
});

const localeOptions = [
    { value: 'id', label: 'Bahasa Indonesia' },
    { value: 'en', label: 'English' },
];

const errorCount = computed(() => Object.keys(editor.errors.value).length);

const savedTime = computed(() =>
    editor.savedAt.value
        ? new Intl.DateTimeFormat('id-ID', {
              hour: '2-digit',
              minute: '2-digit',
          }).format(new Date(editor.savedAt.value))
        : '',
);

function showFirstError(): void {
    const field = document.querySelector<HTMLElement>('[aria-invalid="true"]');
    field?.scrollIntoView({ block: 'center', behavior: 'smooth' });
    field?.focus({ preventScroll: true });
}

function filenameFrom(header: string | null): string {
    const encoded = header?.match(/filename\*=UTF-8''([^;]+)/i)?.[1];

    if (encoded) {
        return decodeURIComponent(encoded);
    }

    return header?.match(/filename="([^"]+)"/i)?.[1] ?? 'CV.pdf';
}

async function download(): Promise<void> {
    downloading.value = true;

    try {
        if (!(await editor.flush())) {
            toast.error(
                editor.state.value === 'invalid'
                    ? 'Perbaiki isian yang ditandai sebelum mengunduh PDF.'
                    : 'CV belum tersimpan. Periksa koneksi lalu coba lagi.',
            );
            showFirstError();

            return;
        }

        const response = await fetch(pdf.url(editor.resumeId), {
            headers: { Accept: 'application/pdf' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            toast.error(
                response.status === 429
                    ? 'Terlalu banyak unduhan dalam waktu singkat. Tunggu sebentar.'
                    : 'PDF belum dapat dibuat. Silakan coba lagi.',
            );

            return;
        }

        const url = URL.createObjectURL(await response.blob());
        const link = document.createElement('a');
        link.href = url;
        link.download = filenameFrom(
            response.headers.get('Content-Disposition'),
        );
        link.click();
        window.setTimeout(() => URL.revokeObjectURL(url), 10_000);
    } catch {
        toast.error('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
    } finally {
        downloading.value = false;
    }
}
</script>

<template>
    <header
        class="z-20 flex shrink-0 flex-wrap items-center gap-x-3 gap-y-2 border-b border-neutral-200 bg-white px-3 py-2 lg:h-14 lg:flex-nowrap lg:py-0"
    >
        <Link
            :href="dashboard()"
            class="flex items-center gap-1.5 rounded-md px-2 py-1.5 text-sm text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900"
        >
            <ArrowLeft class="size-4" />
            <span class="hidden sm:inline">CV Saya</span>
        </Link>

        <div class="h-5 w-px bg-neutral-200" />

        <div class="min-w-0 flex-1">
            <input
                v-model="editor.resume.title"
                maxlength="120"
                aria-label="Judul CV"
                :aria-invalid="editor.error('title') ? true : undefined"
                class="w-full min-w-0 truncate rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-semibold text-neutral-900 outline-none hover:border-neutral-200 focus:border-neutral-300 aria-invalid:border-red-400"
                placeholder="Judul CV"
            />
        </div>

        <div class="flex shrink-0 items-center text-xs" aria-live="polite">
            <span
                v-if="editor.state.value === 'saving'"
                class="flex items-center gap-1.5 text-neutral-500"
            >
                <Spinner class="size-3.5" />
                Menyimpan…
            </span>
            <span
                v-else-if="editor.state.value === 'pending'"
                class="text-neutral-500"
                >Perubahan belum tersimpan</span
            >
            <button
                v-else-if="editor.state.value === 'invalid'"
                type="button"
                class="flex items-center gap-1.5 rounded px-1.5 py-1 text-red-600 hover:bg-red-50"
                @click="showFirstError"
            >
                <AlertCircle class="size-3.5" />
                Perbaiki {{ errorCount }} isian
            </button>
            <button
                v-else-if="editor.state.value === 'failed'"
                type="button"
                class="flex items-center gap-1.5 rounded px-1.5 py-1 text-red-600 hover:bg-red-50"
                :title="editor.failure.value"
                @click="editor.save()"
            >
                <AlertCircle class="size-3.5" />
                Gagal menyimpan · Coba lagi
            </button>
            <span v-else class="flex items-center gap-1 text-neutral-500">
                <Check class="size-3.5" />
                {{ savedTime ? `Tersimpan ${savedTime}` : 'Tersimpan' }}
            </span>
        </div>

        <div class="flex w-full items-center gap-2 lg:w-auto">
            <NativeSelect
                v-model="editor.resume.template"
                :options="templateOptions"
                aria-label="Template"
                class="min-w-0 flex-1 lg:w-44 lg:flex-none"
            />
            <NativeSelect
                v-model="editor.resume.locale"
                :options="localeOptions"
                aria-label="Bahasa dokumen"
                title="Bahasa judul bagian dan tanggal pada dokumen"
                class="min-w-0 flex-1 lg:w-40 lg:flex-none"
            />
            <KeywordAssistant v-if="editor.assistantEnabled" />
            <Button :disabled="downloading" class="shrink-0" @click="download">
                <Spinner v-if="downloading" />
                <Download v-else />
                <span>Unduh PDF</span>
            </Button>
        </div>
    </header>
</template>
