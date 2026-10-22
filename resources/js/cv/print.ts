/**
 * Entry point of the PDF print bundle (vite.print.config.ts).
 *
 * ResumePdfRenderer inlines this script, its stylesheet and the CV payload
 * into a standalone HTML page and lets headless Chrome print it. The page
 * signals completion through `window.__CV_READY__` once fonts, the photo and
 * pagination have settled.
 */
import { createApp, h, ref } from 'vue';
import CvDocument from './CvDocument.vue';
import './styles/print.css';
import type { ResumeData } from './types';

declare global {
    interface Window {
        __CV_READY__?: boolean;
        __CV_PAGES__?: number;
    }
}

async function main(): Promise<void> {
    const source = document.getElementById('cv-data');
    const data = JSON.parse(source?.textContent ?? '{}') as ResumeData;
    const documentRef = ref<InstanceType<typeof CvDocument> | null>(null);

    createApp({
        render: () => h(CvDocument, { data, ref: documentRef }),
    }).mount('#cv-print');

    await document.fonts.ready;
    await Promise.all(
        Array.from(document.images).map((image) =>
            image.decode().catch(() => undefined),
        ),
    );

    window.__CV_PAGES__ = await documentRef.value?.layout();
    window.__CV_READY__ = true;
}

void main();
