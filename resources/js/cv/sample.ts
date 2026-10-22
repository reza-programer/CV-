import type { ResumeData, TemplateSlug } from './types';

/**
 * Example content for the landing page preview and template thumbnails.
 * It is labelled as an example wherever it appears and is never copied into
 * a user's CV.
 */
export function sampleResume(template: TemplateSlug = 'classic'): ResumeData {
    return {
        title: 'Contoh CV',
        template,
        locale: 'id',
        section_order: [
            'summary',
            'experience',
            'education',
            'skills',
            'internship',
            'organization',
            'projects',
            'certifications',
            'languages',
        ],
        personal: {
            full_name: 'Rina Wulandari',
            headline: 'Analis Data',
            email: 'rina.wulandari@email.com',
            phone: '+62 812 3456 7890',
            location: 'Bandung, Jawa Barat',
            website: '',
            linkedin: 'linkedin.com/in/rinawulandari',
        },
        photo_url: null,
        summary:
            'Analis data dengan pengalaman empat tahun di sektor ritel dan logistik. Terbiasa membangun dasbor penjualan, membersihkan data operasional, dan menyajikan temuan untuk tim manajemen. Mencari peran analis data di perusahaan yang mengambil keputusan berbasis data.',
        experiences: [
            {
                position: 'Analis Data',
                organization: 'PT Sinar Retail Nusantara',
                location: 'Bandung',
                start_date: '2022-03',
                end_date: '',
                is_current: true,
                highlights: [
                    'Menyusun dasbor penjualan harian untuk 48 gerai yang dipakai manajer wilayah dalam rapat mingguan.',
                    'Mengotomatiskan rekap stok dari tiga sistem gudang sehingga laporan selesai dalam satu jam, bukan satu hari.',
                    'Mendampingi tim pemasaran membaca hasil uji promo dan menentukan periode diskon.',
                ],
            },
            {
                position: 'Staf Administrasi Data',
                organization: 'CV Logistik Priangan',
                location: 'Cimahi',
                start_date: '2020-01',
                end_date: '2022-02',
                is_current: false,
                highlights: [
                    'Merapikan data pengiriman dan pelanggan untuk keperluan penagihan bulanan.',
                    'Membuat panduan input data yang mengurangi kesalahan alamat pada dokumen pengiriman.',
                ],
            },
        ],
        activities: [],
        educations: [
            {
                institution: 'Universitas Padjadjaran',
                degree: 'S1',
                field_of_study: 'Statistika',
                location: 'Jatinangor',
                start_date: '2015',
                end_date: '2019',
                is_current: false,
                gpa: '3,61/4,00',
                highlights: [],
            },
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Analisis',
                skills: 'SQL, Python (pandas), Microsoft Excel, statistik deskriptif',
            },
            { category: 'Visualisasi', skills: 'Looker Studio, Power BI' },
        ],
        certifications: [
            {
                name: 'Google Data Analytics Professional Certificate',
                issuer: 'Google',
                issued_at: '2021-08',
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
