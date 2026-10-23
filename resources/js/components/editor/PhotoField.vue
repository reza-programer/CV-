<script setup lang="ts">
import { ImageUp, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { RequestError, requestJson } from '@/lib/http';
import { destroy, store } from '@/routes/resumes/photo';
import { useEditor } from './useEditorContext';

const MAX_BYTES = 5 * 1024 * 1024;
const ACCEPTED = ['image/jpeg', 'image/png', 'image/webp'];

const editor = useEditor();
const input = ref<HTMLInputElement | null>(null);
const busy = ref(false);
const error = ref('');

async function upload(event: Event): Promise<void> {
    const file = (event.target as HTMLInputElement).files?.[0];
    (event.target as HTMLInputElement).value = '';

    if (!file || editor.guest) {
        return;
    }

    if (!ACCEPTED.includes(file.type)) {
        error.value = 'Foto harus berformat JPG, PNG, atau WebP.';

        return;
    }

    if (file.size > MAX_BYTES) {
        error.value = 'Ukuran foto maksimal 5 MB.';

        return;
    }

    const form = new FormData();
    form.append('photo', file);
    await run(() =>
        requestJson<{ photo_url: string }>({
            method: 'post',
            url: store.url(editor.resumeId),
            data: form,
        }),
    );
}

/** Photos are stored with a CV, so visitors trying the editor sign up first. */
function choose(): void {
    if (editor.guest) {
        editor.requireAccount('photo');

        return;
    }

    input.value?.click();
}

async function remove(): Promise<void> {
    // The try-out editor only shows the sample photo, which lives in the page.
    if (editor.guest) {
        editor.resume.photo_url = null;

        return;
    }

    await run(() =>
        requestJson<{ photo_url: null }>({
            method: 'delete',
            url: destroy.url(editor.resumeId),
        }),
    );
}

async function run(
    request: () => Promise<{ photo_url: string | null }>,
): Promise<void> {
    busy.value = true;
    error.value = '';

    try {
        // Tracked so a PDF download started meanwhile waits for the new photo.
        const { photo_url } = await editor.track(request());
        editor.resume.photo_url = photo_url;
    } catch (exception) {
        const failure = exception as RequestError;
        error.value = failure.errors?.photo ?? failure.message;
        toast.error(error.value);
    } finally {
        busy.value = false;
    }
}
</script>

<template>
    <div class="flex items-start gap-4">
        <div
            class="flex h-[84px] w-[63px] shrink-0 items-center justify-center overflow-hidden rounded-sm border border-neutral-200 bg-neutral-50"
        >
            <img
                v-if="editor.resume.photo_url"
                :src="editor.resume.photo_url"
                alt="Foto profil"
                class="size-full object-cover"
            />
            <span
                v-else
                class="px-1 text-center text-[10px] leading-tight text-neutral-400"
                >3 × 4</span
            >
        </div>

        <div class="grid gap-2">
            <div>
                <p class="text-[13px] font-medium text-neutral-800">
                    Foto profil
                    <span class="font-normal text-neutral-400">(opsional)</span>
                </p>
                <p class="text-xs text-neutral-500">
                    JPG, PNG, atau WebP hingga 5 MB. Dipotong otomatis ke rasio
                    3:4. Kosongkan bila lowongan tidak memintanya.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <input
                    ref="input"
                    type="file"
                    class="sr-only"
                    :accept="ACCEPTED.join(',')"
                    tabindex="-1"
                    @change="upload"
                />
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="busy"
                    @click="choose"
                >
                    <Spinner v-if="busy" />
                    <ImageUp v-else />
                    {{ editor.resume.photo_url ? 'Ganti foto' : 'Unggah foto' }}
                </Button>
                <Button
                    v-if="editor.resume.photo_url"
                    variant="ghost"
                    size="sm"
                    :disabled="busy"
                    @click="remove"
                >
                    <Trash2 />
                    Hapus
                </Button>
            </div>
            <p v-if="error" class="text-xs text-red-600" role="alert">
                {{ error }}
            </p>
        </div>
    </div>
</template>
