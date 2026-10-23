import type { ResumeData, ResumeLocale } from '@/cv/types';

/**
 * One translated text from POST /resumes/{resume}/translate. `path` is a dot
 * path into the stored CV, e.g. "experiences.0.highlights.1".
 */
export type TranslationItem = {
    path: string;
    original: string;
    translated: string;
};

export const LOCALE_NAMES: Record<ResumeLocale, string> = {
    id: 'Bahasa Indonesia',
    en: 'English',
};

const SECTION_NAMES: Record<string, string> = {
    experiences: 'Pengalaman kerja',
    educations: 'Pendidikan',
    projects: 'Proyek',
    skill_groups: 'Keahlian',
    languages: 'Bahasa',
};

const FIELD_NAMES: Record<string, string> = {
    position: 'Posisi',
    degree: 'Jenjang atau gelar',
    field_of_study: 'Jurusan',
    role: 'Peran',
    category: 'Kategori',
    skills: 'Daftar keahlian',
    name: 'Nama',
    proficiency: 'Tingkat kemahiran',
};

/** Same limits as UpdateResumeRequest, so an applied translation still saves. */
const MAX_LENGTHS: Record<string, number> = {
    headline: 150,
    summary: 2000,
    position: 150,
    highlights: 600,
    degree: 150,
    field_of_study: 150,
    role: 150,
    category: 80,
    skills: 500,
    name: 80,
    proficiency: 80,
};

const HIGHLIGHT_PATH = /^([a-z_]+)\.(\d+)\.highlights\.(\d+)$/;

/** Short Indonesian label for a path, e.g. "Pengalaman kerja 1 · Poin 2". */
export function translationLabel(resume: ResumeData, path: string): string {
    if (path === 'personal.headline') {
        return 'Jabatan atau headline';
    }

    if (path === 'summary') {
        return 'Profil singkat';
    }

    const [list, index, key, line] = path.split('.');
    const position = Number(index);
    let entry = `${SECTION_NAMES[list] ?? list} ${position + 1}`;
    let field = FIELD_NAMES[key] ?? key;

    if (list === 'activities') {
        // Internships and organisations are separate sections on the CV, so
        // number each entry within its own type.
        const type = resume.activities[position]?.type ?? 'organization';
        const number = resume.activities
            .slice(0, position + 1)
            .filter((item) => item.type === type).length;
        entry = `${type === 'internship' ? 'Magang' : 'Organisasi'} ${Math.max(number, 1)}`;
        field = key === 'position' ? 'Peran' : field;
    }

    if (key === 'highlights') {
        field = `Poin ${Number(line) + 1}`;
    }

    return `${entry} · ${field}`;
}

/** Longest text the field accepts when the CV is saved. */
export function translationMaxLength(path: string): number {
    const parts = path.split('.');
    const key = parts.includes('highlights')
        ? 'highlights'
        : parts[parts.length - 1];

    return MAX_LENGTHS[key] ?? 150;
}

type Node = Record<string, unknown> | unknown[];

function isNode(value: unknown): value is Node {
    return typeof value === 'object' && value !== null;
}

function child(node: Node, key: string): unknown {
    return Array.isArray(node) ? node[Number(key)] : node[key];
}

function walk(resume: ResumeData, keys: string[]): unknown {
    let node: unknown = resume;

    for (const key of keys) {
        if (!isNode(node)) {
            return undefined;
        }

        node = child(node, key);
    }

    return node;
}

export function getByPath(resume: ResumeData, path: string): unknown {
    return walk(resume, path.split('.'));
}

/** Replaces an existing string at `path`; returns false when there is none. */
export function setByPath(
    resume: ResumeData,
    path: string,
    value: string,
): boolean {
    const keys = path.split('.');
    const last = keys.pop();
    const parent = walk(resume, keys);

    if (
        last === undefined ||
        !isNode(parent) ||
        typeof child(parent, last) !== 'string'
    ) {
        return false;
    }

    if (Array.isArray(parent)) {
        parent[Number(last)] = value;
    } else {
        parent[last] = value;
    }

    return true;
}

/**
 * The server drops blank bullet points, while the editor keeps them until the
 * user removes them; map the stored bullet index to the editor's index.
 */
function editorPath(resume: ResumeData, path: string): string | null {
    const match = HIGHLIGHT_PATH.exec(path);

    if (!match) {
        return path;
    }

    const [, list, index, line] = match;
    const highlights = getByPath(resume, `${list}.${index}.highlights`);

    if (!Array.isArray(highlights)) {
        return null;
    }

    let seen = -1;

    for (const [position, text] of highlights.entries()) {
        if (typeof text === 'string' && text.trim() !== '') {
            seen += 1;

            if (seen === Number(line)) {
                return `${list}.${index}.highlights.${position}`;
            }
        }
    }

    return null;
}

/**
 * Writes one reviewed translation into the editor. The text is only replaced
 * while the field still holds the original that was translated, so a stale
 * suggestion never overwrites other content.
 */
export function applyTranslation(
    resume: ResumeData,
    item: Pick<TranslationItem, 'path' | 'original'>,
    text: string,
): boolean {
    const path = editorPath(resume, item.path);
    const current = path === null ? undefined : getByPath(resume, path);

    if (
        path === null ||
        typeof current !== 'string' ||
        current.trim() !== item.original.trim()
    ) {
        return false;
    }

    return setByPath(resume, path, text);
}
