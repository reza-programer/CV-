<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { CHOSEN_TEMPLATE_KEY } from '@/components/resumes/templateFilters';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { login, register } from '@/routes';
import type { AccountReason } from './useEditorContext';
import { showFirstError, useEditor } from './useEditorContext';

/**
 * Shown when a visitor in the try-out editor does something that needs an
 * account. The draft is saved to the session first, so the CV they typed is
 * waiting in the editor right after they sign up or log in.
 */
const reason = defineModel<AccountReason | null>({ required: true });

const props = defineProps<{
    /** Whether the session holds a draft, i.e. the visitor changed anything. */
    hasDraft: boolean;
}>();

const editor = useEditor();
const leaving = ref<'register' | 'login' | null>(null);

const COPY: Record<AccountReason, { title: string; text: string }> = {
    save: {
        title: 'Simpan CV ini ke akun',
        text: 'Daftar gratis untuk menyimpan CV, membukanya lagi kapan saja, dan mengunduh PDF.',
    },
    pdf: {
        title: 'Daftar untuk mengunduh PDF',
        text: 'PDF dibuat dari CV yang tersimpan di akun Anda. Pendaftaran gratis dan hanya butuh nama, email, dan kata sandi.',
    },
    photo: {
        title: 'Daftar untuk menambahkan foto',
        text: 'Foto disimpan bersama CV di akun Anda. Pendaftaran gratis dan hanya butuh nama, email, dan kata sandi.',
    },
};

const copy = computed(() => (reason.value ? COPY[reason.value] : null));

function onOpenChange(open: boolean): void {
    if (!open && !leaving.value) {
        reason.value = null;
    }
}

async function leave(target: 'register' | 'login'): Promise<void> {
    leaving.value = target;

    if (!(await editor.flush())) {
        leaving.value = null;
        reason.value = null;
        toast.error(
            editor.state.value === 'invalid'
                ? 'Perbaiki isian yang ditandai dulu agar semua isi ikut tersimpan.'
                : 'Draf belum tersimpan. Periksa koneksi lalu coba lagi.',
        );
        void showFirstError();

        return;
    }

    // Nothing was typed, so only the template choice carries over to the dashboard.
    if (!props.hasDraft) {
        try {
            sessionStorage.setItem(CHOSEN_TEMPLATE_KEY, editor.resume.template);
        } catch {
            // Storage can be unavailable (private mode); the dashboard then opens normally.
        }
    }

    router.visit(target === 'register' ? register() : login(), {
        onFinish: () => (leaving.value = null),
    });
}
</script>

<template>
    <Dialog :open="reason !== null" @update:open="onOpenChange">
        <DialogContent class="gap-0 p-0 sm:max-w-md">
            <div v-if="copy" class="grid gap-6 p-6 sm:p-8">
                <div>
                    <p class="eyebrow">Mode coba</p>
                    <DialogTitle
                        class="mt-2 font-display text-2xl font-medium tracking-tight text-ink"
                    >
                        {{ copy.title }}
                    </DialogTitle>
                    <DialogDescription
                        class="mt-3 text-sm leading-relaxed text-neutral-600"
                    >
                        {{ copy.text }}
                    </DialogDescription>
                </div>

                <p
                    class="border-l-2 border-ink-blue pl-3 text-sm leading-relaxed text-neutral-700"
                >
                    {{
                        hasDraft
                            ? 'Isi yang sudah Anda ketik langsung menjadi CV pertama di akun Anda. Tidak perlu mengetik ulang.'
                            : 'Template yang Anda pilih langsung disiapkan setelah mendaftar.'
                    }}
                </p>

                <div class="grid gap-2">
                    <Button
                        size="lg"
                        :disabled="leaving !== null"
                        @click="leave('register')"
                    >
                        <Spinner v-if="leaving === 'register'" />
                        Daftar gratis
                    </Button>
                    <Button
                        variant="ghost"
                        :disabled="leaving !== null"
                        @click="leave('login')"
                    >
                        <Spinner v-if="leaving === 'login'" />
                        Sudah punya akun? Masuk
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
