import {
    DOCUMENT_LABELS,
    displayUrl,
    formatDate,
    formatRange,
    hasText,
    joinParts,
    SECTION_TITLES,
    toHref,
} from './format';
import type { ResumeData, ResumeLocale, SectionKey } from './types';

/**
 * A CV is flattened into a list of blocks before layout. Blocks are the units
 * the paginator moves between pages: a page break may fall between two blocks
 * but never inside one. Templates decide how each block looks; this module
 * decides what the blocks are, so every template paginates the same content.
 */

export type ContactItem = {
    kind: 'email' | 'phone' | 'location' | 'linkedin' | 'website';
    label: string;
    href: string | null;
};

export type EntryView = {
    section: SectionKey;
    /** Role, degree, project or certificate name. */
    title: string;
    /** Employer, institution, project role or issuer. */
    subtitle: string;
    location: string;
    dates: string;
    /** Short extra facts shown after the subtitle, e.g. GPA or credential ID. */
    details: string[];
    link: { href: string; label: string } | null;
};

export type HeaderBlock = {
    kind: 'header';
    id: string;
    /**
     * Column layouts can split the header: the name and headline open the
     * main column while the photo and contacts open the side column.
     */
    part: 'all' | 'identity' | 'contact';
    /** Localised title for a contact panel ("Kontak" / "Contact"). */
    contactTitle: string;
    name: string;
    headline: string;
    contacts: ContactItem[];
    photoUrl: string | null;
};

export type HeadingBlock = {
    kind: 'heading';
    id: string;
    section: SectionKey;
    /** 1-based position among the sections that are printed. */
    number: number;
    title: string;
};

export type TextBlock = {
    kind: 'text';
    id: string;
    section: SectionKey;
    text: string;
};

export type EntryBlock = {
    kind: 'entry';
    id: string;
    section: SectionKey;
    group: string;
    entry: EntryView;
    hasBullets: boolean;
};

export type BulletBlock = {
    kind: 'bullet';
    id: string;
    section: SectionKey;
    group: string;
    text: string;
};

export type LineBlock = {
    kind: 'line';
    id: string;
    section: SectionKey;
    label: string;
    /** The whole line as text, e.g. "PHP, Laravel, MySQL". */
    value: string;
    /** The same content split into items, for templates that list them. */
    items: string[];
};

export type Block =
    | HeaderBlock
    | HeadingBlock
    | TextBlock
    | EntryBlock
    | BulletBlock
    | LineBlock;

export type LayoutHint = {
    /** Must share a page with the following block (headings). */
    keepWithNext: boolean;
    /**
     * Blocks that move together while short: one entry with its bullets, or
     * all lines of a list section such as skills.
     */
    group: string | null;
};

export function layoutHint(block: Block): LayoutHint {
    let group: string | null = null;

    if (block.kind === 'entry' || block.kind === 'bullet') {
        group = block.group;
    } else if (block.kind === 'line') {
        group = `${block.section}-lines`;
    }

    return { keepWithNext: block.kind === 'heading', group };
}

export function buildBlocks(data: ResumeData): Block[] {
    const locale = data.locale;
    const blocks: Block[] = [];
    const header = buildHeader(data);

    if (header) {
        blocks.push(header);
    }

    let printedSections = 0;

    for (const section of data.section_order) {
        const body = buildSection(section, data, locale);

        if (body.length > 0) {
            printedSections += 1;
            blocks.push({
                kind: 'heading',
                id: `h-${section}`,
                section,
                number: printedSections,
                title: SECTION_TITLES[locale][section],
            });
            blocks.push(...body);
        }
    }

    return blocks;
}

function buildHeader(data: ResumeData): HeaderBlock | null {
    const p = data.personal;
    const contacts: ContactItem[] = [];

    if (hasText(p.location)) {
        contacts.push({
            kind: 'location',
            label: p.location.trim(),
            href: null,
        });
    }

    if (hasText(p.phone)) {
        contacts.push({
            kind: 'phone',
            label: p.phone.trim(),
            href: `tel:${p.phone.replace(/[^\d+]/g, '')}`,
        });
    }

    if (hasText(p.email)) {
        contacts.push({
            kind: 'email',
            label: p.email.trim(),
            href: `mailto:${p.email.trim()}`,
        });
    }

    if (hasText(p.linkedin)) {
        contacts.push({
            kind: 'linkedin',
            label: displayUrl(p.linkedin),
            href: toHref(p.linkedin),
        });
    }

    if (hasText(p.website)) {
        contacts.push({
            kind: 'website',
            label: displayUrl(p.website),
            href: toHref(p.website),
        });
    }

    if (
        !hasText(p.full_name) &&
        !hasText(p.headline) &&
        contacts.length === 0 &&
        !data.photo_url
    ) {
        return null;
    }

    return {
        kind: 'header',
        id: 'header',
        part: 'all',
        contactTitle: DOCUMENT_LABELS[data.locale].contact,
        name: p.full_name.trim(),
        headline: p.headline.trim(),
        contacts,
        photoUrl: data.photo_url,
    };
}

