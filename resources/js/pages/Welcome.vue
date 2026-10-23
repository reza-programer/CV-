<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Check, Plus } from '@lucide/vue';
import { computed, ref } from 'vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import PaperTile from '@/components/cv/PaperTile.vue';
import TemplateBadges from '@/components/resumes/TemplateBadges.vue';
import TemplateFilterBar from '@/components/resumes/TemplateFilterBar.vue';
import type { TemplateFilter } from '@/components/resumes/templateFilters';
import { filterTemplates } from '@/components/resumes/templateFilters';
import AtsTextView from '@/components/site/AtsTextView.vue';
import SectionHeading from '@/components/site/SectionHeading.vue';
import SiteFooter from '@/components/site/SiteFooter.vue';
import SiteHeader from '@/components/site/SiteHeader.vue';
import TemplatePreviewDialog from '@/components/site/TemplatePreviewDialog.vue';
import { Button } from '@/components/ui/button';
import { SAMPLE_NAME, sampleResume } from '@/cv/sample';
import { isMultiColumn } from '@/cv/templates';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';
import { dashboard } from '@/routes';
import * as demo from '@/routes/demo';

const props = defineProps<{
    templates: TemplateSummary[];
    translationEnabled: boolean;
}>();

const page = usePage();
const signedIn = computed(() => !!page.props.auth?.user);
// Visitors try the editor first and sign up when they want to keep the CV.
const startHref = computed(() =>
    signedIn.value ? dashboard().url : demo.show().url,
);

/** A few structurally different layouts to flip through in the hero. */
const HERO_TEMPLATES: TemplateSlug[] = [
    'professional',
    'classic',
    'sidebar',
    'two-column',
];
const heroTemplates = computed(() =>
    HERO_TEMPLATES.flatMap(
        (slug) => props.templates.find((item) => item.slug === slug) ?? [],
    ),
);
const heroTemplate = ref<TemplateSlug>(
    heroTemplates.value[0]?.slug ?? props.templates[0]?.slug ?? 'classic',
);
// The hero keeps one person across templates: same content, different layout.
const heroResume = computed(() =>
    sampleResume(heroTemplate.value, 'developer'),
);
const atsResume = sampleResume('classic');

const filter = ref<TemplateFilter>('all');
const visibleTemplates = computed(() =>
    filterTemplates(props.templates, filter.value),
);
const previewing = ref<TemplateSlug | null>(null);

const singleColumnCount = computed(
    () =>
        props.templates.filter((template) => !isMultiColumn(template.slug))
            .length,
);

const stats = computed(() => [
    { value: String(props.templates.length), label: 'template siap pakai' },
    {
        value: String(singleColumnCount.value),
        label: 'tata letak satu kolom ramah ATS',
    },
    { value: 'A4', label: 'margin dan pergantian halaman rapi' },
    { value: 'ID · EN', label: 'dua bahasa dokumen' },
]);

const steps = [
    {
        title: 'Isi data sekali',
        text: 'Data diri, pengalaman, pendidikan, dan keahlian dalam formulir yang tertata. Bagian yang kosong tidak ikut tercetak.',
    },
    {
        title: 'Pilih tampilan',
        text: 'Ganti template, warna aksen, perataan paragraf, atau bahasa kapan saja. Isi CV tetap utuh.',
    },
    {
        title: 'Unduh PDF A4',
        text: 'Yang tampil di pratinjau adalah yang tercetak: teks asli, margin tepat, dan halaman yang terbagi rapi.',
    },
];

const atsPoints = [
    {
        title: 'Teks asli, bukan gambar',
        text: 'PDF tersusun dari teks yang dapat diseleksi dan dicari, bukan tangkapan layar.',
    },
    {
        title: 'Judul bagian yang lazim',
        text: 'Pengalaman Kerja, Pendidikan, Keahlian: istilah yang dikenali sistem dan perekrut.',
    },
    {
        title: 'Urutan baca yang jelas',
        text: 'Template satu kolom dibaca dari atas ke bawah, tanpa tabel atau kotak teks.',
    },
    {
        title: 'Tanpa grafik pengganti isi',
        text: 'Tidak ada bar keahlian atau ikon pengganti kontak. Semua informasi tertulis.',
    },
];

