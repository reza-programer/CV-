import type { ResumeData, TemplateSlug } from './types';

/**
 * Example content for the landing page preview and template thumbnails.
 * It is labelled as an example wherever it appears and is never copied into
 * a user's CV. The employers and figures are illustrative.
 */
/** Templates whose layout is built around a photo show a neutral silhouette. */
const PHOTO_TEMPLATES: TemplateSlug[] = [
    'portrait',
    'timeline',
    'sidebar',
    'sidebar-light',
    'two-column',
];

const SAMPLE_PHOTO = `data:image/svg+xml,${encodeURIComponent(
    "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 400'><rect width='300' height='400' fill='#d9dee5'/><circle cx='150' cy='155' r='68' fill='#a7b0bc'/><path d='M28 400c0-82 55-132 122-132s122 50 122 132z' fill='#a7b0bc'/></svg>",
)}`;

export const SAMPLE_NAME = 'Mohamad Reza Nurpadilah';

export function sampleResume(template: TemplateSlug = 'classic'): ResumeData {
    return {
        title: 'Contoh CV',
        template,
        locale: 'id',
        paragraph_align: 'left',
        accent_color: 'navy',
        section_order: [
            'summary',
            'experience',
            'skills',
            'education',
            'projects',
            'internship',
            'organization',
            'certifications',
            'languages',
        ],
        personal: {
            full_name: SAMPLE_NAME,
            headline: 'Fullstack Developer',
            email: 'mohamad.reza@example.com',
            phone: '+62 812 3456 7890',
            location: 'Jakarta, Indonesia',
            website: '',
            linkedin: 'linkedin.com/in/mohamadreza',
        },
        photo_url: PHOTO_TEMPLATES.includes(template) ? SAMPLE_PHOTO : null,
        summary:
            'Fullstack developer dengan empat tahun pengalaman membangun aplikasi web untuk e-commerce dan layanan B2B, dari perancangan basis data dan REST API hingga antarmuka Vue.js. Terbiasa menjaga kualitas lewat pengujian otomatis dan code review.',
        experiences: [
            {
                position: 'Fullstack Developer',
                organization: 'PT Niaga Digital Indonesia',
                location: 'Jakarta',
                start_date: '2022-01',
                end_date: '',
                is_current: true,
                highlights: [
                    'Membangun modul pemesanan B2B dengan Laravel dan Vue.js yang dipakai lebih dari 300 toko mitra setiap hari.',
                    'Menurunkan waktu respons API katalog dari 800 ms menjadi 250 ms dengan indeks MySQL dan cache Redis.',
                    'Menyusun pipeline CI/CD dengan GitHub Actions dan Docker sehingga rilis berubah dari mingguan menjadi harian.',
                ],
            },
            {
                position: 'Web Developer',
                organization: 'CV Solusi Kreatif',
                location: 'Bandung',
                start_date: '2020-08',
                end_date: '2021-12',
                is_current: false,
                highlights: [
                    'Mengembangkan 12 website dan toko online untuk klien UMKM dengan Laravel dan Tailwind CSS.',
                    'Mengintegrasikan pembayaran daring dan cek ongkos kirim agar klien menerima pesanan langsung dari website.',
                ],
            },
        ],
        activities: [],
        educations: [
            {
                institution: 'Universitas Padjadjaran',
                degree: 'S1',
                field_of_study: 'Teknik Informatika',
                location: 'Sumedang',
                start_date: '2016',
                end_date: '2020',
                is_current: false,
                gpa: '3,58/4,00',
                highlights: [],
            },
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Backend',
                skills: 'PHP, Laravel, REST API, MySQL, Redis',
            },
            {
                category: 'Frontend',
                skills: 'TypeScript, Vue.js, Inertia.js, Tailwind CSS',
            },
            {
                category: 'DevOps',
                skills: 'Git, Docker, GitHub Actions, Linux',
            },
        ],
        certifications: [
            {
                name: 'AWS Certified Cloud Practitioner',
                issuer: 'Amazon Web Services',
                issued_at: '2025-05',
                expires_at: '',
                credential_id: '',
                url: '',
            },
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Profesional' },
        ],
    };
}
