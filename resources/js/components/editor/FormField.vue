<script setup lang="ts">
import { Label } from '@/components/ui/label';

defineProps<{
    label: string;
    for?: string;
    error?: string;
    hint?: string;
    optional?: boolean;
}>();
</script>

<template>
    <div class="grid content-start gap-1.5">
        <Label
            :for="$props.for"
            class="text-[13px] font-medium text-neutral-800"
        >
            {{ label }}
            <span v-if="optional" class="font-normal text-neutral-400"
                >(opsional)</span
            >
        </Label>
        <slot />
        <p v-if="error" class="text-xs text-red-600" role="alert">
            {{ error }}
        </p>
        <p v-else-if="hint" class="text-xs text-neutral-500">{{ hint }}</p>
    </div>
</template>