const features = computed(() => [
    {
        title: 'Pratinjau A4 langsung',
        text: 'Setiap ketikan langsung tampil di halaman A4, lengkap dengan pergantian halaman yang sama seperti PDF.',
    },
    {
        title: 'Ganti template tanpa mengetik ulang',
        text: 'Isi CV disimpan terpisah dari tata letak. Pindah template atau warna aksen tanpa kehilangan isi.',
    },
    {
        title: 'Dua bahasa dokumen',
        text: props.translationEnabled
            ? 'Judul bagian dan format tanggal mengikuti Bahasa Indonesia atau English. Isi dapat diterjemahkan asisten AI, lalu Anda periksa sebelum diterapkan.'
            : 'Judul bagian dan format tanggal mengikuti Bahasa Indonesia atau English, sesuai lowongan yang dituju.',
    },
    {
        title: 'Foto opsional',
        text: 'Pasang satu foto bila lowongan memintanya, otomatis dipotong ke rasio 3:4. Kosongkan bila tidak perlu.',
    },
    {
        title: 'Bagian kosong tidak tercetak',
        text: 'Belum punya sertifikasi atau proyek? Bagian itu tidak muncul, jadi tidak ada judul yang menggantung.',
    },
    {
        title: 'Tersimpan otomatis dan privat',
        text: 'Perubahan tersimpan sendiri. Hanya Anda yang dapat membuka CV dan foto Anda.',
    },
]);

const faqs = computed(() => [
    {
        question: 'Apa itu ATS dan mengapa CV perlu ramah ATS?',
        answer: 'ATS (Applicant Tracking System) adalah perangkat lunak yang dipakai perusahaan untuk menyaring lamaran. Sistem ini membaca teks CV untuk mencocokkan pengalaman dan keahlian dengan lowongan, sehingga CV berteks asli dengan struktur yang jelas lebih mudah terbaca dengan benar.',
    },
    {
        question: 'Apakah template Kreatif aman untuk ATS?',
        answer: 'Template Kreatif memakai dua kolom. Teksnya tetap asli, tetapi sebagian ATS lama dapat mencampur urutan kolom. Untuk melamar lewat portal lowongan, gunakan template satu kolom; template Kreatif cocok untuk dikirim langsung ke perekrut.',
    },
    {
        question: 'Perlukah memasang foto?',
        answer: 'Tidak wajib. Banyak perusahaan di Indonesia masih meminta foto, sementara lowongan internasional umumnya tidak. Foto bersifat opsional dan dapat dihapus kapan saja.',
    },
    {
        question: 'Bisakah membuat CV dalam bahasa Inggris?',
        answer: props.translationEnabled
            ? 'Bisa. Pilih English sebagai bahasa dokumen, maka judul bagian dan format tanggal ikut berganti. Asisten AI dapat menerjemahkan isi CV, dan hasilnya selalu ditampilkan untuk Anda periksa sebelum diterapkan.'
            : 'Bisa. Pilih English sebagai bahasa dokumen, maka judul bagian dan format tanggal ikut berganti. Isi CV Anda tulis sendiri dalam bahasa yang dipilih.',
    },
    {
        question: 'Berapa halaman CV yang ideal?',
        answer: 'Umumnya satu halaman untuk lulusan baru dan dua halaman untuk pengalaman yang lebih panjang. Pratinjau menunjukkan jumlah halaman secara langsung, dan judul bagian tidak akan tertinggal sendirian di bawah halaman.',
    },
    {
        question: 'Harus daftar dulu sebelum mencoba?',
        answer: 'Tidak. Tombol Buat CV membuka editor berisi contoh yang bisa langsung Anda ubah, lengkap dengan pratinjau A4. Daftar gratis saat ingin menyimpan, menambahkan foto, atau mengunduh PDF; isi yang sudah Anda ketik ikut tersimpan ke akun.',
    },
    {
        question: 'Apakah data saya aman?',
        answer: 'CV dan foto hanya dapat dibuka oleh pemilik akun. Anda dapat menghapus CV beserta fotonya kapan saja dari halaman CV Saya.',
    },
]);
</script>

