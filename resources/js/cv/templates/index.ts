import type { Component } from 'vue';
import type { TemplateSlug } from '../types';
import ClassicTemplate from './classic/ClassicTemplate.vue';
import CompactTemplate from './compact/CompactTemplate.vue';
import ModernTemplate from './modern/ModernTemplate.vue';
import ProfessionalTemplate from './professional/ProfessionalTemplate.vue';
import '../styles/document.css';
import './classic/classic.css';
import './compact/compact.css';
import './modern/modern.css';
import './professional/professional.css';

/**
 * Layout implementations, keyed by the slug stored in cv_templates. Each
 * component renders one block; page margins and typography live in the
 * template's own stylesheet under `.tpl-{slug}`.
 */
export const TEMPLATE_COMPONENTS: Record<TemplateSlug, Component> = {
    classic: ClassicTemplate,
    compact: CompactTemplate,
    modern: ModernTemplate,
    professional: ProfessionalTemplate,
};

export const DEFAULT_TEMPLATE: TemplateSlug = 'classic';

export function resolveTemplate(slug: string): TemplateSlug {
    return slug in TEMPLATE_COMPONENTS
        ? (slug as TemplateSlug)
        : DEFAULT_TEMPLATE;
}
