import type { PersonaKey } from './samplePersonas';
import { PERSONAS } from './samplePersonas';
import type { AccentColor, ResumeData, TemplateSlug } from './types';

/**
 * Example content for the landing page preview and template thumbnails.
 * It is labelled as an example wherever it appears and is never copied into
 * a user's CV. Each template shows a person from a different field, so the
 * gallery reads as CVs for many professions rather than one.
 */

/** Templates whose layout is built around a photo show a neutral silhouette. */
const PHOTO_TEMPLATES: TemplateSlug[] = [
    'portrait',
    'timeline',
    'sidebar',
    'sidebar-light',
    'two-column',
    'magazine',
    'folio',
    'lumen',
    'heritage',
    'skyline',
    'prestige',
    'wave',
    'orbit',
    'fresh',
    'noir',
    'slate',
    'pastel',
    'atelier',
    'frame',
];

const SAMPLE_PHOTO = `data:image/svg+xml,${encodeURIComponent(
    "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 400'><rect width='300' height='400' fill='#d9dee5'/><circle cx='150' cy='155' r='68' fill='#a7b0bc'/><path d='M28 400c0-82 55-132 122-132s122 50 122 132z' fill='#a7b0bc'/></svg>",
)}`;

const TEMPLATE_PERSONAS: Record<TemplateSlug, PersonaKey> = {
    classic: 'accountant',
    compact: 'civil',
    modern: 'marketing',
    professional: 'developer',
    executive: 'legal',
    accent: 'hr',
    banner: 'hospitality',
    block: 'pharmacist',
    portrait: 'nurse',
    timeline: 'teacher',
    gutter: 'graduate',
    minimal: 'designer',
    monogram: 'pharmacist',
    numbered: 'graduate',
    leader: 'teacher',
    bold: 'developer',
    ribbon: 'nurse',
    corporate: 'civil',
    tabbed: 'marketing',
    elegant: 'designer',
    sidebar: 'civil',
    'sidebar-light': 'accountant',
    'two-column': 'hr',
    split: 'legal',
    magazine: 'hospitality',
    folio: 'graduate',
    lumen: 'designer',
    heritage: 'legal',
    skyline: 'marketing',
    prestige: 'hr',
    wave: 'teacher',
    geo: 'civil',
    orbit: 'nurse',
    fresh: 'pharmacist',
    contrast: 'accountant',
    label: 'graduate',
    noir: 'developer',
    slate: 'hospitality',
    pastel: 'designer',
    atelier: 'accountant',
    frame: 'legal',
};

/** Each field gets its own accent, so coloured templates show the range. */
const PERSONA_ACCENTS: Record<PersonaKey, AccentColor> = {
    developer: 'navy',
    accountant: 'navy',
    nurse: 'teal',
    teacher: 'forest',
    marketing: 'plum',
    civil: 'charcoal',
    designer: 'burgundy',
    hr: 'teal',
    legal: 'navy',
    pharmacist: 'forest',
    hospitality: 'teal',
    graduate: 'navy',
};

/** The main example, used in the landing page hero. */
export const SAMPLE_NAME = PERSONAS.developer.personal.full_name;

export function sampleResume(
    template: TemplateSlug = 'classic',
    persona: PersonaKey = TEMPLATE_PERSONAS[template],
): ResumeData {
    const content = PERSONAS[persona];

    return {
        ...structuredClone(content),
        title: 'Contoh CV',
        template,
        paragraph_align: 'left',
        accent_color: PERSONA_ACCENTS[persona],
        photo_url: PHOTO_TEMPLATES.includes(template) ? SAMPLE_PHOTO : null,
    };
}
