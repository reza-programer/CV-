import { isColoured, isMultiColumn } from '@/cv/templates';
import type { TemplateSummary } from '@/cv/types';

export type TemplateFilter =
    | 'all'
    | 'recommended'
    | 'ats'
    | 'coloured'
    | 'creative';

/** Ways to narrow the template gallery; shared by the landing page and the new-CV dialog. */
export const TEMPLATE_FILTERS: Array<{
    value: TemplateFilter;
    label: string;
    matches: (template: TemplateSummary) => boolean;
}> = [
    { value: 'all', label: 'Semua', matches: () => true },
    {
        value: 'recommended',
        label: 'Rekomendasi',
        matches: (template) => template.recommended,
    },
    {
        value: 'ats',
        label: 'Satu kolom ATS',
        matches: (template) => !isMultiColumn(template.slug),
    },
    {
        value: 'coloured',
        label: 'Berwarna',
        matches: (template) => isColoured(template.slug),
    },
    {
        value: 'creative',
        label: 'Kreatif',
        matches: (template) => isMultiColumn(template.slug),
    },
];

export function filterTemplates(
    templates: TemplateSummary[],
    filter: TemplateFilter,
): TemplateSummary[] {
    const rule = TEMPLATE_FILTERS.find((item) => item.value === filter);

    return rule ? templates.filter(rule.matches) : templates;
}

/** Template chosen on the landing page, picked up by the dashboard after sign-in or sign-up. */
export const CHOSEN_TEMPLATE_KEY = 'cvforge:template';
