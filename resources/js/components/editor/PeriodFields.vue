<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import FormField from './FormField.vue';
import PartialDateInput from './PartialDateInput.vue';
import { useEditor } from './useEditorContext';

/** Start / end date pair with a "still ongoing" switch. */
defineProps<{
    item: { start_date: string; end_date: string; is_current: boolean };
    id: string;
    errorPath: string;
    currentLabel?: string;
}>();

const editor = useEditor();
</script>

<template>
    <div class="grid gap-3">
        <div class="grid gap-4 sm:grid-cols-2">
            <FormField
                label="Mulai"
                :for="`${id}-start`"
                :error="editor.error(`${errorPath}.start_date`)"
            >
                <PartialDateInput
                    :id="`${id}-start`"
                    v-model="item.start_date"
                    :invalid="!!editor.error(`${errorPath}.start_date`)"
                />
            </FormField>
            <FormField
                label="Selesai"
                :for="`${id}-end`"
                :error="editor.error(`${errorPath}.end_date`)"
            >
                <PartialDateInput
                    :id="`${id}-end`"
                    v-model="item.end_date"
                    :disabled="item.is_current"
                    :invalid="!!editor.error(`${errorPath}.end_date`)"
                />
            </FormField>
        </div>
        <label class="flex w-fit items-center gap-2 text-sm text-neutral-700">
            <Checkbox v-model="item.is_current" />
            {{ currentLabel ?? 'Masih berlangsung' }}
        </label>
    </div>
</template>