function buildSection(
    section: SectionKey,
    data: ResumeData,
    locale: ResumeLocale,
): Block[] {
    const labels = DOCUMENT_LABELS[locale];

    switch (section) {
        case 'summary':
            return hasText(data.summary)
                ? [
                      {
                          kind: 'text',
                          id: 'summary',
                          section,
                          text: data.summary.trim(),
                      },
                  ]
                : [];

        case 'experience':
        case 'internship':
        case 'organization': {
            const items =
                section === 'experience'
                    ? data.experiences
                    : data.activities.filter((item) => item.type === section);

            return items
                .filter(
                    (item) =>
                        hasText(item.position) || hasText(item.organization),
                )
                .flatMap((item, index) =>
                    entryWithBullets(section, index, item.highlights, {
                        title: item.position.trim(),
                        subtitle: item.organization.trim(),
                        location: item.location.trim(),
                        dates: formatRange(
                            item.start_date,
                            item.end_date,
                            item.is_current,
                            locale,
                        ),
                        details: [],
                        link: null,
                    }),
                );
        }

        case 'education':
            return data.educations
                .filter(
                    (item) =>
                        hasText(item.institution) ||
                        hasText(item.degree) ||
                        hasText(item.field_of_study),
                )
                .flatMap((item, index) =>
                    entryWithBullets(section, index, item.highlights, {
                        title: joinParts(
                            [item.degree, item.field_of_study],
                            ', ',
                        ),
                        subtitle: item.institution.trim(),
                        location: item.location.trim(),
                        dates: formatRange(
                            item.start_date,
                            item.end_date,
                            item.is_current,
                            locale,
                        ),
                        details: hasText(item.gpa)
                            ? [`${labels.gpa} ${item.gpa.trim()}`]
                            : [],
                        link: null,
                    }),
                );

        case 'projects':
            return data.projects
                .filter((item) => hasText(item.name))
                .flatMap((item, index) =>
                    entryWithBullets(section, index, item.highlights, {
                        title: item.name.trim(),
                        subtitle: item.role.trim(),
                        location: '',
                        dates: formatRange(
                            item.start_date,
                            item.end_date,
                            item.is_current,
                            locale,
                        ),
                        details: [],
                        link: hasText(item.url)
                            ? {
                                  href: toHref(item.url),
                                  label: displayUrl(item.url),
                              }
                            : null,
                    }),
                );

        case 'certifications':
            return data.certifications
                .filter((item) => hasText(item.name))
                .flatMap((item, index) => {
                    const expires = formatDate(item.expires_at, locale);
                    const details: string[] = [];

                    if (expires) {
                        details.push(`${labels.expires} ${expires}`);
                    }

                    if (hasText(item.credential_id)) {
                        details.push(
                            `${labels.credential}: ${item.credential_id.trim()}`,
                        );
                    }

                    return entryWithBullets(section, index, [], {
                        title: item.name.trim(),
                        subtitle: item.issuer.trim(),
                        location: '',
                        dates: formatDate(item.issued_at, locale),
                        details,
                        link: hasText(item.url)
                            ? {
                                  href: toHref(item.url),
                                  label: displayUrl(item.url),
                              }
                            : null,
                    });
                });

        case 'skills':
            return data.skill_groups
                .filter((item) => hasText(item.skills))
                .map((item, index) => {
                    const items = splitList(item.skills);

                    return {
                        kind: 'line',
                        id: `skills-${index}`,
                        section,
                        label: item.category.trim(),
                        value: items.join(', '),
                        items,
                    };
                });

        case 'languages': {
            const items = data.languages
                .filter((item) => hasText(item.name))
                .map((item) =>
                    hasText(item.proficiency)
                        ? `${item.name.trim()} (${item.proficiency.trim()})`
                        : item.name.trim(),
                );

            return items.length
                ? [
                      {
                          kind: 'line',
                          id: 'languages',
                          section,
                          label: '',
                          value: items.join(', '),
                          items,
                      },
                  ]
                : [];
        }
    }
}

function entryWithBullets(
    section: SectionKey,
    index: number,
    highlights: string[],
    entry: Omit<EntryView, 'section'>,
): Block[] {
    const group = `${section}-${index}`;
    const bullets = highlights.filter(hasText);

    return [
        {
            kind: 'entry',
            id: group,
            section,
            group,
            entry: { section, ...entry },
            hasBullets: bullets.length > 0,
        },
        ...bullets.map((text, bulletIndex): BulletBlock => ({
            kind: 'bullet',
            id: `${group}-b${bulletIndex}`,
            section,
            group,
            text: text.trim(),
        })),
    ];
}

/** "PHP,Laravel ,  MySQL" → ["PHP", "Laravel", "MySQL"] */
function splitList(value: string): string[] {
    return value
        .split(/[,;\n]/)
        .map((part) => part.trim())
        .filter(Boolean);
}
