<script setup lang="ts">
import { ChevronDown } from '@lucide/vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ACCENT_KEYS, ACCENTS } from '@/cv/accents';
import type { AccentColor } from '@/cv/types';

/** Accent colour for the coloured templates. */
const model = defineModel<AccentColor>({ required: true });
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger
            class="flex h-9 shrink-0 items-center gap-1.5 rounded-md border border-input bg-white px-2.5 shadow-xs outline-none hover:bg-neutral-50 focus-visible:ring-[3px] focus-visible:ring-ring/50"
            :aria-label="`Warna aksen: ${ACCENTS[model].label}`"
            :title="`Warna aksen: ${ACCENTS[model].label}`"
        >
            <span
                class="size-4 rounded-full"
                :style="{ backgroundColor: ACCENTS[model].hex }"
            />
            <ChevronDown class="size-3.5 text-neutral-500" />
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-48">
            <DropdownMenuLabel class="text-xs font-normal text-neutral-500">
                Warna aksen
            </DropdownMenuLabel>
            <DropdownMenuRadioGroup
                :model-value="model"
                @update:model-value="model = $event as AccentColor"
            >
                <DropdownMenuRadioItem
                    v-for="key in ACCENT_KEYS"
                    :key="key"
                    :value="key"
                >
                    <span
                        class="size-3.5 rounded-full"
                        :style="{ backgroundColor: ACCENTS[key].hex }"
                    />
                    {{ ACCENTS[key].label }}
                </DropdownMenuRadioItem>
            </DropdownMenuRadioGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
