import type {
    ActivityItem,
    ActivityType,
    CertificationItem,
    EducationItem,
    ExperienceItem,
    LanguageItem,
    ProjectItem,
    SkillGroupItem,
} from './types';

/** Blank entries for the editor's "add" buttons. */

export const emptyExperience = (): ExperienceItem => ({
    position: '',
    organization: '',
    location: '',
    start_date: '',
    end_date: '',
    is_current: false,
    highlights: [''],
});

export const emptyActivity = (
    type: ActivityType = 'internship',
): ActivityItem => ({
    type,
    ...emptyExperience(),
});

export const emptyEducation = (): EducationItem => ({
    institution: '',
    degree: '',
    field_of_study: '',
    location: '',
    start_date: '',
    end_date: '',
    is_current: false,
    gpa: '',
    highlights: [],
});

export const emptyProject = (): ProjectItem => ({
    name: '',
    role: '',
    url: '',
    start_date: '',
    end_date: '',
    is_current: false,
    highlights: [''],
});

export const emptySkillGroup = (): SkillGroupItem => ({
    category: '',
    skills: '',
});

export const emptyCertification = (): CertificationItem => ({
    name: '',
    issuer: '',
    issued_at: '',
    expires_at: '',
    credential_id: '',
    url: '',
});

export const emptyLanguage = (): LanguageItem => ({
    name: '',
    proficiency: '',
});
