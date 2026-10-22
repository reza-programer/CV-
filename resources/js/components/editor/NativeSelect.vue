<script setup lang="ts">
import { ChevronDown } from '@lucide/vue';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

/**
 * Native <select> styled like the shadcn inputs. Used where the platform
 * picker is the better control (month/year on phones, short option lists).
 */
defineOptions({ inheritAttrs: false });

const model = defineModel<string>({ required: true });

defineProps<{
    options: Array<{ value: string; label: string }>;
    class?: HTMLAttributes['class'];
    invalid?: boolean;
}>();
</script>

<template>
    <div :class="cn('relative', $props.class)">
        <select
            v-model="model"
            :aria-invalid="invalid || undefined"
            class="h-9 w-full appearance-none rounded-md border border-input bg-white py-1 pr-8 pl-3 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive"
            v-bind="$attrs"
        >
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <ChevronDown
            class="pointer-events-none absolute top-1/2 right-2.5 size-4 -translate-y-1/2 text-neutral-500"
        />
    </div>
</template>
