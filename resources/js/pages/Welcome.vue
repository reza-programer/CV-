<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useResizeObserver } from '@vueuse/core';
import { computed, ref } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import { Button } from '@/components/ui/button';
import CvDocument from '@/cv/CvDocument.vue';
import { sampleResume } from '@/cv/sample';
import type { TemplateSlug, TemplateSummary } from '@/cv/types';
import { dashboard, login, register } from '@/routes';

const props = defineProps<{ templates: TemplateSummary[] }>();

const page = usePage();
const signedIn = computed(() => !!page.props.auth?.user);
const startHref = computed(() => (signedIn.value ? dashboard() : register()));

const PAGE_WIDTH = (210 / 25.4) * 96;
const previewBox = ref<HTMLElement | null>(null);
const previewWidth = ref(0);
const previewTemplate = ref<TemplateSlug>(
    props.templates[0]?.slug ?? 'classic',
);

useResizeObserver(previewBox, ([entry]) => {
    previewWidth.value = entry.contentRect.width;
});

const previewScale = computed(() =>
    previewWidth.value ? previewWidth.value / PAGE_WIDTH : 0.6,
);
const preview = computed(() => sampleResume(previewTemplate.value));
const previewName = computed(
    () =>
        props.templates.find(
            (template) => template.slug === previewTemplate.value,
        )?.name,
);

