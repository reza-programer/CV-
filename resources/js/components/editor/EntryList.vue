<script setup lang="ts" generic="T extends object">
import { ArrowDown, ArrowUp, ChevronRight, Plus, Trash2 } from '@lucide/vue';
import { reactive, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { itemKey } from '@/composables/useResumeEditor';
import { useEditor } from './useEditorContext';

type ListKey =
    | 'experiences'
    | 'activities'
    | 'educations'
    | 'projects'
    | 'skill_groups'
    | 'certifications'
    | 'languages';

/**
 * Repeatable entries of one section: add, collapse, reorder, delete with
 * undo. Fields of each entry come from the `item` slot.
 */
const props = defineProps<{
    list: ListKey;
    items: T[];
    addLabel: string;
    emptyText: string;
    create: () => T;
    summary: (item: T) => { title: string; subtitle?: string };
}>();

const editor = useEditor();
const expanded = reactive(new Set<string>());

function toggle(item: T): void {
    const key = itemKey(item);

    if (expanded.has(key)) {
        expanded.delete(key);
    } else {
        expanded.add(key);
    }
}

function add(): void {
    const item = props.create();
    editor.addItem(props.list, item as never);
    expanded.add(itemKey(item));

    requestAnimationFrame(() => {
        document
            .querySelector<HTMLElement>(
                `[data-entry="${itemKey(item)}"] input, [data-entry="${itemKey(item)}"] textarea`,
            )
            ?.focus();
    });
}

function remove(index: number): void {
    const item = props.items[index];
    const label = props.summary(item).title || 'Entri';
    editor.removeItem(props.list, index);

    toast(`${label} dihapus.`, {
        action: {
            label: 'Batalkan',
            onClick: () => {
                (editor.resume[props.list] as unknown[]).splice(index, 0, item);
            },
        },
    });
}

function hasError(index: number): boolean {
    const prefix = `${props.list}.${index}.`;

    return Object.keys(editor.errors.value).some((key) =>
        key.startsWith(prefix),
    );
}

// Open entries that the server rejected so the messages are visible.
watch(
    () => editor.errors.value,
    () =>
        props.items.forEach(
            (item, index) => hasError(index) && expanded.add(itemKey(item)),
        ),
);
</script>

<template>
    <div class="grid gap-2">
        <p v-if="items.length === 0" class="text-sm text-neutral-500">
            {{ emptyText }}
        </p>

        <div
            v-for="(item, index) in items"
            :key="itemKey(item)"
            :data-entry="itemKey(item)"
            class="min-w-0 rounded-md border bg-white"
            :class="hasError(index) ? 'border-red-300' : 'border-neutral-200'"
        >
            <div class="flex items-center gap-1 py-1 pr-1 pl-2">
                <button
                    type="button"
                    class="flex min-w-0 flex-1 items-center gap-2 rounded px-1 py-1.5 text-left focus-visible:ring-2 focus-visible:ring-neutral-300 focus-visible:outline-none"
                    :aria-expanded="expanded.has(itemKey(item))"
                    @click="toggle(item)"
                >
                    <ChevronRight
                        class="size-4 shrink-0 text-neutral-400 transition-transform"
                        :class="{ 'rotate-90': expanded.has(itemKey(item)) }"
                    />
                    <span class="min-w-0">
                        <span
                            class="block truncate text-sm font-medium text-neutral-900"
                        >
                            {{ summary(item).title || 'Entri baru' }}
                        </span>
                        <span
                            v-if="summary(item).subtitle"
                            class="block truncate text-xs text-neutral-500"
                        >
                            {{ summary(item).subtitle }}
                        </span>
                    </span>
                </button>

                <Button
                    variant="ghost"
                    size="icon-sm"
                    :disabled="index === 0"
                    :aria-label="`Pindahkan ke atas`"
                    title="Pindahkan ke atas"
                    @click="editor.moveItem(list, index, index - 1)"
                >
                    <ArrowUp />
                </Button>
                <Button
                    variant="ghost"
                    size="icon-sm"
                    :disabled="index === items.length - 1"
                    aria-label="Pindahkan ke bawah"
                    title="Pindahkan ke bawah"
                    @click="editor.moveItem(list, index, index + 1)"
                >
                    <ArrowDown />
                </Button>
                <Button
                    variant="ghost"
                    size="icon-sm"
                    class="text-neutral-500 hover:text-red-600"
                    aria-label="Hapus entri"
                    title="Hapus entri"
                    @click="remove(index)"
                >
                    <Trash2 />
                </Button>
            </div>

            <div
                v-if="expanded.has(itemKey(item))"
                class="grid gap-4 border-t border-neutral-100 px-3 pt-3 pb-4"
            >
                <slot name="item" :item="item" :index="index" />
            </div>
        </div>

        <Button
            variant="outline"
            size="sm"
            class="justify-self-start"
            @click="add"
        >
            <Plus />
            {{ addLabel }}
        </Button>
    </div>
</template>
