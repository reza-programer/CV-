<script setup lang="ts">
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import { buildBlocks, layoutHint } from './blocks';
import type { BlockMetric } from './paginate';
import { paginate } from './paginate';
import { resolveTemplate, TEMPLATE_COMPONENTS } from './templates';
import type { ResumeData } from './types';

/**
 * Renders a CV as real A4 pages.
 *
 * Every block is first rendered off-screen at full size and measured, then
 * distributed over pages by `paginate`. The editor preview and the PDF print
 * bundle both use this component, so the PDF breaks pages exactly where the
 * preview does.
 */
const props = withDefaults(
    defineProps<{
        data: ResumeData;
        /** Visual zoom of the pages; measurement always happens at 1:1. */
        scale?: number;
        /** Space between pages in unscaled px (0 for print). */
        gap?: number;
        /** Render only the first N pages (thumbnails). */
        pageLimit?: number;
    }>(),
    { scale: 1, gap: 0, pageLimit: undefined },
);

const emit = defineEmits<{ laidOut: [pageCount: number] }>();

const slug = computed(() => resolveTemplate(props.data.template));
const component = computed(() => TEMPLATE_COMPONENTS[slug.value]);
const blocks = computed(() => buildBlocks(props.data));

const measureFlow = ref<HTMLElement | null>(null);
const probe = ref<HTMLElement | null>(null);
const pages = ref<number[][]>([]);
const pageHeight = ref(0);

const visiblePages = computed(() =>
    props.pageLimit ? pages.value.slice(0, props.pageLimit) : pages.value,
);

const outerHeight = computed(() => {
    const count = Math.max(visiblePages.value.length, 1);

    return (count * pageHeight.value + (count - 1) * props.gap) * props.scale;
});

async function layout(): Promise<number> {
    await nextTick();

    const flow = measureFlow.value;
    const page = probe.value;

    if (!flow || !page) {
        return pages.value.length;
    }

    const pageStyle = getComputedStyle(page);
    const pageBox = page.getBoundingClientRect().height;
    const capacity =
        pageBox -
        parseFloat(pageStyle.paddingTop) -
        parseFloat(pageStyle.paddingBottom);
    const elements = new Map<number, HTMLElement>();

    for (const element of Array.from(flow.children) as HTMLElement[]) {
        elements.set(Number(element.dataset.index), element);
    }

    // The measured DOM can lag one render behind `blocks`; a block without an
    // element yet counts as zero height until the next layout pass.
    const metrics: BlockMetric[] = blocks.value.map((block, index) => {
        const element = elements.get(index);

        return {
            height: element?.getBoundingClientRect().height ?? 0,
            spaceBefore: element
                ? parseFloat(getComputedStyle(element).paddingTop) || 0
                : 0,
            ...layoutHint(block),
        };
    });

    pageHeight.value = pageBox;
    pages.value = paginate(metrics, capacity);
    emit('laidOut', pages.value.length);

    return pages.value.length;
}

let frame = 0;

function scheduleLayout(): void {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(() => void layout());
}

watch([blocks, slug], scheduleLayout);

const onFontsLoaded = () => scheduleLayout();

onMounted(() => {
    scheduleLayout();
    void document.fonts.ready.then(scheduleLayout);
    document.fonts.addEventListener('loadingdone', onFontsLoaded);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(frame);
    document.fonts.removeEventListener('loadingdone', onFontsLoaded);
});

defineExpose({ layout, pageCount: computed(() => pages.value.length) });
</script>

<template>
    <div
        class="cv-doc"
        :class="[`tpl-${slug}`, { 'is-scaled': scale !== 1 }]"
        :style="scale === 1 ? undefined : { height: `${outerHeight}px` }"
    >
        <div class="cv-measure" aria-hidden="true">
            <div ref="probe" class="cv-page" />
            <div ref="measureFlow" class="cv-flow">
                <component
                    :is="component"
                    v-for="(block, index) in blocks"
                    :key="block.id"
                    :block="block"
                    :data-index="index"
                />
            </div>
        </div>

        <div
            class="cv-pages"
            :style="{ transform: scale === 1 ? undefined : `scale(${scale})` }"
        >
            <section
                v-for="(page, pageIndex) in visiblePages"
                :key="pageIndex"
                class="cv-page"
                :style="
                    pageIndex > 0 && gap > 0
                        ? { marginTop: `${gap}px` }
                        : undefined
                "
            >
                <div class="cv-flow">
                    <component
                        :is="component"
                        v-for="index in page"
                        :key="blocks[index].id"
                        :block="blocks[index]"
                    />
                </div>
            </section>
        </div>
    </div>
</template>
