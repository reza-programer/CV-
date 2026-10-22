<script setup lang="ts">
import { ChevronDown } from '@lucide/vue';
import { ref } from 'vue';

/**
 * Collapsible block of the editor form. Sections are separated by rules
 * rather than boxed in cards, like the panels of a document editor.
 */
const props = withDefaults(
    defineProps<{
        id: string;
        title: string;
        description?: string;
        count?: number;
        hasError?: boolean;
        defaultOpen?: boolean;
    }>(),
    { defaultOpen: true },
);

const open = ref(props.defaultOpen);
</script>

<template>
    <section :id="id" class="scroll-mt-16 border-b border-neutral-200">
        <h2>
            <button
                type="button"
                class="flex w-full items-center gap-3 px-5 py-3.5 text-left hover:bg-neutral-50 focus-visible:bg-neutral-50 focus-visible:outline-none"
                :aria-expanded="open"
                :aria-controls="`${id}-body`"
                @click="open = !open"
            >
                <span class="flex-1">
                    <span
                        class="flex items-center gap-2 text-sm font-semibold text-neutral-900"
                    >
                        {{ title }}
                        <span
                            v-if="count"
                            class="text-xs font-normal text-neutral-500 tabular-nums"
                            >{{ count }}</span
                        >
                        <span
                            v-if="hasError"
                            class="size-1.5 rounded-full bg-red-600"
                            aria-label="Ada isian yang perlu diperbaiki"
                        />
                    </span>
                    <span
                        v-if="description && open"
                        class="mt-0.5 block text-xs text-neutral-500"
                        >{{ description }}</span
                    >
                </span>
                <ChevronDown
                    class="size-4 text-neutral-500 transition-transform"
                    :class="{ '-rotate-180': open }"
                />
            </button>
        </h2>
        <div v-show="open" :id="`${id}-body`" class="px-5 pt-1 pb-5">
            <slot />
        </div>
    </section>
</template>
