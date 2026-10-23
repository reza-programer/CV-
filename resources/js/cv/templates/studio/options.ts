import type { Component, PropType } from 'vue';
import { defineComponent, h } from 'vue';
import type { Block } from '../../blocks';
import type { Region } from '../../regions';

/** How a studio template lists contact details. */
export type ContactMode = 'plain' | 'icons' | 'icons-stacked' | 'table';

/**
 * The few markup choices that differ between studio templates; everything
 * else (colour, shapes, type) lives in each template's stylesheet.
 */
export type StudioOptions = {
    contacts?: ContactMode;
    /** Icon badge before each section title (the title itself stays text). */
    sectionIcons?: boolean;
};

/** Binds a shared studio component to one template's options. */
export function withOptions(
    component: Component,
    options: StudioOptions,
): Component {
    return defineComponent({
        props: {
            block: { type: Object as PropType<Block>, required: true },
            region: { type: String as PropType<Region>, default: 'main' },
        },
        setup(props) {
            return () =>
                h(component, {
                    block: props.block,
                    region: props.region,
                    options,
                });
        },
    });
}
