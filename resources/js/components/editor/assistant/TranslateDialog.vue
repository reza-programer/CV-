<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import type { ResumeLocale } from '@/cv/types';
import { RequestError, requestJson } from '@/lib/http';
import { translate } from '@/routes/resumes';
import { useEditor } from '../useEditorContext';
import type { TranslationItem } from './translation';
import {
    LOCALE_NAMES,
    applyTranslation,
    translationLabel,
    translationMaxLength,
} from './translation';

/**
 * Shown when the document language changes. Offers to translate the CV
 * content; every translation is listed for review and editing, and nothing
 * reaches the CV until "Terapkan terjemahan" writes it into the editor (which
 * then autosaves). Closing the dialog keeps the current language.
 */
const target = defineModel<ResumeLocale | null>('target', { required: true });

type Draft = TranslationItem & { label: string; max: number; text: string };

const editor = useEditor();
// Kept after closing so the title does not change during the close animation.
const locale = ref<ResumeLocale>('en');
const step = ref<'confirm' | 'review'>('confirm');
const loading = ref(false);
const error = ref('');
const drafts = ref<Draft[]>([]);
let pending: AbortController | null = null;

const localeName = computed(() => LOCALE_NAMES[locale.value]);

const problems = computed(() =>
    drafts.value.map((draft) => {
        const length = draft.text.trim().length;

        if (length === 0) {
            return 'Terjemahan tidak boleh kosong.';
        }

        return length > draft.max
            ? `Terlalu panjang: ${length} dari maksimal ${draft.max} karakter.`
            : '';
    }),
);

const canApply = computed(() => problems.value.every((problem) => !problem));

watch(target, (value) => {
    pending?.abort();
    pending = null;
    loading.value = false;

    if (value) {
        locale.value = value;
        step.value = 'confirm';
        error.value = '';
        drafts.value = [];
    }
});

function close(): void {
    target.value = null;
}

function onOpenChange(value: boolean): void {
    if (!value) {
        close();
    }
}

function switchOnly(): void {
    editor.resume.locale = locale.value;
    close();
}

async function translateContent(): Promise<void> {
    // Only offered on a stored CV; the try-out editor has none.
    if (editor.guest) {
        return;
    }

    const request = new AbortController();
    pending = request;
    loading.value = true;
    error.value = '';

    try {
        // The translation reads the stored CV, so persist pending edits first.
        if (!(await editor.flush())) {
            error.value =
                'Simpan CV terlebih dahulu: masih ada isian yang perlu diperbaiki.';

            return;
        }

        if (request.signal.aborted) {
            return;
        }

        const response = await requestJson<{ items: TranslationItem[] }>({
            method: 'post',
            url: translate.url(editor.resumeId),
            data: { target: locale.value },
            signal: request.signal,
        });

        if (request.signal.aborted) {
            return;
        }

        drafts.value = response.items.map((item) => ({
            ...item,
            label: translationLabel(editor.resume, item.path),
            max: translationMaxLength(item.path),
            text: item.translated,
        }));
        step.value = 'review';
    } catch (exception) {
        if (!request.signal.aborted) {
            const failure = exception as RequestError;
            error.value =
                Object.values(failure.errors ?? {})[0] ?? failure.message;
        }
    } finally {
        if (pending === request) {
            pending = null;
            loading.value = false;
        }
    }
}

function apply(): void {
    if (!canApply.value) {
        return;
    }

    const skipped = drafts.value.filter(
        (draft) => !applyTranslation(editor.resume, draft, draft.text.trim()),
    ).length;

    editor.resume.locale = locale.value;
    close();

    if (skipped > 0) {
        toast.warning(
            `${skipped} teks tidak diterapkan karena isinya sudah berubah. Periksa kembali bagian tersebut.`,
        );
    }
}
</script>

