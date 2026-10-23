<script setup lang="ts">
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import { accentHex } from './accents';
import type { Block } from './blocks';
import { buildBlocks, layoutHint } from './blocks';
import CvPageBody from './CvPageBody.vue';
import type { BlockMetric } from './paginate';
import { paginate } from './paginate';
import { splitRegions } from './regions';
import { resolveTemplate, TEMPLATES } from './templates';
import type { ResumeData } from './types';

/**
 * Renders a CV as real A4 pages.
 *
 * Every block is first rendered off-screen at full size inside a probe page
 * with the template's real column widths and measured, then distributed over
 * pages by `paginate`, one column at a time. The editor preview and the PDF
 * print bundle both use this component, so the PDF breaks pages exactly where
 * the preview does.
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

type PagePlan = { main: number[]; aside: number[] };

const slug = computed(() => resolveTemplate(props.data.template));
const template = computed(() => TEMPLATES[slug.value]);
const hasAside = computed(() => template.value.layout.kind === 'columns');
const asideFirst = computed(
    () =>
        template.value.layout.kind === 'columns' &&
        template.value.layout.asideSide === 'left',
);
const regions = computed(() =>
    splitRegions(buildBlocks(props.data), template.value.layout),
);

const measure = ref<HTMLElement | null>(null);
const pages = ref<PagePlan[]>([]);
const pageHeight = ref(0);

const visiblePages = computed(() =>
    props.pageLimit ? pages.value.slice(0, props.pageLimit) : pages.value,
);

const outerHeight = computed(() => {
    const count = Math.max(visiblePages.value.length, 1);

    return (count * pageHeight.value + (count - 1) * props.gap) * props.scale;
});

function pick(blocks: Block[], indexes: number[]): Block[] {
    return indexes.map((index) => blocks[index]).filter(Boolean);
}

/** Usable height inside a column box. */
function contentHeight(element: Element | null): number {
    if (!(element instanceof HTMLElement)) {
        return 0;
    }

    const style = getComputedStyle(element);

    return (
        element.getBoundingClientRect().height -
        parseFloat(style.paddingTop) -
        parseFloat(style.paddingBottom)
    );
}

// Margins count towards the space a block takes: a full-bleed header uses a
// negative top margin to reach into the page margin. The measured DOM can lag
// one render behind the blocks; a missing element counts as zero height until
// the next layout pass.
function metricsFor(flow: Element | null, blocks: Block[]): BlockMetric[] {
    const elements = new Map<number, HTMLElement>();

    for (const element of Array.from(flow?.children ?? []) as HTMLElement[]) {
        elements.set(Number(element.dataset.index), element);
    }

    return blocks.map((block, index) => {
        const element = elements.get(index);

        if (!element) {
            return { height: 0, spaceBefore: 0, ...layoutHint(block) };
        }

        const style = getComputedStyle(element);

        return {
            height:
                element.getBoundingClientRect().height +
                (parseFloat(style.marginTop) || 0) +
                (parseFloat(style.marginBottom) || 0),
            spaceBefore: parseFloat(style.paddingTop) || 0,
            ...layoutHint(block),
        };
    });
}

async function layout(): Promise<number> {
    await nextTick();

    const root = measure.value;
    const first = root?.querySelector<HTMLElement>('[data-probe="first"]');
    const rest = root?.querySelector('[data-probe="rest"]');

    if (!root || !first || !rest) {
        return pages.value.length;
    }

    // The first page loses the height of a full-width top region, so each
    // column has its own capacity on page one and on later pages.
    const columnPages = (region: 'main' | 'aside') => {
        const selector = `.cv-col-${region}`;
        const firstCapacity = contentHeight(first.querySelector(selector));
        const restCapacity = contentHeight(rest.querySelector(selector));

        return paginate(
            metricsFor(
                first.querySelector(`[data-flow="${region}"]`),
                regions.value[region],
            ),
            (page) => (page === 0 ? firstCapacity : restCapacity),
        );
    };

    const main = columnPages('main');
    const aside = hasAside.value ? columnPages('aside') : [];
    const count = Math.max(main.length, aside.length, 1);

    pageHeight.value = first.getBoundingClientRect().height;
    pages.value = Array.from({ length: count }, (_, page) => ({
        main: main[page] ?? [],
        aside: aside[page] ?? [],
    }));
    emit('laidOut', pages.value.length);

    return pages.value.length;
}

let frame = 0;

function scheduleLayout(): void {
    cancelAnimationFrame(frame);
    frame = requestAnimationFrame(() => void layout());
}

watch([regions, slug], scheduleLayout);

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
        :class="[
            `tpl-${slug}`,
            {
                'is-scaled': scale !== 1,
                'align-justify': data.paragraph_align === 'justify',
            },
        ]"
        :style="{
            '--cv-accent': accentHex(data.accent_color),
            height: scale === 1 ? undefined : `${outerHeight}px`,
        }"
    >
        <div ref="measure" class="cv-measure" aria-hidden="true">
            <div class="cv-page" data-probe="first">
                <CvPageBody
                    :block-component="template.component"
                    :top="regions.top"
                    :main="regions.main"
                    :aside="hasAside ? regions.aside : null"
                    :aside-first="asideFirst"
                    indexed
                />
            </div>
            <div class="cv-page" data-probe="rest">
                <CvPageBody
                    :block-component="template.component"
                    :top="[]"
                    :main="[]"
                    :aside="hasAside ? [] : null"
                    :aside-first="asideFirst"
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
                <CvPageBody
                    :block-component="template.component"
                    :top="pageIndex === 0 ? regions.top : []"
                    :main="pick(regions.main, page.main)"
                    :aside="hasAside ? pick(regions.aside, page.aside) : null"
                    :aside-first="asideFirst"
                />
            </section>
        </div>
    </div>
</template>
