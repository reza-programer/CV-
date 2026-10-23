import type { PartialDate, ResumeLocale, SectionKey } from './types';

const MONTHS: Record<ResumeLocale, string[]> = {
    id: [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des',
    ],
    en: [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ],
};

const PRESENT: Record<ResumeLocale, string> = {
    id: 'Sekarang',
    en: 'Present',
};

export const SECTION_TITLES: Record<
    ResumeLocale,
    Record<SectionKey, string>
> = {
    id: {
        summary: 'Profil',
        experience: 'Pengalaman Kerja',
        internship: 'Pengalaman Magang',
        organization: 'Pengalaman Organisasi',
        education: 'Pendidikan',
        projects: 'Proyek',
        skills: 'Keahlian',
        certifications: 'Sertifikasi',
        languages: 'Bahasa',
    },
    en: {
        summary: 'Summary',
        experience: 'Work Experience',
        internship: 'Internship Experience',
        organization: 'Organizational Experience',
        education: 'Education',
        projects: 'Projects',
        skills: 'Skills',
        certifications: 'Certifications',
        languages: 'Languages',
    },
};

export const DOCUMENT_LABELS: Record<
    ResumeLocale,
    {
        gpa: string;
        credential: string;
        expires: string;
        contact: string;
        /** Field names for templates that list contacts as a table. */
        contactFields: Record<
            'email' | 'phone' | 'location' | 'linkedin' | 'website',
            string
        >;
    }
> = {
    id: {
        gpa: 'IPK',
        credential: 'ID Kredensial',
        expires: 'berlaku hingga',
        contact: 'Kontak',
        contactFields: {
            email: 'Email',
            phone: 'Telepon',
            location: 'Domisili',
            linkedin: 'LinkedIn',
            website: 'Website',
        },
    },
    en: {
        gpa: 'GPA',
        credential: 'Credential ID',
        expires: 'expires',
        contact: 'Contact',
        contactFields: {
            email: 'Email',
            phone: 'Phone',
            location: 'Location',
            linkedin: 'LinkedIn',
            website: 'Website',
        },
    },
};

const DATE_PATTERN = /^(\d{4})(?:-(0[1-9]|1[0-2]))?$/;

export function formatDate(value: PartialDate, locale: ResumeLocale): string {
    const match = DATE_PATTERN.exec(value ?? '');

    if (!match) {
        return '';
    }

    const [, year, month] = match;

    return month ? `${MONTHS[locale][Number(month) - 1]} ${year}` : year;
}

/**
 * "Jan 2021 – Mar 2023", "Jan 2021 – Sekarang", or a single date when only
 * one end of the range is known.
 */
export function formatRange(
    start: PartialDate,
    end: PartialDate,
    isCurrent: boolean,
    locale: ResumeLocale,
): string {
    const from = formatDate(start, locale);
    const to = isCurrent ? PRESENT[locale] : formatDate(end, locale);

    if (from && to) {
        return from === to ? from : `${from} – ${to}`;
    }

    return from || to;
}

/** Addresses are entered without a scheme as often as with one. */
export function toHref(value: string): string {
    const trimmed = value.trim();

    return /^https?:\/\//i.test(trimmed) ? trimmed : `https://${trimmed}`;
}

/** "https://www.linkedin.com/in/nama/" → "linkedin.com/in/nama" */
export function displayUrl(value: string): string {
    return value
        .trim()
        .replace(/^https?:\/\//i, '')
        .replace(/^www\./i, '')
        .replace(/\/+$/, '');
}

export function hasText(value: string | null | undefined): boolean {
    return typeof value === 'string' && value.trim() !== '';
}

/** Join the non-empty parts with a separator. */
export function joinParts(
    parts: Array<string | null | undefined>,
    separator: string,
): string {
    return parts
        .filter(hasText)
        .map((part) => (part as string).trim())
        .join(separator);
}
