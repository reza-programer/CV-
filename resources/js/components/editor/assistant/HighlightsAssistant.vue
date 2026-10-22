<script setup lang="ts">
import { Sparkles } from '@lucide/vue';
import { computed, ref } from 'vue';
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
import { hasText } from '@/cv/format';
import AssistantNotice from './AssistantNotice.vue';
import JobDescriptionField from './JobDescriptionField.vue';
import { useAssistant } from './useAssistant';

const props = defineProps<{
    position: string;
    organization: string;
}>();

const highlights = defineModel<string[]>({ required: true });

const open = ref(false);
const draft = ref<string[]>([]);
const original = computed(() => highlights.value.filter(hasText));
const { jobDescription, loading, error, result, run, reset } = useAssistant<{
    highlights: string[];
    notes: string[];
}>('highlights');

async function generate(): Promise<void> {
    const response = await run({
        entry: {
            position: props.position,
            organization: props.organization,
            highlights: original.value,
        },
    });
    draft.value = [...(response?.highlights ?? [])];
}

function apply(): void {
    highlights.value = draft.value.map((line) => line.trim()).filter(Boolean);
    open.value = false;
}

function onOpenChange(value: boolean): void {
    open.value = value;

    if (!value) {
        reset();
        draft.value = [];
    }
}
</script>

<template>
    <Button
        variant="ghost"
        size="sm"
        class="h-7 px-2 text-xs"
        :disabled="original.length === 0"
        @click="open = true"
    >
        <Sparkles />
        Perbaiki poin
    </Button>

    <Dialog :open="open" @update:open="onOpenChange">
        <DialogContent class="max-h-[90svh] overflow-y-auto sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>Perbaiki poin pencapaian</DialogTitle>
                <DialogDescription>{{
                    [position, organization].filter(Boolean).join(' · ') ||
                    'Entri ini'
                }}</DialogDescription>
            </DialogHeader>

            <div class="grid gap-4">
                <AssistantNotice />
                <JobDescriptionField v-model="jobDescription" />

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid content-start gap-1.5">
                        <p class="text-[13px] font-medium text-neutral-800">
                            Tulisan Anda
                        </p>
                        <ul class="grid gap-1.5 text-sm text-neutral-700">
                            <li
                                v-for="(line, index) in original"
                                :key="index"
                                class="flex gap-2"
                            >
                                <span aria-hidden="true">•</span
                                ><span>{{ line }}</span>
                            </li>
                        </ul>
                    </div>

                    <div v-if="result" class="grid content-start gap-1.5">
                        <p class="text-[13px] font-medium text-neutral-800">
                            Saran (dapat disunting)
                        </p>
                        <textarea
                            v-for="(_, index) in draft"
                            :key="index"
                            v-model="draft[index]"
                            rows="2"
                            :aria-label="`Saran poin ${index + 1}`"
                            class="block field-sizing-content w-full resize-none rounded-md border border-input px-3 py-2 text-sm outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        />
                        <ul
                            v-if="result.notes.length"
                            class="grid gap-1 text-xs text-neutral-600"
                        >
                            <li v-for="note in result.notes" :key="note">
                                – {{ note }}
                            </li>
                        </ul>
                    </div>
                </div>

                <p v-if="error" class="text-sm text-red-600" role="alert">
                    {{ error }}
                </p>
            </div>

            <DialogFooter class="gap-2">
                <Button variant="outline" :disabled="loading" @click="generate">
                    <Spinner v-if="loading" />
                    {{ result ? 'Buat ulang' : 'Buat saran' }}
                </Button>
                <Button
                    :disabled="!result || draft.every((line) => !line.trim())"
                    @click="apply"
                    >Gunakan</Button
                >
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
