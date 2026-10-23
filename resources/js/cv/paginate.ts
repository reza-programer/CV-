/**
 * Distributes measured blocks over fixed-height A4 pages.
 *
 * Rules, in order of priority:
 * - a block is never split;
 * - a section heading always shares a page with the block after it;
 * - an entry that is short relative to the page moves as a whole;
 * - a long entry may break between bullets, but its head line keeps at least
 *   one bullet and the last bullet never starts a page alone;
 * - the spacing above a block is dropped when it starts a page.
 *
 * Font size is never reduced to make content fit.
 */

export type BlockMetric = {
    /** Rendered height in px, including the spacing above the block. */
    height: number;
    /** Spacing above the block in px, removed when it opens a page. */
    spaceBefore: number;
    keepWithNext: boolean;
    group: string | null;
};

export type PaginateOptions = {
    /** Entries up to this fraction of a page are kept together. */
    keepTogetherRatio?: number;
};

/** Sub-pixel rounding differs between browsers; allow a hair of slack. */
const EPSILON = 0.5;

/**
 * `capacity` is the usable height per page, or a function of the page index
 * when the first page is shorter (a full-width header above two columns).
 */
export function paginate(
    metrics: BlockMetric[],
    capacity: number | ((page: number) => number),
    options: PaginateOptions = {},
): number[][] {
    const capacityOf = (page: number): number =>
        typeof capacity === 'function' ? capacity(page) : capacity;
    const keepTogetherLimit =
        capacityOf(1) * (options.keepTogetherRatio ?? 0.4);
    const count = metrics.length;
    const groupFirst = new Map<string, number>();
    const groupLast = new Map<string, number>();
    const groupHeight = new Map<string, number>();

    metrics.forEach((metric, index) => {
        if (metric.group === null) {
            return;
        }

        if (!groupFirst.has(metric.group)) {
            groupFirst.set(metric.group, index);
        }

        groupLast.set(metric.group, index);
        groupHeight.set(
            metric.group,
            (groupHeight.get(metric.group) ?? 0) + metric.height,
        );
    });

    /** Exclusive end of the smallest run starting at `start` that must stay on one page. */
    const unitEnd = (start: number): number => {
        let index = start;

        for (;;) {
            const group = metrics[index].group;

            if (group !== null && groupFirst.get(group) === index) {
                const last = groupLast.get(group)!;
                index =
                    groupHeight.get(group)! <= keepTogetherLimit
                        ? last
                        : Math.min(last, index + 1);
            }

            const current = metrics[index].group;

            if (
                current !== null &&
                index + 1 === groupLast.get(current) &&
                groupFirst.get(current) !== index + 1
            ) {
                index += 1;
            }

            if (metrics[index].keepWithNext && index + 1 < count) {
                index += 1;
                continue;
            }

            return index + 1;
        }
    };

    const pages: number[][] = [];
    let page: number[] = [];
    let used = 0;

    const heightOf = (index: number, opensPage: boolean): number =>
        metrics[index].height - (opensPage ? metrics[index].spaceBefore : 0);

    const closePage = () => {
        pages.push(page);
        page = [];
        used = 0;
    };

    let index = 0;

    while (index < count) {
        const end = unitEnd(index);
        const capacityHere = capacityOf(pages.length);
        let unitHeight = 0;

        for (let i = index; i < end; i++) {
            unitHeight += heightOf(i, page.length === 0 && i === index);
        }

        if (used + unitHeight <= capacityHere + EPSILON) {
            for (let i = index; i < end; i++) {
                page.push(i);
            }

            used += unitHeight;
            index = end;
            continue;
        }

        if (page.length > 0) {
            closePage();
            continue;
        }

        // A short first page (under a tall header) may not fit a unit that a
        // full page would: leave this column empty here and start on the next.
        if (unitHeight <= capacityOf(pages.length + 1) + EPSILON) {
            closePage();
            continue;
        }

        // The unit is taller than an empty page: fill this page block by block
        // and continue the rest of the unit on the next one.
        while (index < end) {
            const height = heightOf(index, page.length === 0);

            if (page.length > 0 && used + height > capacityHere + EPSILON) {
                break;
            }

            page.push(index);
            used += height;
            index += 1;
        }

        closePage();
    }

    if (page.length > 0 || pages.length === 0) {
        pages.push(page);
    }

    return pages;
}
