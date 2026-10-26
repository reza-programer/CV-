<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, Check, Download } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { ResumeLocale, TemplateSummary } from '@/cv/types';
import { dashboard, home } from '@/routes';
import { pdf } from '@/routes/resumes';
import KeywordAssistant from './assistant/KeywordAssistant.vue';
import TranslateDialog from './assistant/TranslateDialog.vue';
import { isColoured, isMultiColumn } from '@/cv/templates';
import AccentColorPicker from './AccentColorPicker.vue';
import NativeSelect from './NativeSelect.vue';
import ParagraphAlignToggle from './ParagraphAlignToggle.vue';
import { showFirstError, useEditor } from './useEditorContext';

const props = defineProps<{ templates: TemplateSummary[] }>();

const editor = useEditor();
const downloading = ref(false);

const templateOptions = computed(() => {
    const options = props.templates.map((template) => {
        const tags = [
            isMultiColumn(template.slug) && !/kreatif/i.test(template.name)
                ? 'kreatif'
                : '',
            isColoured(template.slug) ? 'berwarna' : '',
        ].filter(Boolean);

        return {
            value: template.slug,
            label: tags.length
                ? `${template.name} · ${tags.join(', ')}`
                : template.name,
        };
    });

    // Keep a template the admin has since deactivated selectable for this CV.
    if (!options.some((option) => option.value === editor.resume.template)) {
        options.unshift({
            value: editor.resume.template,
            label: editor.resume.template,
        });
    }

    return options;
});

const localeOptions: Array<{ value: ResumeLocale; label: string }> = [
    { value: 'id', label: 'Indonesia' },
    { value: 'en', label: 'English' },
];

/** Language chosen in the select while the translation dialog is open. */
const pendingLocale = ref<ResumeLocale | null>(null);

function chooseLocale(value: string): void {
    const locale = localeOptions.find((option) => option.value === value);

    if (!locale || locale.value === editor.resume.locale) {
        return;
    }

    if (editor.assistantEnabled) {
        pendingLocale.value = locale.value;

        return;
    }

    editor.resume.locale = locale.value;
    toast.info(
        'Bahasa dokumen diganti. Isi CV tidak diterjemahkan otomatis; terjemahan tersedia bila asisten AI (ANTHROPIC_API_KEY) diaktifkan.',
    );
}

const errorCount = computed(() => Object.keys(editor.errors.value).length);

const savedTime = computed(() =>
    editor.savedAt.value
        ? new Intl.DateTimeFormat('id-ID', {
              hour: '2-digit',
              minute: '2-digit',
          }).format(new Date(editor.savedAt.value))
        : '',
);

function filenameFrom(header: string | null): string {
    const encoded = header?.match(/filename\*=UTF-8''([^;]+)/i)?.[1];

    if (encoded) {
        return decodeURIComponent(encoded);
    }

    return header?.match(/filename="([^"]+)"/i)?.[1] ?? 'CV.pdf';
}

async function download(): Promise<void> {
    // PDFs are rendered from a stored CV.
    if (editor.guest) {
        editor.requireAccount('pdf');

        return;
    }

    downloading.value = true;

    try {
        if (!(await editor.flush())) {
            toast.error(
                editor.state.value === 'invalid'
                    ? 'Perbaiki isian yang ditandai sebelum mengunduh PDF.'
                    : 'CV belum tersimpan. Periksa koneksi lalu coba lagi.',
            );
            void showFirstError();

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
            :href="editor.guest ? home() : dashboard()"
            class="flex items-center gap-1.5 rounded-md px-2 py-1.5 text-sm text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900"
        >
            <ArrowLeft class="size-4" />
            <span class="hidden sm:inline">{{
                editor.guest ? 'Beranda' : 'CV Saya'
            }}</span>
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
            <span
                v-else-if="editor.guest"
                class="text-neutral-500"
                title="Disimpan sementara selama sesi ini. Daftar untuk menyimpannya ke akun."
                >Draf sementara</span
            >
            <span v-else class="flex items-center gap-1 text-neutral-500">
                <Check class="size-3.5" />
                {{ savedTime ? `Tersimpan ${savedTime}` : 'Tersimpan' }}
            </span>
        </div>

        <div
            class="flex w-full flex-wrap items-center gap-2 lg:w-auto lg:flex-nowrap"
        >
            <NativeSelect
                v-model="editor.resume.template"
                :options="templateOptions"
                aria-label="Template"
                class="min-w-40 flex-1 lg:w-60 lg:flex-none"
            />
            <!-- Shows the pending choice while the dialog is open and the document language again once it closes. -->
            <NativeSelect
                :model-value="pendingLocale ?? editor.resume.locale"
                :options="localeOptions"
                aria-label="Bahasa dokumen"
                title="Bahasa judul bagian dan tanggal pada dokumen"
                class="min-w-28 flex-1 lg:w-32 lg:flex-none"
                @update:model-value="chooseLocale"
            />
            <TranslateDialog
                v-if="editor.assistantEnabled"
                v-model:target="pendingLocale"
            />
            <AccentColorPicker
                v-if="isColoured(editor.resume.template)"
                v-model="editor.resume.accent_color"
            />
            <ParagraphAlignToggle v-model="editor.resume.paragraph_align" />
            <KeywordAssistant v-if="editor.assistantEnabled" />
            <Button :disabled="downloading" class="shrink-0" @click="download">
                <Spinner v-if="downloading" />
                <Download v-else />
                <span>Unduh PDF</span>
            </Button>
        </div>
    </header>
</template>
