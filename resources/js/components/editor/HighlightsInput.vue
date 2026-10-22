<script setup lang="ts">
import { ArrowUp, Plus, X } from '@lucide/vue';
import { nextTick, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { useEditor } from './useEditorContext';

/**
 * Bullet points of one entry. Enter starts a new bullet and Backspace on an
 * empty bullet removes it, so a list can be typed without the mouse.
 */
const bullets = defineModel<string[]>({ required: true });

const props = withDefaults(
    defineProps<{
        id: string;
        errorPath: string;
        label?: string;
        placeholder?: string;
        max?: number;
    }>(),
    {
        label: 'Poin pencapaian dan tanggung jawab',
        placeholder:
            'Mulai dengan kata kerja, mis. "Mengembangkan…", "Memimpin…"',
        max: 12,
    },
);

const editor = useEditor();
const fields = ref<HTMLTextAreaElement[]>([]);

async function focus(index: number): Promise<void> {
    await nextTick();
    const field = fields.value[index];
    field?.focus();
    field?.setSelectionRange(field.value.length, field.value.length);
}

function add(after = bullets.value.length - 1): void {
    if (bullets.value.length >= props.max) {
        return;
    }

    bullets.value.splice(after + 1, 0, '');
    void focus(after + 1);
}

function remove(index: number): void {
    bullets.value.splice(index, 1);
    void focus(Math.max(0, index - 1));
}

function moveUp(index: number): void {
    if (index > 0) {
        const [line] = bullets.value.splice(index, 1);
        bullets.value.splice(index - 1, 0, line);
    }
}

function onKeydown(event: KeyboardEvent, index: number): void {
    if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
        event.preventDefault();
        add(index);
    } else if (
        event.key === 'Backspace' &&
        bullets.value[index] === '' &&
        bullets.value.length > 1
    ) {
        event.preventDefault();
        remove(index);
    }
}

/** Pasting a multi-line list splits it into separate bullets. */
function onPaste(event: ClipboardEvent, index: number): void {
    const text = event.clipboardData?.getData('text') ?? '';
    const lines = text
        .split(/\r?\n/)
        .map((line) => line.replace(/^\s*(?:[-•*·▪●]|\d+[.)])\s*/, '').trim())
        .filter(Boolean);

    if (lines.length < 2) {
        return;
    }

    event.preventDefault();
    const room =
        props.max -
        bullets.value.length +
        (bullets.value[index] === '' ? 1 : 0);
    bullets.value.splice(
        index,
        bullets.value[index] === '' ? 1 : 0,
        ...lines.slice(0, Math.max(room, 0)),
    );
}
</script>

<template>
    <div class="grid gap-2">
        <div class="flex items-center justify-between gap-2">
            <span
                :id="`${id}-label`"
                class="text-[13px] font-medium text-neutral-800"
                >{{ label }}</span
            >
            <slot name="actions" />
        </div>

        <ul class="grid gap-1.5" :aria-labelledby="`${id}-label`">
            <li
                v-for="(_, index) in bullets"
                :key="index"
                class="group flex items-start gap-1.5"
            >
                <span
                    class="pt-2 text-sm leading-5 text-neutral-400 select-none"
                    aria-hidden="true"
                    >•</span
                >
                <div class="min-w-0 flex-1">
                    <textarea
                        :ref="
                            (el) => (fields[index] = el as HTMLTextAreaElement)
                        "
                        v-model="bullets[index]"
                        rows="1"
                        maxlength="600"
                        :placeholder="index === 0 ? placeholder : ''"
                        :aria-label="`Poin ${index + 1}`"
                        :aria-invalid="
                            editor.error(`${errorPath}.${index}`)
                                ? true
                                : undefined
                        "
                        class="block field-sizing-content min-h-9 w-full resize-none rounded-md border border-input bg-white px-3 py-2 text-sm leading-5 shadow-xs outline-none placeholder:text-neutral-400 focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive"
                        @keydown="onKeydown($event, index)"
                        @paste="onPaste($event, index)"
                    />
                    <p
                        v-if="editor.error(`${errorPath}.${index}`)"
                        class="mt-1 text-xs text-red-600"
                    >
                        {{ editor.error(`${errorPath}.${index}`) }}
                    </p>
                </div>
                <div
                    class="flex pt-0.5 opacity-60 group-focus-within:opacity-100 group-hover:opacity-100"
                >
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        :disabled="index === 0"
                        aria-label="Naikkan poin"
                        title="Naikkan poin"
                        @click="moveUp(index)"
                    >
                        <ArrowUp />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        aria-label="Hapus poin"
                        title="Hapus poin"
                        @click="remove(index)"
                    >
                        <X />
                    </Button>
                </div>
            </li>
        </ul>

        <p v-if="editor.error(errorPath)" class="text-xs text-red-600">
            {{ editor.error(errorPath) }}
        </p>

        <div class="flex items-center gap-3">
            <Button
                variant="ghost"
                size="sm"
                class="-ml-2"
                :disabled="bullets.length >= max"
                @click="add()"
            >
                <Plus />
                Tambah poin
            </Button>
            <span class="text-xs text-neutral-400"
                >Enter untuk poin baru · tempel daftar untuk memecah
                otomatis</span
            >
        </div>
    </div>
</template>
