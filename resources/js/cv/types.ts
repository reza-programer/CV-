/**
 * Document payload shared by the editor, the preview and the PDF renderer.
 * Mirrors App\Support\Resumes\ResumePayload and UpdateResumeRequest.
 */

export type TemplateSlug = 'classic' | 'compact' | 'modern' | 'professional';

export type ResumeLocale = 'id' | 'en';

export type SectionKey =
    | 'summary'
    | 'experience'
    | 'internship'
    | 'organization'
    | 'education'
    | 'projects'
    | 'skills'
    | 'certifications'
    | 'languages';

export type ActivityType = 'internship' | 'organization';

/** "YYYY", "YYYY-MM" or "" when unknown. */
export type PartialDate = string;

export type PersonalInfo = {
    full_name: string;
    headline: string;
    email: string;
    phone: string;
    location: string;
    website: string;
    linkedin: string;
};

export type ExperienceItem = {
    position: string;
    organization: string;
    location: string;
    start_date: PartialDate;
    end_date: PartialDate;
    is_current: boolean;
    highlights: string[];
};

export type ActivityItem = ExperienceItem & {
    type: ActivityType;
};

export type EducationItem = {
    institution: string;
    degree: string;
    field_of_study: string;
    location: string;
    start_date: PartialDate;
    end_date: PartialDate;
    is_current: boolean;
    gpa: string;
    highlights: string[];
};

export type ProjectItem = {
    name: string;
    role: string;
    url: string;
    start_date: PartialDate;
    end_date: PartialDate;
    is_current: boolean;
    highlights: string[];
};

export type SkillGroupItem = {
    category: string;
    skills: string;
};

export type CertificationItem = {
    name: string;
    issuer: string;
    issued_at: PartialDate;
    expires_at: PartialDate;
    credential_id: string;
    url: string;
};

export type LanguageItem = {
    name: string;
    proficiency: string;
};

export type ResumeData = {
    id?: number;
    title: string;
    template: TemplateSlug;
    locale: ResumeLocale;
    section_order: SectionKey[];
    personal: PersonalInfo;
    photo_url: string | null;
    summary: string;
    experiences: ExperienceItem[];
    activities: ActivityItem[];
    educations: EducationItem[];
    projects: ProjectItem[];
    skill_groups: SkillGroupItem[];
    certifications: CertificationItem[];
    languages: LanguageItem[];
};

export type TemplateSummary = {
    slug: TemplateSlug;
    name: string;
    description: string | null;
};

export const SECTION_KEYS: SectionKey[] = [
    'summary',
    'experience',
    'internship',
    'organization',
    'education',
    'projects',
    'skills',
    'certifications',
    'languages',
];
