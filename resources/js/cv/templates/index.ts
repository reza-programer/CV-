import type { Component } from 'vue';
import type { TemplateLayout } from '../regions';
import type { TemplateSlug } from '../types';
import AccentTemplate from './accent/AccentTemplate.vue';
import BannerTemplate from './banner/BannerTemplate.vue';
import BlockTemplate from './block/BlockTemplate.vue';
import BoldTemplate from './bold/BoldTemplate.vue';
import ClassicTemplate from './classic/ClassicTemplate.vue';
import CompactTemplate from './compact/CompactTemplate.vue';
import CorporateTemplate from './corporate/CorporateTemplate.vue';
import ElegantTemplate from './elegant/ElegantTemplate.vue';
import ExecutiveTemplate from './executive/ExecutiveTemplate.vue';
import FolioTemplate from './folio/FolioTemplate.vue';
import GutterTemplate from './gutter/GutterTemplate.vue';
import LeaderTemplate from './leader/LeaderTemplate.vue';
import MagazineTemplate from './magazine/MagazineTemplate.vue';
import MinimalTemplate from './minimal/MinimalTemplate.vue';
import ModernTemplate from './modern/ModernTemplate.vue';
import MonogramTemplate from './monogram/MonogramTemplate.vue';
import NumberedTemplate from './numbered/NumberedTemplate.vue';
import PortraitTemplate from './portrait/PortraitTemplate.vue';
import ProfessionalTemplate from './professional/ProfessionalTemplate.vue';
import RibbonTemplate from './ribbon/RibbonTemplate.vue';
import SidebarTemplate from './sidebar/SidebarTemplate.vue';
import SidebarLightTemplate from './sidebar-light/SidebarLightTemplate.vue';
import SplitTemplate from './split/SplitTemplate.vue';
import TabbedTemplate from './tabbed/TabbedTemplate.vue';
import TimelineTemplate from './timeline/TimelineTemplate.vue';
import TwoColumnTemplate from './two-column/TwoColumnTemplate.vue';
import '../styles/document.css';
import './classic/classic.css';
import './compact/compact.css';
import './modern/modern.css';
import './professional/professional.css';
import './executive/executive.css';
import './accent/accent.css';
import './banner/banner.css';
import './block/block.css';
import './portrait/portrait.css';
import './timeline/timeline.css';
import './gutter/gutter.css';
import './minimal/minimal.css';
import './sidebar/sidebar.css';
import './sidebar-light/sidebar-light.css';
import './two-column/two-column.css';
import './split/split.css';
import './monogram/monogram.css';
import './numbered/numbered.css';
import './leader/leader.css';
import './bold/bold.css';
import './ribbon/ribbon.css';
import './corporate/corporate.css';
import './tabbed/tabbed.css';
import './elegant/elegant.css';
import './magazine/magazine.css';
import './folio/folio.css';

export type TemplateDefinition = {
    /** Renders one block; typography lives in the template's `.tpl-{slug}` stylesheet. */
    component: Component;
    layout: TemplateLayout;
    /** Uses the CV's accent colour (`--cv-accent`). */
    coloured: boolean;
};

const single: TemplateLayout = { kind: 'single' };

/**
 * Layout implementations, keyed by the slug stored in cv_templates.
 * Single-column templates are the ATS-safe choice; column templates trade
 * some parser compatibility for a more graphic page.
 */
export const TEMPLATES: Record<TemplateSlug, TemplateDefinition> = {
    classic: { component: ClassicTemplate, layout: single, coloured: false },
    compact: { component: CompactTemplate, layout: single, coloured: false },
    modern: { component: ModernTemplate, layout: single, coloured: false },
    professional: {
        component: ProfessionalTemplate,
        layout: single,
        coloured: false,
    },
    executive: {
        component: ExecutiveTemplate,
        layout: single,
        coloured: false,
    },
    accent: { component: AccentTemplate, layout: single, coloured: true },
    banner: { component: BannerTemplate, layout: single, coloured: true },
    block: { component: BlockTemplate, layout: single, coloured: true },
    portrait: { component: PortraitTemplate, layout: single, coloured: true },
    timeline: { component: TimelineTemplate, layout: single, coloured: true },
    gutter: { component: GutterTemplate, layout: single, coloured: false },
    minimal: { component: MinimalTemplate, layout: single, coloured: false },
    sidebar: {
        component: SidebarTemplate,
        layout: {
            kind: 'columns',
            aside: ['education', 'skills', 'languages', 'certifications'],
            asideSide: 'left',
            header: 'split',
        },
        coloured: true,
    },
    'sidebar-light': {
        component: SidebarLightTemplate,
        layout: {
            kind: 'columns',
            aside: ['skills', 'languages', 'certifications'],
            asideSide: 'right',
            header: 'split',
        },
        coloured: true,
    },
    'two-column': {
        component: TwoColumnTemplate,
        layout: {
            kind: 'columns',
            aside: ['education', 'skills', 'certifications', 'languages'],
            asideSide: 'right',
            header: 'top',
            topSections: ['summary'],
        },
        coloured: true,
    },
    split: {
        component: SplitTemplate,
        layout: {
            kind: 'columns',
            aside: ['education', 'skills', 'certifications', 'languages'],
            asideSide: 'right',
            header: 'top',
        },
        coloured: false,
    },
    monogram: { component: MonogramTemplate, layout: single, coloured: true },
    numbered: { component: NumberedTemplate, layout: single, coloured: true },
    leader: { component: LeaderTemplate, layout: single, coloured: false },
    bold: { component: BoldTemplate, layout: single, coloured: false },
    ribbon: { component: RibbonTemplate, layout: single, coloured: true },
    corporate: {
        component: CorporateTemplate,
        layout: single,
        coloured: false,
    },
    tabbed: { component: TabbedTemplate, layout: single, coloured: true },
    elegant: { component: ElegantTemplate, layout: single, coloured: false },
    magazine: {
        component: MagazineTemplate,
        layout: {
            kind: 'columns',
            aside: ['education', 'skills', 'certifications', 'languages'],
            asideSide: 'right',
            header: 'top',
            topSections: ['summary'],
        },
        coloured: true,
    },
    folio: {
        component: FolioTemplate,
        layout: {
            kind: 'columns',
            aside: ['education', 'skills', 'certifications', 'languages'],
            asideSide: 'left',
            header: 'top',
        },
        coloured: false,
    },
};

export const DEFAULT_TEMPLATE: TemplateSlug = 'classic';

export function resolveTemplate(slug: string): TemplateSlug {
    return slug in TEMPLATES ? (slug as TemplateSlug) : DEFAULT_TEMPLATE;
}

export function isColoured(slug: string): boolean {
    return TEMPLATES[resolveTemplate(slug)].coloured && slug in TEMPLATES;
}

/** Two-column layouts: more graphic, but some older ATS read columns out of order. */
export function isMultiColumn(slug: string): boolean {
    return (
        slug in TEMPLATES &&
        TEMPLATES[slug as TemplateSlug].layout.kind === 'columns'
    );
}
