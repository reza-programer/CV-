import type { SectionKey } from '../../types';
import type { KitSlug } from '../../types';
import type { ContactMode } from '../studio/options';
import { withOptions } from '../studio/options';
import StudioColumnsTemplate from '../studio/StudioColumnsTemplate.vue';
import StudioSingleTemplate from '../studio/StudioSingleTemplate.vue';
import type { TemplateDefinition } from '../index';

/**
 * The catalogue: templates assembled from the style traits in kit.css on top
 * of the studio markup. A trait is one decision (a font, a header, a section
 * title style, a page decoration, a photo shape, a side panel), so each entry
 * below reads as a short recipe and no template needs its own stylesheet.
 */

type Single = {
    traits: string[];
    coloured: boolean;
    contacts?: ContactMode;
};

type Columns = Single & {
    side?: 'left' | 'right';
    header?: 'split' | 'top';
    aside?: SectionKey[];
};

const ASIDE: SectionKey[] = ['skills', 'languages', 'certifications'];
const ASIDE_WITH_EDUCATION: SectionKey[] = [
    'education',
    'skills',
    'languages',
    'certifications',
];

function single({
    traits,
    coloured,
    contacts = 'plain',
}: Single): TemplateDefinition {
    return {
        component: withOptions(StudioSingleTemplate, { contacts }),
        layout: { kind: 'single' },
        coloured,
        family: 'studio',
        traits: ['kit', coloured ? 'colour' : 'mono', ...traits],
    };
}

function columns({
    traits,
    coloured,
    contacts = 'icons-stacked',
    side = 'left',
    header = 'split',
    aside = ASIDE,
}: Columns): TemplateDefinition {
    return {
        component: withOptions(StudioColumnsTemplate, { contacts }),
        layout: { kind: 'columns', aside, asideSide: side, header },
        coloured,
        family: 'studio',
        traits: [
            'kit',
            'cols',
            `aside-${side}`,
            `header-${header}`,
            coloured ? 'colour' : 'mono',
            ...traits,
        ],
    };
}