<template>
    <Head title="Buat CV Profesional, Tanpa Ribet" />

    <div class="min-h-svh bg-white text-ink">
        <SiteHeader />

        <main>
            <!-- Hero ------------------------------------------------------ -->
            <section class="border-b border-neutral-200">
                <div
                    class="mx-auto grid max-w-6xl grid-cols-1 items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[minmax(0,_1fr)_minmax(0,_29rem)] lg:gap-16 lg:py-20"
                >
                    <div class="max-w-xl min-w-0">
                        <p class="eyebrow">Pembuat CV ATS-friendly</p>
                        <h1
                            class="mt-4 font-display text-[2.6rem] leading-[1.05] font-medium tracking-tight sm:text-6xl"
                        >
                            Buat CV Profesional, Tanpa Ribet
                        </h1>
                        <p
                            class="mt-6 text-base leading-relaxed text-neutral-600 sm:text-lg"
                        >
                            Isi data Anda sekali, pilih dari
                            {{ templates.length }} template, lalu unduh PDF A4
                            yang rapi dan berteks asli. Yang Anda lihat di
                            pratinjau adalah yang tercetak.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Button as-child size="lg" class="px-6">
                                <Link :href="startHref">Buat CV</Link>
                            </Button>
                            <Button
                                as-child
                                size="lg"
                                variant="outline"
                                class="px-6"
                            >
                                <a href="#template">Lihat template</a>
                            </Button>
                        </div>
                        <ul class="mt-10 grid gap-2.5 text-sm text-neutral-700">
                            <li
                                v-for="point in [
                                    'PDF berteks asli yang terbaca sistem ATS',
                                    'Pratinjau A4 sama persis dengan hasil cetak',
                                    'Bahasa Indonesia dan English',
                                ]"
                                :key="point"
                                class="flex items-center gap-2.5"
                            >
                                <Check
                                    class="size-4 shrink-0 text-ink-blue"
                                    :stroke-width="2.25"
                                />
                                {{ point }}
                            </li>
                        </ul>
                    </div>

                    <figure class="min-w-0">
                        <div
                            class="rounded-lg bg-desk px-8 pt-10 pb-8 sm:px-12 sm:pt-12"
                        >
                            <div class="relative mx-auto max-w-[23rem]">
                                <!-- A second sheet behind the first hints at multi-page CVs. -->
                                <div
                                    class="absolute inset-0 translate-x-3 translate-y-3 paper"
                                    aria-hidden="true"
                                />
                                <div class="relative paper">
                                    <CvThumbnail :data="heroResume" />
                                </div>
                            </div>

                            <div
                                class="mt-9 grid grid-cols-2 gap-1 sm:flex sm:flex-wrap sm:justify-center"
                                role="group"
                                aria-label="Contoh template"
                            >
                                <button
                                    v-for="template in heroTemplates"
                                    :key="template.slug"
                                    type="button"
                                    class="rounded-[5px] px-2.5 py-1 text-xs transition-colors focus-visible:ring-2 focus-visible:ring-neutral-400 focus-visible:outline-none"
                                    :class="
                                        heroTemplate === template.slug
                                            ? 'bg-ink text-white'
                                            : 'text-neutral-600 hover:bg-white hover:text-ink'
                                    "
                                    :aria-pressed="
                                        heroTemplate === template.slug
                                    "
                                    @click="heroTemplate = template.slug"
                                >
                                    {{ template.name.replace(/ ATS$/, '') }}
                                </button>
                            </div>
                        </div>
                        <figcaption
                            class="mt-3 text-center text-xs text-neutral-500"
                        >
                            Contoh CV · {{ SAMPLE_NAME }}
                        </figcaption>
                    </figure>
                </div>
            </section>

            <!-- Facts ----------------------------------------------------- -->
            <section aria-label="Ringkasan" class="border-b border-neutral-200">
                <dl
                    class="mx-auto grid max-w-6xl grid-cols-2 px-4 sm:px-6 lg:grid-cols-4"
                >
                    <div
                        v-for="(stat, index) in stats"
                        :key="stat.label"
                        class="border-neutral-200 py-8 lg:px-8 lg:first:pl-0"
                        :class="[
                            index % 2 === 1 ? 'pl-6' : 'pr-6',
                            index < 2 ? 'border-b lg:border-b-0' : '',
                            index > 0 ? 'lg:border-l' : '',
                            index % 2 === 1 ? 'border-l' : '',
                        ]"
                    >
                        <dt class="sr-only">{{ stat.label }}</dt>
                        <dd>
                            <span
                                class="block font-display text-3xl font-medium tracking-tight sm:text-4xl"
                                >{{ stat.value }}</span
                            >
                            <span
                                class="mt-1 block text-sm leading-snug text-neutral-600"
                                >{{ stat.label }}</span
                            >
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- How it works ---------------------------------------------- -->
            <section
                id="cara-kerja"
                class="scroll-mt-16 border-b border-neutral-200"
            >
                <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-24">
                    <SectionHeading
                        eyebrow="Cara kerja"
                        title="Tiga langkah menuju CV yang siap dikirim"
                    />
                    <ol class="mt-12 grid gap-10 md:grid-cols-3 md:gap-8">
                        <li
                            v-for="(step, index) in steps"
                            :key="step.title"
                            class="border-t border-ink pt-6"
                        >
                            <span
                                class="font-display text-2xl font-medium text-ink-blue tabular-nums"
                                >0{{ index + 1 }}</span
                            >
                            <h3
                                class="mt-3 text-lg font-semibold tracking-tight"
                            >
                                {{ step.title }}
                            </h3>
                            <p
                                class="mt-2 text-[15px] leading-relaxed text-neutral-600"
                            >
                                {{ step.text }}
                            </p>
                        </li>
                    </ol>
                </div>
            </section>

            <!-- Templates ------------------------------------------------- -->
            <section
                id="template"
                class="scroll-mt-16 border-b border-neutral-200"
            >
                <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-24">
                    <div
                        class="flex flex-wrap items-end justify-between gap-x-10 gap-y-6"
                    >
                        <SectionHeading
                            eyebrow="Template"
                            :title="`${templates.length} tata letak, dari ATS murni hingga kreatif`"
                            lead="Semua template memakai isi yang sama, jadi Anda bisa berpindah kapan saja. Satu kolom untuk portal lowongan; Kreatif untuk dikirim langsung ke perekrut."
                        />
                        <TemplateFilterBar
                            v-model="filter"
                            :templates="templates"
                        />
                    </div>

                    <ul
                        class="mt-12 grid grid-cols-2 gap-x-5 gap-y-10 sm:gap-x-6 lg:grid-cols-4"
                    >
                        <li
                            v-for="template in visibleTemplates"
                            :key="template.slug"
                            class="min-w-0"
                        >
                            <button
                                type="button"
                                class="group block w-full rounded-md text-left focus-visible:ring-2 focus-visible:ring-neutral-400 focus-visible:ring-offset-4 focus-visible:outline-none"
                                :aria-label="`Lihat template ${template.name}`"
                                @click="previewing = template.slug"
                            >
                                <PaperTile
                                    :data="sampleResume(template.slug)"
                                />
                                <span
                                    class="mt-4 flex flex-wrap items-center gap-1.5 text-[15px] font-medium"
                                >
                                    {{ template.name }}
                                    <TemplateBadges :slug="template.slug" />
                                </span>
                                <span
                                    class="mt-1 line-clamp-2 block text-sm leading-snug text-neutral-600"
                                    >{{ template.description }}</span
                                >
                            </button>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- ATS ------------------------------------------------------- -->
            <section id="ats" class="scroll-mt-16 border-b border-neutral-200">
                <div
                    class="mx-auto grid max-w-6xl grid-cols-1 gap-14 px-4 py-20 sm:px-6 lg:grid-cols-[minmax(0,_1fr)_minmax(0,_1.1fr)] lg:items-center lg:py-24"
                >
                    <div class="min-w-0">
                        <SectionHeading
                            eyebrow="Ramah ATS"
                            title="Rapi di mata perekrut, terbaca oleh sistem"
                            lead="Banyak perusahaan menyaring lamaran dengan Applicant Tracking System sebelum dibaca manusia. ATS membaca teks, bukan tampilan."
                        />
                        <dl class="mt-10 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                            <div
                                v-for="point in atsPoints"
                                :key="point.title"
                                class="border-t border-neutral-200 pt-4"
                            >
                                <dt class="font-medium">{{ point.title }}</dt>
                                <dd
                                    class="mt-1.5 text-sm leading-relaxed text-neutral-600"
                                >
                                    {{ point.text }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div
                        class="grid min-w-0 grid-cols-1 gap-6 rounded-lg bg-desk p-6 sm:grid-cols-2 sm:p-8"
                    >
                        <figure class="min-w-0">
                            <figcaption
                                class="mb-3 text-xs font-medium text-neutral-600"
                            >
                                Dilihat perekrut
                            </figcaption>
                            <div class="paper">
                                <CvThumbnail :data="atsResume" />
                            </div>
                        </figure>
                        <figure class="flex min-w-0 flex-col">
                            <figcaption
                                class="mb-3 text-xs font-medium text-neutral-600"
                            >
                                Dibaca ATS
                            </figcaption>
                            <!-- Same sheet size as the rendered page beside it. -->
                            <div
                                class="aspect-[210/297] overflow-hidden paper px-4 py-4"
                            >
                                <AtsTextView :data="atsResume" :limit="40" />
                            </div>
                        </figure>
                    </div>
                </div>
            </section>

            <!-- Features -------------------------------------------------- -->
            <section
                id="fitur"
                class="scroll-mt-16 border-b border-neutral-200"
            >
                <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:py-24">
                    <SectionHeading
                        eyebrow="Fitur"
                        title="Semua yang dibutuhkan, tanpa yang tidak perlu"
                    />
                    <dl
                        class="mt-12 grid gap-x-10 gap-y-10 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="border-t border-neutral-200 pt-5"
                        >
                            <dt class="font-semibold tracking-tight">
                                {{ feature.title }}
                            </dt>
                            <dd
                                class="mt-2 text-[15px] leading-relaxed text-neutral-600"
                            >
                                {{ feature.text }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- FAQ ------------------------------------------------------- -->
            <section id="faq" class="scroll-mt-16 border-b border-neutral-200">
                <div
                    class="mx-auto grid max-w-6xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[minmax(0,_1fr)_minmax(0,_1.6fr)] lg:gap-16 lg:py-24"
                >
                    <SectionHeading
                        eyebrow="FAQ"
                        title="Pertanyaan yang sering diajukan"
                    />
                    <div class="border-t border-neutral-200">
                        <details
                            v-for="item in faqs"
                            :key="item.question"
                            class="group border-b border-neutral-200"
                        >
                            <summary
                                class="flex cursor-pointer list-none items-start justify-between gap-6 py-5 text-left font-medium [&::-webkit-details-marker]:hidden"
                            >
                                {{ item.question }}
                                <Plus
                                    class="mt-0.5 size-4 shrink-0 text-neutral-500 transition-transform group-open:rotate-45"
                                />
                            </summary>
                            <p
                                class="-mt-1 pr-10 pb-6 text-[15px] leading-relaxed text-neutral-600"
                            >
                                {{ item.answer }}
                            </p>
                        </details>
                    </div>
                </div>
            </section>

            <!-- Call to action -------------------------------------------- -->
            <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-20">
                <div
                    class="flex flex-wrap items-center justify-between gap-8 rounded-lg bg-desk px-6 py-12 sm:px-12"
                >
                    <div class="max-w-xl">
                        <h2
                            class="font-display text-3xl leading-tight font-medium tracking-tight sm:text-4xl"
                        >
                            Siap menyusun CV yang rapi?
                        </h2>
                        <p class="mt-3 text-neutral-600">
                            Mulai dari data Anda, pilih tampilan, dan unduh PDF
                            dalam hitungan menit.
                        </p>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <Button as-child size="lg" class="px-6">
                            <Link :href="startHref">Buat CV sekarang</Link>
                        </Button>
                        <span class="text-xs text-neutral-500">{{
                            signedIn
                                ? 'Semua CV Anda tersimpan di halaman CV Saya.'
                                : 'Coba dulu tanpa daftar. Daftar gratis saat ingin menyimpan.'
                        }}</span>
                    </div>
                </div>
            </section>
        </main>

        <SiteFooter />

        <TemplatePreviewDialog v-model="previewing" :templates="templates" />
    </div>
</template>
