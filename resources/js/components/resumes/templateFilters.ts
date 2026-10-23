import { isColoured, isMultiColumn } from '@/cv/templates';
import type { TemplateSummary } from '@/cv/types';

export type TemplateFilter = 'all' | 'ats' | 'coloured' | 'creative';

/** Ways to narrow the template gallery; shared by the landing page and the new-CV dialog. */
export const TEMPLATE_FILTERS: Array<{
    value: TemplateFilter;
    label: string;
    matches: (slug: string) => boolean;
}> = [
    { value: 'all', label: 'Semua', matches: () => true },
    {
        value: 'ats',
        label: 'Satu kolom ATS',
        matches: (slug) => !isMultiColumn(slug),
    },
    { value: 'coloured', label: 'Berwarna', matches: isColoured },
    { value: 'creative', label: 'Kreatif', matches: isMultiColumn },
];

export function filterTemplates(
    templates: TemplateSummary[],
    filter: TemplateFilter,
): TemplateSummary[] {
    const rule = TEMPLATE_FILTERS.find((item) => item.value === filter);

    return rule
        ? templates.filter((template) => rule.matches(template.slug))
        : templates;
}

/** Template chosen on the landing page, picked up by the dashboard after sign-in or sign-up. */
export const CHOSEN_TEMPLATE_KEY = 'cvforge:template';