export const KIT_TEMPLATES: Record<KitSlug, TemplateDefinition> = {
    // Plain black-and-white ATS ---------------------------------------------
    clean: single({ traits: ['head-center', 'h-rule'], coloured: false }),
    simple: single({ traits: ['airy', 'h-plain-caps'], coloured: false }),
    formal: single({
        traits: ['font-tinos', 'head-center', 'name-caps', 'h-double'],
        coloured: false,
    }),
    academic: single({
        traits: ['font-tinos', 'head-center', 'h-line-center', 'sub-italic'],
        coloured: false,
    }),
    crisp: single({
        traits: ['font-carlito', 'name-xl', 'h-line-after'],
        coloured: false,
    }),
    nordic: single({
        traits: [
            'airy',
            'name-xl',
            'name-regular',
            'headline-caps',
            'h-caps-spaced',
        ],
        coloured: false,
    }),
    office: single({
        traits: ['font-carlito', 'head-rule', 'h-boxed'],
        coloured: false,
    }),
    ledger: single({
        traits: ['dense', 'h-rule', 'dates-strong'],
        coloured: false,
    }),
    concise: single({
        traits: ['font-carlito', 'dense', 'h-underline'],
        coloured: false,
    }),
    serif: single({
        traits: ['font-tinos', 'h-dot', 'sub-italic'],
        coloured: false,
    }),
    swiss: single({
        traits: ['name-xl', 'head-rule', 'h-line-after'],
        coloured: false,
    }),
    outline: single({ traits: ['deco-frame', 'h-rule'], coloured: false }),
    bar: single({ traits: ['h-bar', 'bullet-square'], coloured: false }),
    centered: single({
        traits: ['font-carlito', 'head-center', 'h-center', 'h-plain-caps'],
        coloured: false,
    }),
    graphite: single({ traits: ['head-tint', 'h-rule'], coloured: false }),
    sided: single({
        traits: ['head-split', 'h-rule'],
        coloured: false,
        contacts: 'icons-stacked',
    }),
    tidy: single({
        traits: ['font-mixed', 'h-underline', 'bullet-dash'],
        coloured: false,
    }),
    plain: single({ traits: ['font-carlito', 'h-bold'], coloured: false }),

    // Coloured, one column --------------------------------------------------------
    azure: single({
        traits: ['head-band', 'photo-ring', 'h-bar', 'h-accent'],
        coloured: true,
        contacts: 'icons',
    }),
    horizon: single({
        traits: ['deco-topbar', 'name-accent', 'h-line-after', 'h-accent'],
        coloured: true,
        contacts: 'icons',
    }),
    stripe: single({
        traits: ['deco-stripe', 'name-caps', 'h-caps-spaced'],
        coloured: true,
    }),
    corner: single({
        traits: ['deco-corner', 'photo-round', 'name-accent', 'h-underline'],
        coloured: true,
        contacts: 'table',
    }),
    sunrise: single({
        traits: ['head-tint', 'photo-rounded', 'h-pill'],
        coloured: true,
        contacts: 'icons',
    }),
    ink: single({
        traits: [
            'font-tinos',
            'name-accent',
            'h-rule',
            'h-accent',
            'sub-italic',
        ],
        coloured: true,
    }),
    vista: single({
        traits: [
            'deco-arc',
            'head-center',
            'photo-ring',
            'h-line-center',
            'h-accent',
        ],
        coloured: true,
        contacts: 'icons',
    }),
    marker: single({
        traits: ['photo-square', 'name-accent', 'h-marker'],
        coloured: true,
        contacts: 'icons',
    }),
    emblem: single({
        traits: ['head-right', 'photo-ring', 'h-tab'],
        coloured: true,
        contacts: 'icons',
    }),
    pulse: single({
        traits: [
            'deco-dots',
            'h-dot',
            'h-accent',
            'bullet-accent',
            'dates-accent',
        ],
        coloured: true,
        contacts: 'icons',
    }),
    crest: single({
        traits: ['deco-diagonal', 'head-right', 'photo-round', 'h-caps-spaced'],
        coloured: true,
        contacts: 'icons-stacked',
    }),
    harbor: single({
        traits: ['head-band', 'photo-square', 'h-boxed', 'h-accent'],
        coloured: true,
        contacts: 'table',
    }),
    bloom: single({
        traits: ['deco-circles', 'photo-round', 'name-accent', 'h-pill-soft'],
        coloured: true,
        contacts: 'icons',
    }),
    spark: single({
        traits: ['head-rule', 'name-caps', 'h-number', 'bullet-square'],
        coloured: true,
    }),
    linear: single({
        traits: [
            'deco-bottombar',
            'name-caps',
            'headline-accent',
            'h-line-after',
        ],
        coloured: true,
    }),
    metro: single({
        traits: ['dense', 'head-split', 'h-band'],
        coloured: true,
        contacts: 'icons-stacked',
    }),
    canvas: single({
        traits: [
            'deco-frame',
            'head-center',
            'photo-round',
            'h-center',
            'h-accent',
        ],
        coloured: true,
        contacts: 'icons',
    }),
    aurora: single({
        traits: ['head-band', 'head-center', 'photo-ring', 'h-underline'],
        coloured: true,
        contacts: 'icons',
    }),
    sage: single({
        traits: ['font-carlito', 'head-tint', 'photo-rounded', 'h-bar'],
        coloured: true,
        contacts: 'icons',
    }),
    royal: single({
        traits: [
            'font-tinos',
            'deco-frame-double',
            'head-center',
            'name-caps',
            'name-accent',
            'h-double',
        ],
        coloured: true,
    }),
    zenith: single({
        traits: [
            'deco-stripe-right',
            'head-right',
            'photo-arch',
            'headline-caps',
            'h-dot',
        ],
        coloured: true,
        contacts: 'icons',
    }),
    ember: single({
        traits: ['deco-bars', 'photo-round', 'h-pill'],
        coloured: true,
        contacts: 'icons',
    }),
    coral: single({
        traits: ['deco-arc', 'photo-rounded', 'name-caps', 'h-pill-soft'],
        coloured: true,
        contacts: 'icons',
    }),
    tide: single({
        traits: ['head-band', 'deco-bottombar', 'h-line-after', 'h-accent'],
        coloured: true,
        contacts: 'icons',
    }),
    mint: single({
        traits: ['font-carlito', 'head-center', 'photo-round', 'h-boxed'],
        coloured: true,
        contacts: 'icons',
    }),
    ridge: single({
        traits: ['font-mixed', 'photo-square', 'h-number', 'h-line-after'],
        coloured: true,
        contacts: 'icons',
    }),

    // Two columns -------------------------------------------------------------
    harmony: columns({
        traits: ['aside-tint', 'photo-round', 'h-pill-soft'],
        coloured: true,
    }),
    summit: columns({
        traits: ['aside-dark', 'photo-ring', 'h-line-after'],
        coloured: true,
        aside: ASIDE_WITH_EDUCATION,
    }),
    balance: columns({
        traits: ['aside-line', 'head-band', 'photo-square', 'h-rule'],
        coloured: true,
        side: 'right',
        header: 'top',
        contacts: 'icons',
    }),
    ivory: columns({
        traits: ['aside-gray', 'font-tinos', 'photo-rounded', 'h-rule'],
        coloured: false,
    }),
    duo: columns({
        traits: ['aside-tint', 'head-center', 'photo-round', 'h-underline'],
        coloured: true,
        side: 'right',
        header: 'top',
        contacts: 'icons',
    }),
    cobalt: columns({
        traits: ['aside-dark', 'name-caps', 'photo-arch', 'h-caps-spaced'],
        coloured: true,
        aside: ASIDE_WITH_EDUCATION,
    }),
    terra: columns({
        traits: ['aside-dark', 'photo-round', 'h-bar'],
        coloured: true,
        side: 'right',
    }),
    mosaic: columns({
        traits: ['aside-tint', 'dense', 'photo-square', 'h-boxed'],
        coloured: true,
        aside: ASIDE_WITH_EDUCATION,
    }),
    crystal: columns({
        traits: ['aside-line', 'font-carlito', 'photo-arch', 'h-underline'],
        coloured: true,
    }),
    quartz: columns({
        traits: ['aside-gray', 'photo-round', 'h-bar'],
        coloured: false,
        side: 'right',
    }),
    sierra: columns({
        traits: ['aside-dark', 'ink-charcoal', 'photo-square', 'h-line-after'],
        coloured: false,
        aside: ASIDE_WITH_EDUCATION,
    }),
    pixel: columns({
        traits: ['aside-tint', 'photo-rounded', 'h-number'],
        coloured: true,
    }),
    lotus: columns({
        traits: ['aside-tint', 'name-serif', 'photo-round', 'h-dot'],
        coloured: true,
        side: 'right',
    }),
    vertex: columns({
        traits: ['aside-dark', 'deco-corner', 'photo-ring', 'h-tab'],
        coloured: true,
        aside: ASIDE_WITH_EDUCATION,
    }),
    echo: columns({
        traits: ['aside-line', 'font-carlito', 'head-center', 'h-line-center'],
        coloured: false,
        side: 'right',
        header: 'top',
        contacts: 'icons',
    }),
};
