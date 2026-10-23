<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useEventListener } from '@vueuse/core';
import { computed, ref } from 'vue';
import EditorToolbar from '@/components/editor/EditorToolbar.vue';
import EditorWorkspace from '@/components/editor/EditorWorkspace.vue';
import GuestSignupDialog from '@/components/editor/GuestSignupDialog.vue';
import type { AccountReason } from '@/components/editor/useEditorContext';
import { provideEditor } from '@/components/editor/useEditorContext';
import { Button } from '@/components/ui/button';
import { useResumeEditor } from '@/composables/useResumeEditor';
import { sampleResume } from '@/cv/sample';
import type { ResumeData, TemplateSlug, TemplateSummary } from '@/cv/types';
import { update } from '@/routes/demo';

/** A draft as kept in the session: the editor payload without id and photo. */
type Draft = Omit<ResumeData, 'id' | 'photo_url'>;

const props = defineProps<{
    draft: Draft | null;
    templates: TemplateSummary[];
    /** Picked in the landing page gallery. */
    template: TemplateSlug | null;
}>();

const DEFAULT_TEMPLATE: TemplateSlug = 'professional';

/** Empty fields reach the session as null; the editor works with ''. */
function withoutNulls<T>(value: T): T {
    if (value === null) {
        return '' as T;
    }

    if (Array.isArray(value)) {
        return value.map(withoutNulls) as T;
    }

    if (typeof value === 'object') {
        return Object.fromEntries(
            Object.entries(value).map(([key, item]) => [
                key,
                withoutNulls(item),
            ]),
        ) as T;
    }

    return value;
}

// A returning visitor continues their draft; a new one starts from a sample.
const editor = useResumeEditor(
    props.draft
        ? { ...withoutNulls(props.draft), photo_url: null }
        : {
              ...sampleResume(props.template ?? DEFAULT_TEMPLATE),
              title: 'CV Saya',
          },
    null,
    update.url(),
);

// Applied as an edit, so the stored draft picks up the new template too.
if (props.draft && props.template) {
    editor.resume.template = props.template;
}

const prompt = ref<AccountReason | null>(null);

provideEditor({
    ...editor,
    assistantEnabled: false,
    guest: true,
    resumeId: null,
    requireAccount: (reason) => (prompt.value = reason),
});

const hasDraft = computed(
    () => props.draft !== null || editor.savedAt.value !== null,
);

function clearContent(): void {
    if (!window.confirm('Hapus semua isi contoh dan mulai dari kosong?')) {
        return;
    }

    Object.assign(editor.resume, {
        personal: {
            full_name: '',
            headline: '',
            email: '',
            phone: '',
            location: '',
            website: '',
            linkedin: '',
        },
        photo_url: null,
        summary: '',
        experiences: [],
        activities: [],
        educations: [],
        projects: [],
        skill_groups: [],
        certifications: [],
        languages: [],
    } satisfies Partial<ResumeData>);
}

useEventListener(window, 'keydown', (event: KeyboardEvent) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
        event.preventDefault();
        prompt.value = 'save';
    }
});
</script>

<template>
    <Head title="Coba editor CV" />

    <div class="flex min-h-svh flex-col bg-white lg:h-svh">
        <EditorToolbar :templates="templates" />

        <div
            class="flex shrink-0 flex-wrap items-center gap-x-4 gap-y-2 border-b border-neutral-200 bg-desk px-4 py-2.5"
        >
            <!-- The buttons drop below the text on narrow screens. -->
            <p
                class="min-w-0 grow basis-72 text-sm leading-relaxed text-neutral-700"
            >
                <span class="font-medium text-ink">Mode coba.</span>
                Ubah contoh ini sesuka Anda dan lihat hasilnya langsung. Daftar
                gratis untuk menyimpan dan mengunduh PDF; isinya ikut tersimpan.
            </p>
            <div class="flex shrink-0 items-center gap-2">
                <Button variant="ghost" size="sm" @click="clearContent">
                    Kosongkan isi
                </Button>
                <Button size="sm" @click="prompt = 'save'">
                    Simpan ke akun
                </Button>
            </div>
        </div>

        <EditorWorkspace />

        <GuestSignupDialog v-model="prompt" :has-draft="hasDraft" />
    </div>
</template>
