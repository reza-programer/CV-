import type { ResumeData } from '@/cv/types';

/** Row of the "CV Saya" dashboard. */
export type ResumeListItem = {
    id: number;
    title: string;
    template_name: string;
    updated_at: string;
    document: ResumeData;
};
