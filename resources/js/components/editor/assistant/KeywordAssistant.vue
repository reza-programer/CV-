<script setup lang="ts">
import { ScanSearch } from '@lucide/vue';
import { ref } from 'vue';
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
import JobDescriptionField from './JobDescriptionField.vue';
import { useAssistant } from './useAssistant';

type KeywordReport = {
    matched: string[];
    missing: string[];
    suggestions: string[];
};

const open = ref(false);
const { jobDescription, loading, error, result, run, reset } =
    useAssistant<KeywordReport>('keywords');

function onOpenChange(value: boolean): void {
    open.value = value;

    if (!value) {
        reset();
    }
}
</script>

<template>
    <Button variant="ghost" size="sm" @click="open = true">
        <ScanSearch />
        <span class="hidden xl:inline">Cocokkan lowongan</span>
    </Button>

    <Dialog :open="open" @update:open="onOpenChange">
        <DialogContent class="max-h-[90svh] overflow-y-auto sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>Cocokkan dengan lowongan</DialogTitle>
                <DialogDescription>
                    Bandingkan kata kunci lowongan dengan isi CV. Hasilnya hanya
                    laporan; CV tidak diubah.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-5">
                <JobDescriptionField v-model="jobDescription" required />

                <template v-if="result">
                    <div class="grid gap-2">
                        <h3 class="text-sm font-semibold">
                            Sudah tercermin di CV
                        </h3>
                        <p
                            v-if="!result.matched.length"
                            class="text-sm text-neutral-500"
                        >
                            Belum ada kata kunci yang cocok.
                        </p>
                        <ul class="flex flex-wrap gap-1.5">
                            <li
                                v-for="word in result.matched"
                                :key="word"
                                class="rounded border border-neutral-300 px-2 py-0.5 text-xs"
                            >
                                {{ word }}
                            </li>
                        </ul>
                    </div>

                    <div class="grid gap-2">
                        <h3 class="text-sm font-semibold">
                            Belum disebut di CV
                        </h3>
                        <p class="text-xs text-neutral-500">
                            Tambahkan hanya yang benar-benar Anda kuasai atau
                            pernah kerjakan.
                        </p>
                        <ul class="flex flex-wrap gap-1.5">
                            <li
                                v-for="word in result.missing"
                                :key="word"
                                class="rounded border border-dashed border-neutral-400 px-2 py-0.5 text-xs text-neutral-700"
                            >
                                {{ word }}
                            </li>
                        </ul>
                    </div>

                    <div v-if="result.suggestions.length" class="grid gap-2">
                        <h3 class="text-sm font-semibold">Saran penyesuaian</h3>
                        <ol
                            class="grid list-decimal gap-1.5 pl-5 text-sm text-neutral-700"
                        >
                            <li
                                v-for="suggestion in result.suggestions"
                                :key="suggestion"
                            >
                                {{ suggestion }}
                            </li>
                        </ol>
                    </div>
                </template>

                <p v-if="error" class="text-sm text-red-600" role="alert">
                    {{ error }}
                </p>
            </div>

            <DialogFooter>
                <Button
                    :disabled="loading || !jobDescription.trim()"
                    @click="run()"
                >
                    <Spinner v-if="loading" />
                    {{ result ? 'Analisis ulang' : 'Analisis' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
