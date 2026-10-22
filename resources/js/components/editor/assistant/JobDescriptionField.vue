<script setup lang="ts">
import { Textarea } from '@/components/ui/textarea';

const model = defineModel<string>({ required: true });

defineProps<{ required?: boolean }>();
</script>

<template>
    <div class="grid gap-1.5">
        <label
            for="assistant-job"
            class="text-[13px] font-medium text-neutral-800"
        >
            Deskripsi lowongan
            <span v-if="!required" class="font-normal text-neutral-400"
                >(opsional)</span
            >
        </label>
        <Textarea
            id="assistant-job"
            v-model="model"
            maxlength="8000"
            rows="5"
            class="max-h-48 min-h-24 text-sm"
            placeholder="Tempel deskripsi pekerjaan dari iklan lowongan agar saran memakai istilah yang relevan."
        />
    </div>
</template>