function showTemplate(slug: TemplateSlug): void {
    previewTemplate.value = slug;
    document
        .getElementById('contoh')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

const features = [
    {
        title: 'Pratinjau A4 langsung',
        text: 'Setiap ketikan langsung terlihat di halaman A4, lengkap dengan pergantian halaman yang sama seperti PDF.',
    },
    {
        title: 'PDF berteks asli',
        text: 'Teks pada PDF dapat diseleksi dan dicari, sehingga terbaca oleh sistem pelacak lamaran (ATS), bukan gambar tangkapan layar.',
    },
    {
        title: 'Ganti template tanpa mengetik ulang',
        text: 'Isi CV disimpan terpisah dari tata letak. Pindah template atau bahasa dokumen kapan saja.',
    },
    {
        title: 'Tersimpan otomatis dan privat',
        text: 'Perubahan tersimpan sendiri. Hanya Anda yang dapat membuka CV dan foto Anda.',
    },
];
</script>

<template>
    <Head title="Buat CV Profesional, Tanpa Ribet" />

    <div class="min-h-svh bg-white text-neutral-900">
        <header class="border-b border-neutral-200">
            <div
                class="mx-auto flex h-14 max-w-6xl items-center gap-4 px-4 sm:px-6"
            >
                <Link href="/" aria-label="CVForge">
                    <AppLogo />
                </Link>
                <nav
                    class="ml-auto flex items-center gap-1 text-sm"
                    aria-label="Navigasi"
                >
                    <a
                        href="#template"
                        class="hidden rounded-md px-3 py-2 text-neutral-600 hover:text-neutral-900 sm:block"
                        >Template</a
                    >
                    <a
                        href="#fitur"
                        class="hidden rounded-md px-3 py-2 text-neutral-600 hover:text-neutral-900 sm:block"
                        >Fitur</a
                    >
                    <template v-if="signedIn">
                        <Button as-child size="sm" class="ml-2">
                            <Link :href="dashboard()">CV Saya</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-md px-3 py-2 text-neutral-700 hover:text-neutral-900"
                            >Masuk</Link
                        >
                        <Button as-child size="sm" class="ml-1">
                            <Link :href="register()">Buat CV</Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <section class="border-b border-neutral-200">
                <div
                    class="mx-auto grid max-w-6xl grid-cols-1 items-start gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[minmax(0,_1fr)_minmax(0,_440px)] lg:py-20"
                >
                    <div class="max-w-xl min-w-0 lg:pt-10">
                        <h1
                            class="text-4xl leading-[1.1] font-semibold tracking-tight sm:text-5xl"
                        >
                            Buat CV Profesional, Tanpa Ribet
                        </h1>
                        <p
                            class="mt-5 text-base leading-relaxed text-neutral-600 sm:text-lg"
                        >
                            Isi data Anda sekali, pilih template satu kolom yang
                            ramah ATS, lalu unduh PDF A4 yang rapi dan berteks
                            asli. Yang Anda lihat di pratinjau adalah yang
                            tercetak.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <Button as-child size="lg">
                                <Link :href="startHref">Buat CV</Link>
                            </Button>
                            <Button as-child size="lg" variant="outline">
                                <a href="#template">Lihat template</a>
                            </Button>
                        </div>
                        <dl
                            class="mt-12 grid max-w-md grid-cols-3 gap-6 border-t border-neutral-200 pt-6 text-sm"
                        >
                            <div>
                                <dt class="text-neutral-500">Format</dt>
                                <dd class="mt-1 font-medium">A4, satu kolom</dd>
                            </div>
                            <div>
                                <dt class="text-neutral-500">Template</dt>
                                <dd class="mt-1 font-medium">
                                    {{ templates.length }} tata letak
                                </dd>
                            </div>
                            <div>
                                <dt class="text-neutral-500">Ekspor</dt>
                                <dd class="mt-1 font-medium">PDF teks asli</dd>
                            </div>
                        </dl>
                    </div>

                    <figure
                        id="contoh"
                        class="mx-auto w-full max-w-[440px] min-w-0 scroll-mt-6"
                    >
                        <div class="cv-landing bg-neutral-100 p-5 sm:p-8">
                            <div ref="previewBox" class="mx-auto w-full">
                                <CvDocument
                                    :data="preview"
                                    :scale="previewScale"
                                    :page-limit="1"
                                />
                            </div>
                        </div>
                        <figcaption class="mt-3 text-xs text-neutral-500">
                            Contoh CV · {{ previewName }} · dirender oleh mesin
                            yang sama dengan PDF
                        </figcaption>
                    </figure>
                </div>
            </section>

            <section
                id="template"
                class="scroll-mt-4 border-b border-neutral-200"
            >
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-semibold tracking-tight">
                            Template
                        </h2>
                        <p class="mt-2 text-neutral-600">
                            Empat tata letak dengan struktur berbeda. Semuanya
                            hitam-putih, satu kolom, tanpa grafik atau ikon yang
                            mengganggu pembacaan ATS.
                        </p>
                    </div>

                    <ul
                        class="mt-10 grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4"
                    >
                        <li v-for="template in templates" :key="template.slug">
                            <button
                                type="button"
                                class="group block w-full text-left"
                                :aria-label="`Tampilkan contoh ${template.name}`"
                                @click="showTemplate(template.slug)"
                            >
                                <span
                                    class="block overflow-hidden border bg-white transition-colors"
                                    :class="
                                        previewTemplate === template.slug
                                            ? 'border-neutral-900'
                                            : 'border-neutral-200 group-hover:border-neutral-400'
                                    "
                                >
                                    <CvThumbnail
                                        :data="sampleResume(template.slug)"
                                    />
                                </span>
                                <span class="mt-3 block text-sm font-medium">{{
                                    template.name
                                }}</span>
                                <span
                                    class="mt-1 block text-sm leading-snug text-neutral-600"
                                    >{{ template.description }}</span
                                >
                            </button>
                        </li>
                    </ul>
                </div>
            </section>

            <section id="fitur" class="scroll-mt-4 border-b border-neutral-200">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <h2 class="text-2xl font-semibold tracking-tight">
                        Dibuat untuk dokumen yang rapi
                    </h2>
                    <dl
                        class="mt-10 grid gap-x-10 gap-y-8 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="border-t border-neutral-900 pt-4"
                        >
                            <dt class="font-medium">{{ feature.title }}</dt>
                            <dd
                                class="mt-2 text-sm leading-relaxed text-neutral-600"
                            >
                                {{ feature.text }}
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-14 flex flex-wrap items-center gap-4">
                        <Button as-child size="lg">
                            <Link :href="startHref">Buat CV sekarang</Link>
                        </Button>
                        <span class="text-sm text-neutral-500"
                            >Daftar dengan email, tanpa kartu kredit.</span
                        >
                    </div>
                </div>
            </section>
        </main>

        <footer
            class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-8 text-xs text-neutral-500 sm:px-6"
        >
            <span>CVForge</span>
            <span>Pembuat CV ATS-friendly dalam format A4</span>
        </footer>
    </div>
</template>
