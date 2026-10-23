import type { Block, HeaderBlock } from './blocks';
import type { SectionKey } from './types';

/**
 * How a template places blocks on the page.
 *
 * - `single`: one flow, the header first. Every ATS-oriented template.
 * - `columns`: a main column plus a narrower side column, each paginated on
 *   its own. The header either spans the top of the first page (`top`) or is
 *   split so the name opens the main column and the photo and contacts open
 *   the side column (`split`).
 */
export type TemplateLayout =
    | { kind: 'single' }
    | {
          kind: 'columns';
          /** Sections shown in the side column; all others go to the main one. */
          aside: SectionKey[];
          /** Visual side of the side column; also its place in reading order. */
          asideSide: 'left' | 'right';
          header: 'top' | 'split';
          /** Sections shown full width under a `top` header, e.g. the profile. */
          topSections?: SectionKey[];
      };

export type Region = 'top' | 'main' | 'aside';

export type Regions = Record<Region, Block[]>;

export function splitRegions(blocks: Block[], layout: TemplateLayout): Regions {
    const regions: Regions = { top: [], main: [], aside: [] };

    if (layout.kind === 'single') {
        regions.main = blocks;

        return regions;
    }

    for (const block of blocks) {
        if (block.kind === 'header') {
            placeHeader(block, layout, regions);
            continue;
        }

        if (layout.topSections?.includes(block.section)) {
            regions.top.push(block);
        } else if (layout.aside.includes(block.section)) {
            regions.aside.push(block);
        } else {
            regions.main.push(block);
        }
    }

    return regions;
}

function placeHeader(
    header: HeaderBlock,
    layout: Extract<TemplateLayout, { kind: 'columns' }>,
    regions: Regions,
): void {
    if (layout.header === 'top') {
        regions.top.push(header);

        return;
    }

    if (header.name || header.headline) {
        regions.main.push({
            ...header,
            id: 'header-identity',
            part: 'identity',
        });
    }

    if (header.photoUrl || header.contacts.length) {
        regions.aside.push({
            ...header,
            id: 'header-contact',
            part: 'contact',
        });
    }
}