<template>
    <Dialog :open="target !== null" @update:open="onOpenChange">
        <DialogContent class="flex max-h-[90svh] flex-col sm:max-w-2xl">
            <template v-if="step === 'confirm'">
                <DialogHeader>
                    <DialogTitle
                        >Ganti bahasa dokumen ke {{ localeName }}</DialogTitle
                    >
                    <DialogDescription
                        >Terjemahkan juga isi CV?</DialogDescription
                    >
                </DialogHeader>

                <div
                    class="grid gap-3 text-sm leading-relaxed text-neutral-700"
                >
                    <p>
                        Judul bagian dan format tanggal langsung mengikuti
                        bahasa baru. Asisten AI dapat menerjemahkan isi yang
                        Anda tulis, seperti profil, posisi, poin pencapaian, dan
                        keahlian. Nama, perusahaan, institusi, lokasi, dan
                        tanggal tidak diubah.
                    </p>
                    <p>
                        Hasil terjemahan ditampilkan lebih dulu untuk Anda
                        periksa dan sunting sebelum diterapkan.
                    </p>
                    <p v-if="loading" class="text-xs text-neutral-500">
                        Menerjemahkan isi CV… Proses ini dapat memakan waktu
                        hingga satu menit.
                    </p>
                    <p v-if="error" class="text-red-600" role="alert">
                        {{ error }}
                    </p>
                </div>

                <DialogFooter class="gap-2">
                    <Button
                        variant="outline"
                        :disabled="loading"
                        @click="switchOnly"
                        >Ganti bahasa saja</Button
                    >
                    <Button :disabled="loading" @click="translateContent">
                        <Spinner v-if="loading" />
                        Terjemahkan isi
                    </Button>
                </DialogFooter>
            </template>

            <template v-else>
                <DialogHeader>
                    <DialogTitle>Periksa terjemahan</DialogTitle>
                    <DialogDescription>
                        {{
                            drafts.length
                                ? `${drafts.length} teks diterjemahkan ke ${localeName}. Sunting bila perlu, lalu terapkan.`
                                : `Bahasa dokumen akan diganti ke ${localeName}.`
                        }}
                    </DialogDescription>
                </DialogHeader>

                <p
                    class="rounded-md border border-neutral-200 bg-neutral-50 px-3 py-2 text-xs leading-relaxed text-neutral-600"
                >
                    Terjemahan hanya mengganti bahasa tulisan Anda dan tidak
                    menambahkan pengalaman, angka, atau keahlian baru. Periksa
                    dan sunting sebelum diterapkan; CV tidak berubah sampai Anda
                    menekan
                    <span class="font-medium text-neutral-800"
                        >Terapkan terjemahan</span
                    >.
                </p>

                <div class="-mx-6 min-h-0 flex-1 overflow-y-auto px-6">
                    <ol v-if="drafts.length" class="grid gap-4 pb-1">
                        <li
                            v-for="(draft, index) in drafts"
                            :key="draft.path"
                            class="grid gap-1.5"
                        >
                            <label
                                :for="`translation-${index}`"
                                class="text-[13px] font-medium text-neutral-800"
                                >{{ draft.label }}</label
                            >
                            <p
                                class="text-xs leading-relaxed whitespace-pre-line text-neutral-500"
                            >
                                {{ draft.original }}
                            </p>
                            <Textarea
                                :id="`translation-${index}`"
                                v-model="draft.text"
                                :maxlength="draft.max"
                                :aria-invalid="
                                    problems[index] ? true : undefined
                                "
                                class="min-h-9 text-sm"
                            />
                            <p
                                v-if="problems[index]"
                                class="text-xs text-red-600"
                            >
                                {{ problems[index] }}
                            </p>
                        </li>
                    </ol>
                    <p v-else class="text-sm text-neutral-600">
                        Belum ada isi CV yang perlu diterjemahkan.
                    </p>
                </div>

                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="close">Batal</Button>
                    <Button :disabled="!canApply" @click="apply">{{
                        drafts.length ? 'Terapkan terjemahan' : 'Ganti bahasa'
                    }}</Button>
                </DialogFooter>
            </template>
        </DialogContent>
    </Dialog>
</template>
