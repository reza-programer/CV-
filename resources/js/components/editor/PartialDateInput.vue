<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import NativeSelect from './NativeSelect.vue';

/**
 * Month (optional) + year picker producing "YYYY-MM", "YYYY" or "".
 * Native month inputs are missing in Firefox and Safari, and many CVs state
 * only a year, so the two parts are separate controls.
 */
const model = defineModel<string>({ required: true });

const props = defineProps<{
    id: string;
    disabled?: boolean;
    invalid?: boolean;
}>();

const MONTHS = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

const monthOptions = [
    { value: '', label: 'Bulan' },
    ...MONTHS.map((label, index) => ({
        value: String(index + 1).padStart(2, '0'),
        label,
    })),
];

const month = ref('');
const year = ref('');

function readModel(value: string): void {
    const [y = '', m = ''] = (value ?? '').split('-');
    year.value = y;
    month.value = m;
}

readModel(model.value);

watch(model, (value) => {
    if (value !== compose()) {
        readModel(value);
    }
});

function compose(): string {
    if (!/^\d{4}$/.test(year.value)) {
        return '';
    }

    return month.value ? `${year.value}-${month.value}` : year.value;
}

function commit(): void {
    const next = compose();

    // Keep the stored value while a year is half-typed.
    if (next !== '' || year.value === '') {
        model.value = next;
    }
}

watch([month, year], commit);
</script>

<template>
    <div class="grid grid-cols-[minmax(0,1fr)_5.5rem] gap-2">
        <NativeSelect
            v-model="month"
            :options="monthOptions"
            :disabled="props.disabled"
            :invalid="props.invalid"
            :aria-label="`Bulan`"
            :id="`${props.id}-month`"
        />
        <Input
            :id="props.id"
            v-model="year"
            inputmode="numeric"
            maxlength="4"
            placeholder="Tahun"
            autocomplete="off"
            :disabled="props.disabled"
            :aria-invalid="props.invalid || undefined"
            aria-label="Tahun"
        />
    </div>
</template>
