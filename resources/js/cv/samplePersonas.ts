import type {
    ActivityItem,
    CertificationItem,
    EducationItem,
    ExperienceItem,
    ResumeData,
    SectionKey,
} from './types';

/**
 * Example people from different fields, so the template gallery shows what
 * a CV looks like for an accountant, a nurse or a teacher as well as a
 * developer. Names, employers and figures are illustrative; emails use the
 * reserved example.com domain.
 */
export type Persona = Pick<
    ResumeData,
    | 'locale'
    | 'section_order'
    | 'personal'
    | 'summary'
    | 'experiences'
    | 'activities'
    | 'educations'
    | 'projects'
    | 'skill_groups'
    | 'certifications'
    | 'languages'
>;

export type PersonaKey =
    | 'developer'
    | 'accountant'
    | 'nurse'
    | 'teacher'
    | 'marketing'
    | 'civil'
    | 'designer'
    | 'hr'
    | 'legal'
    | 'pharmacist'
    | 'hospitality'
    | 'graduate';

const EXPERIENCED: SectionKey[] = [
    'summary',
    'experience',
    'skills',
    'education',
    'projects',
    'internship',
    'organization',
    'certifications',
    'languages',
];

/** Fresh graduates lead with education and campus experience. */
const GRADUATE: SectionKey[] = [
    'summary',
    'education',
    'internship',
    'organization',
    'skills',
    'certifications',
    'languages',
    'experience',
    'projects',
];

function job(
    position: string,
    organization: string,
    location: string,
    start: string,
    end: string,
    highlights: string[],
): ExperienceItem {
    return {
        position,
        organization,
        location,
        start_date: start,
        end_date: end,
        is_current: end === '',
        highlights,
    };
}

function school(
    institution: string,
    degree: string,
    field: string,
    location: string,
    start: string,
    end: string,
    gpa = '',
    highlights: string[] = [],
): EducationItem {
    return {
        institution,
        degree,
        field_of_study: field,
        location,
        start_date: start,
        end_date: end,
        is_current: false,
        gpa,
        highlights,
    };
}

function cert(name: string, issuer: string, issued: string): CertificationItem {
    return {
        name,
        issuer,
        issued_at: issued,
        expires_at: '',
        credential_id: '',
        url: '',
    };
}

function activity(
    type: ActivityItem['type'],
    item: ExperienceItem,
): ActivityItem {
    return { type, ...item };
}

function person(
    full_name: string,
    headline: string,
    email: string,
    phone: string,
    location: string,
    linkedin = '',
) {
    return {
        full_name,
        headline,
        email,
        phone,
        location,
        website: '',
        linkedin,
    };
}

export const PERSONAS: Record<PersonaKey, Persona> = {
    developer: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Mohamad Reza Nurpadilah',
            'Fullstack Developer',
            'mohamad.reza@example.com',
            '+62 812 3456 7890',
            'Jakarta, Indonesia',
            'linkedin.com/in/mohamadreza',
        ),
        summary:
            'Fullstack developer dengan empat tahun pengalaman membangun aplikasi web untuk e-commerce dan layanan B2B, dari perancangan basis data dan REST API hingga antarmuka Vue.js. Terbiasa menjaga kualitas lewat pengujian otomatis dan code review.',
        experiences: [
            job(
                'Fullstack Developer',
                'PT Niaga Digital Indonesia',
                'Jakarta',
                '2022-01',
                '',
                [
                    'Membangun modul pemesanan B2B dengan Laravel dan Vue.js yang dipakai lebih dari 300 toko mitra setiap hari.',
                    'Menurunkan waktu respons API katalog dari 800 ms menjadi 250 ms dengan indeks MySQL dan cache Redis.',
                    'Menyusun pipeline CI/CD dengan GitHub Actions dan Docker sehingga rilis berubah dari mingguan menjadi harian.',
                ],
            ),
            job(
                'Web Developer',
                'CV Solusi Kreatif',
                'Bandung',
                '2020-08',
                '2021-12',
                [
                    'Mengembangkan 12 website dan toko online untuk klien UMKM dengan Laravel dan Tailwind CSS.',
                    'Mengintegrasikan pembayaran daring dan cek ongkos kirim agar klien menerima pesanan langsung dari website.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Padjadjaran',
                'S1',
                'Teknik Informatika',
                'Sumedang',
                '2016',
                '2020',
                '3,58/4,00',
            ),
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
            cert(
                'AWS Certified Cloud Practitioner',
                'Amazon Web Services',
                '2025-05',
            ),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Profesional' },
        ],
    },

    accountant: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Dewi Anggraini, S.Ak.',
            'Staf Akuntansi',
            'dewi.anggraini@example.com',
            '+62 813 1234 5678',
            'Surabaya, Jawa Timur',
        ),
        summary:
            'Staf akuntansi dengan tiga tahun pengalaman di perusahaan distribusi, terbiasa menyusun laporan keuangan bulanan, rekonsiliasi bank, dan pelaporan pajak. Teliti, tepat waktu, dan nyaman bekerja dengan sistem ERP.',
        experiences: [
            job(
                'Staf Akuntansi',
                'PT Sumber Makmur Distribusi',
                'Surabaya',
                '2022-02',
                '',
                [
                    'Menyusun laporan laba rugi dan neraca bulanan untuk lima cabang sebelum tanggal 7 setiap bulan.',
                    'Melakukan rekonsiliasi bank harian dan menelusuri selisih hingga di bawah 0,1% dari total transaksi.',
                    'Menyiapkan SPT Masa PPN dan PPh 21 beserta dokumen pendukung pemeriksaan pajak.',
                ],
            ),
            job(
                'Staf Administrasi Keuangan',
                'CV Sinar Jaya Abadi',
                'Sidoarjo',
                '2020-09',
                '2022-01',
                [
                    'Mencatat faktur pembelian dan penjualan di Accurate serta menagih piutang yang jatuh tempo.',
                    'Menyusun laporan kas kecil mingguan untuk pemilik usaha.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Airlangga',
                'S1',
                'Akuntansi',
                'Surabaya',
                '2016',
                '2020',
                '3,52/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Akuntansi',
                skills: 'Laporan keuangan, rekonsiliasi bank, PSAK, perpajakan (PPN, PPh 21)',
            },
            {
                category: 'Perangkat',
                skills: 'Microsoft Excel (pivot, VLOOKUP), Accurate, SAP Business One',
            },
        ],
        certifications: [
            cert('Brevet Pajak A dan B', 'Ikatan Akuntan Indonesia', '2021-06'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },

    nurse: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Siti Rahmawati, S.Kep., Ners',
            'Perawat Pelaksana',
            'siti.rahmawati@example.com',
            '+62 811 2345 6789',
            'Yogyakarta',
        ),
        summary:
            'Perawat dengan empat tahun pengalaman di ruang rawat inap penyakit dalam dan IGD. Terampil dalam asesmen pasien, pemberian obat, dan edukasi keluarga pasien, dengan STR aktif dan sertifikat BTCLS.',
        experiences: [
            job(
                'Perawat Pelaksana',
                'RS Medika Sejahtera',
                'Yogyakarta',
                '2021-03',
                '',
                [
                    'Merawat 8–10 pasien per shift di ruang penyakit dalam, termasuk pemantauan tanda vital dan balans cairan.',
                    'Mendokumentasikan asuhan keperawatan di rekam medis elektronik sesuai standar akreditasi.',
                    'Mengedukasi pasien diabetes dan keluarganya tentang perawatan luka dan pengaturan insulin.',
                ],
            ),
            job(
                'Perawat IGD',
                'Klinik Utama Sehat Bersama',
                'Sleman',
                '2019-08',
                '2021-02',
                [
                    'Melakukan triase dan tindakan awal kegawatdaruratan bersama dokter jaga.',
                    'Mengelola stok obat dan alat habis pakai IGD setiap minggu.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Gadjah Mada',
                'Profesi Ners',
                'Ilmu Keperawatan',
                'Yogyakarta',
                '2018',
                '2019',
            ),
            school(
                'Universitas Gadjah Mada',
                'S1',
                'Ilmu Keperawatan',
                'Yogyakarta',
                '2014',
                '2018',
                '3,61/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Klinis',
                skills: 'Asesmen pasien, perawatan luka, pemasangan infus, pemberian obat, triase',
            },
            {
                category: 'Administrasi',
                skills: 'Rekam medis elektronik, SIMRS',
            },
        ],
        certifications: [
            cert(
                'Surat Tanda Registrasi (STR) Perawat',
                'Konsil Keperawatan Indonesia',
                '2019-10',
            ),
            cert('BTCLS', 'Pusat Pelatihan Gawat Darurat', '2022-04'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Dasar' },
        ],
    },

    teacher: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Ahmad Fauzi, S.Pd.',
            'Guru Bahasa Inggris',
            'ahmad.fauzi@example.com',
            '+62 812 9876 5432',
            'Bandung, Jawa Barat',
        ),
        summary:
            'Guru Bahasa Inggris SMA dengan lima tahun pengalaman mengajar kelas X–XII dan membimbing siswa lomba debat. Terbiasa merancang pembelajaran berbasis proyek dan asesmen sesuai Kurikulum Merdeka.',
        experiences: [
            job(
                'Guru Bahasa Inggris',
                'SMA Harapan Bangsa',
                'Bandung',
                '2020-07',
                '',
                [
                    'Mengajar enam rombongan belajar per semester; rata-rata nilai asesmen akhir naik dari 74 menjadi 81.',
                    'Membina klub debat bahasa Inggris yang meraih juara 2 tingkat provinsi pada 2023.',
                    'Menyusun modul ajar dan asesmen formatif sesuai Kurikulum Merdeka.',
                ],
            ),
            job(
                'Pengajar',
                'Lembaga Kursus Bahasa Cakrawala',
                'Bandung',
                '2018-09',
                '2020-06',
                [
                    'Mengajar kelas persiapan TOEFL dan percakapan untuk siswa SMP hingga karyawan.',
                    'Membuat bank soal latihan TOEFL ITP yang dipakai di seluruh cabang.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Pendidikan Indonesia',
                'S1',
                'Pendidikan Bahasa Inggris',
                'Bandung',
                '2014',
                '2018',
                '3,64/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Pengajaran',
                skills: 'Kurikulum Merdeka, pembelajaran berbasis proyek, asesmen formatif, manajemen kelas',
            },
            { category: 'Digital', skills: 'Google Classroom, Canva, Quizizz' },
        ],
        certifications: [
            cert('Sertifikat Pendidik (PPG)', 'Kemendikbudristek', '2021-12'),
            cert('TOEFL ITP, skor 610', 'ETS', '2022-03'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Fasih' },
            { name: 'Sunda', proficiency: 'Penutur asli' },
        ],
    },

    marketing: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Nadia Paramitha',
            'Digital Marketing Specialist',
            'nadia.paramitha@example.com',
            '+62 815 1122 3344',
            'Jakarta Selatan',
        ),
        summary:
            'Spesialis pemasaran digital dengan empat tahun pengalaman mengelola iklan berbayar, media sosial, dan email marketing untuk brand kecantikan dan F&B. Terbiasa mengambil keputusan dari data kampanye.',
        experiences: [
            job(
                'Digital Marketing Specialist',
                'PT Cantika Kosmetik Indonesia',
                'Jakarta',
                '2022-04',
                '',
                [
                    'Mengelola anggaran iklan Meta dan Google sebesar Rp150 juta per bulan dengan ROAS rata-rata 4,2.',
                    'Menambah pengikut Instagram dari 40 ribu menjadi 120 ribu dalam 18 bulan lewat konten video pendek.',
                    'Menyusun laporan kinerja kampanye mingguan di Looker Studio untuk tim penjualan.',
                ],
            ),
            job(
                'Social Media Officer',
                'Kopi Nusantara Group',
                'Jakarta',
                '2020-06',
                '2022-03',
                [
                    'Merencanakan kalender konten bulanan untuk 12 gerai dan dua akun media sosial.',
                    'Mengelola program loyalitas lewat WhatsApp Business yang diikuti 8.000 pelanggan.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Indonesia',
                'S1',
                'Ilmu Komunikasi',
                'Depok',
                '2016',
                '2020',
                '3,47/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Pemasaran',
                skills: 'Meta Ads, Google Ads, SEO dasar, email marketing, copywriting',
            },
            {
                category: 'Analitik',
                skills: 'Google Analytics 4, Looker Studio, Microsoft Excel',
            },
        ],
        certifications: [
            cert('Google Ads Search Certification', 'Google', '2023-05'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Profesional' },
        ],
    },

    civil: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Bagas Wicaksono, S.T.',
            'Site Engineer',
            'bagas.wicaksono@example.com',
            '+62 819 8765 4321',
            'Semarang, Jawa Tengah',
        ),
        summary:
            'Site engineer dengan lima tahun pengalaman pada proyek gedung bertingkat dan perumahan. Terbiasa mengawasi pekerjaan struktur, mengendalikan jadwal dan mutu, serta berkoordinasi dengan konsultan dan subkontraktor.',
        experiences: [
            job(
                'Site Engineer',
                'PT Karya Bangun Persada',
                'Semarang',
                '2021-01',
                '',
                [
                    'Mengawasi pekerjaan struktur gedung perkantoran 8 lantai senilai Rp85 miliar hingga serah terima tepat waktu.',
                    'Menyusun laporan progres mingguan dan kurva S untuk pemilik proyek dan konsultan pengawas.',
                    'Menerapkan inspeksi K3 harian sehingga proyek mencapai 500.000 jam kerja tanpa kecelakaan.',
                ],
            ),
            job(
                'Drafter dan Quantity Surveyor',
                'CV Cipta Rekayasa',
                'Salatiga',
                '2019-03',
                '2020-12',
                [
                    'Membuat gambar kerja AutoCAD dan menghitung volume pekerjaan untuk 15 proyek perumahan.',
                    'Menyusun rencana anggaran biaya (RAB) berdasarkan analisa harga satuan daerah.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Diponegoro',
                'S1',
                'Teknik Sipil',
                'Semarang',
                '2014',
                '2019',
                '3,41/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Teknik',
                skills: 'Pengawasan struktur, manajemen proyek, RAB, quantity take-off, K3 konstruksi',
            },
            {
                category: 'Perangkat lunak',
                skills: 'AutoCAD, SAP2000, Microsoft Project',
            },
        ],
        certifications: [
            cert('SKK Ahli Muda Teknik Bangunan Gedung', 'LPJK', '2022-08'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },

    designer: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Laras Sekar Ayu',
            'Graphic Designer',
            'laras.sekar@example.com',
            '+62 856 1234 9876',
            'Malang, Jawa Timur',
        ),
        summary:
            'Desainer grafis dengan empat tahun pengalaman membuat identitas visual, materi kampanye, dan desain kemasan. Terbiasa bekerja dari brief hingga file siap cetak dan menjaga konsistensi brand guideline.',
        experiences: [
            job(
                'Graphic Designer',
                'Studio Rupa Kreatif',
                'Malang',
                '2021-05',
                '',
                [
                    'Merancang identitas visual untuk 20 klien UMKM, dari logo hingga brand guideline.',
                    'Membuat desain kemasan keripik yang membantu klien masuk ke jaringan minimarket regional.',
                    'Menyiapkan file cetak offset dan berkoordinasi dengan percetakan untuk proof warna.',
                ],
            ),
            job(
                'Desainer Junior',
                'PT Media Kreasi Nusantara',
                'Surabaya',
                '2019-08',
                '2021-04',
                [
                    'Mendesain materi media sosial dan banner promosi untuk enam brand ritel.',
                    'Membuat template presentasi perusahaan yang dipakai seluruh tim penjualan.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Institut Teknologi Sepuluh Nopember',
                'S1',
                'Desain Komunikasi Visual',
                'Surabaya',
                '2015',
                '2019',
                '3,55/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Desain',
                skills: 'Identitas visual, tipografi, tata letak, desain kemasan, ilustrasi vektor',
            },
            {
                category: 'Perangkat lunak',
                skills: 'Adobe Illustrator, Photoshop, InDesign, Figma',
            },
        ],
        certifications: [
            cert(
                'Sertifikat Kompetensi Desain Grafis Madya',
                'BNSP',
                '2022-02',
            ),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },

    hr: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Rina Kartika Sari, S.Psi.',
            'HR Generalist',
            'rina.kartika@example.com',
            '+62 817 2233 4455',
            'Tangerang, Banten',
        ),
        summary:
            'HR generalist dengan empat tahun pengalaman di perusahaan manufaktur, menangani rekrutmen, administrasi karyawan, penggajian, dan hubungan industrial. Terbiasa bekerja dengan data karyawan dan regulasi ketenagakerjaan.',
        experiences: [
            job(
                'HR Generalist',
                'PT Prima Plastindo',
                'Tangerang',
                '2021-08',
                '',
                [
                    'Merekrut 120 karyawan produksi dan staf per tahun dengan waktu pengisian rata-rata 21 hari.',
                    'Mengelola data 650 karyawan, kepesertaan BPJS, dan proses penggajian bulanan.',
                    'Menyusun program orientasi yang menurunkan turnover tiga bulan pertama dari 18% menjadi 9%.',
                ],
            ),
            job(
                'Staf Rekrutmen',
                'PT Solusi Tenaga Kerja Mandiri',
                'Jakarta',
                '2019-10',
                '2021-07',
                [
                    'Melakukan screening CV, psikotes, dan wawancara awal untuk klien perbankan dan ritel.',
                    'Mengelola database kandidat dan laporan rekrutmen mingguan.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Brawijaya',
                'S1',
                'Psikologi',
                'Malang',
                '2015',
                '2019',
                '3,58/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'SDM',
                skills: 'Rekrutmen, psikotes, penggajian, BPJS, hubungan industrial',
            },
            {
                category: 'Perangkat',
                skills: 'Microsoft Excel, HRIS, Google Workspace',
            },
        ],
        certifications: [
            cert('Sertifikasi Kompetensi Manajemen SDM', 'BNSP', '2022-11'),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },

    legal: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Yoga Aditya Pratama, S.H.',
            'Legal Officer',
            'yoga.pratama@example.com',
            '+62 812 5566 7788',
            'Jakarta Pusat',
        ),
        summary:
            'Legal officer dengan empat tahun pengalaman menyusun dan meninjau kontrak komersial, mengurus perizinan usaha, serta mendampingi penyelesaian sengketa. Cermat dalam analisis risiko hukum dan komunikasi dengan regulator.',
        experiences: [
            job(
                'Legal Officer',
                'PT Arta Finansial Indonesia',
                'Jakarta',
                '2021-02',
                '',
                [
                    'Meninjau lebih dari 300 kontrak kerja sama dan perjanjian kerahasiaan setiap tahun.',
                    'Mengurus perizinan dan pelaporan berkala ke regulator tanpa keterlambatan.',
                    'Menyusun pedoman penandatanganan kontrak yang memangkas waktu persetujuan dari 10 menjadi 4 hari kerja.',
                ],
            ),
            job(
                'Associate',
                'Kantor Hukum Pratama & Rekan',
                'Jakarta',
                '2019-03',
                '2021-01',
                [
                    'Melakukan uji tuntas hukum untuk tiga transaksi akuisisi perusahaan.',
                    'Menyusun draf gugatan dan jawaban dalam perkara perdata di pengadilan negeri.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Trisakti',
                'S1',
                'Ilmu Hukum',
                'Jakarta',
                '2014',
                '2018',
                '3,62/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Hukum',
                skills: 'Penyusunan kontrak, uji tuntas hukum, perizinan usaha (OSS), hukum perusahaan, litigasi perdata',
            },
            {
                category: 'Lainnya',
                skills: 'Riset hukum, negosiasi, Microsoft Word',
            },
        ],
        certifications: [
            cert(
                'Pendidikan Khusus Profesi Advokat (PKPA)',
                'PERADI',
                '2019-11',
            ),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Profesional' },
        ],
    },

    pharmacist: {
        locale: 'id',
        section_order: EXPERIENCED,
        personal: person(
            'Intan Permatasari, S.Farm., Apt.',
            'Apoteker Penanggung Jawab',
            'intan.permatasari@example.com',
            '+62 821 3344 5566',
            'Medan, Sumatera Utara',
        ),
        summary:
            'Apoteker dengan empat tahun pengalaman di apotek dan instalasi farmasi rumah sakit. Terbiasa memberikan konseling obat, mengelola persediaan, dan memastikan pelayanan sesuai standar kefarmasian.',
        experiences: [
            job(
                'Apoteker Penanggung Jawab',
                'Apotek Sehat Sentosa',
                'Medan',
                '2022-01',
                '',
                [
                    'Mengawasi pelayanan rata-rata 180 resep per hari bersama empat tenaga teknis kefarmasian.',
                    'Menurunkan obat kedaluwarsa dari 2,1% menjadi 0,6% nilai persediaan lewat sistem FEFO dan stock opname bulanan.',
                    'Memberikan konseling obat untuk pasien penyakit kronis dan program rujuk balik.',
                ],
            ),
            job(
                'Apoteker Pendamping',
                'RS Bunda Medika',
                'Medan',
                '2020-03',
                '2021-12',
                [
                    'Melakukan telaah resep dan rekonsiliasi obat pasien rawat inap.',
                    'Menyusun laporan narkotika dan psikotropika bulanan untuk dinas kesehatan.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Universitas Sumatera Utara',
                'Profesi Apoteker',
                'Farmasi',
                'Medan',
                '2019',
                '2020',
            ),
            school(
                'Universitas Sumatera Utara',
                'S1',
                'Farmasi',
                'Medan',
                '2015',
                '2019',
                '3,57/4,00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Kefarmasian',
                skills: 'Pelayanan resep, konseling obat, manajemen persediaan, farmakovigilans',
            },
            {
                category: 'Sistem',
                skills: 'SIMRS, aplikasi apotek, Microsoft Excel',
            },
        ],
        certifications: [
            cert(
                'Surat Tanda Registrasi Apoteker (STRA)',
                'Konsil Kefarmasian',
                '2020-05',
            ),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },

    hospitality: {
        locale: 'en',
        section_order: EXPERIENCED,
        personal: person(
            'Putu Arya Wirawan',
            'Front Office Supervisor',
            'putu.arya@example.com',
            '+62 878 6543 2109',
            'Badung, Bali',
        ),
        summary:
            'Front office supervisor with six years of experience in four- and five-star resorts in Bali. Leads check-in, reservations and complaint handling, and coaches front desk agents to keep guest satisfaction high.',
        experiences: [
            job(
                'Front Office Supervisor',
                'Samudra Bay Resort & Spa',
                'Nusa Dua',
                '2021-04',
                '',
                [
                    'Supervise 12 front desk agents across three shifts for a 220-room resort.',
                    'Raised the check-in satisfaction score from 86% to 93% by introducing pre-arrival messages.',
                    'Handle VIP arrivals, upgrades and guest complaints with a response time under 15 minutes.',
                ],
            ),
            job(
                'Front Desk Agent',
                'Kuta Garden Hotel',
                'Kuta',
                '2018-06',
                '2021-03',
                [
                    'Processed check-ins, check-outs and payments in Opera PMS for up to 150 guests a day.',
                    'Upsold room upgrades and airport transfers, contributing 8% of monthly front office revenue.',
                ],
            ),
        ],
        activities: [],
        educations: [
            school(
                'Politeknik Pariwisata Bali',
                'D4',
                'Hospitality Management',
                'Badung',
                '2014',
                '2018',
                '3.48/4.00',
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Hospitality',
                skills: 'Front office operations, reservations, guest relations, complaint handling',
            },
            {
                category: 'Systems',
                skills: 'Opera PMS, channel manager, Microsoft Excel',
            },
        ],
        certifications: [
            cert(
                'Front Office Supervisor Competency Certificate',
                'BNSP',
                '2022-09',
            ),
        ],
        languages: [
            { name: 'Indonesian', proficiency: 'Native' },
            { name: 'English', proficiency: 'Fluent' },
            { name: 'Japanese', proficiency: 'Basic' },
        ],
    },

    graduate: {
        locale: 'id',
        section_order: GRADUATE,
        personal: person(
            'Fikri Ramadhan, S.M.',
            'Lulusan Manajemen',
            'fikri.ramadhan@example.com',
            '+62 853 9988 7766',
            'Makassar, Sulawesi Selatan',
        ),
        summary:
            'Lulusan S1 Manajemen dengan minat di bidang operasional dan analisis bisnis. Berpengalaman magang di bagian supply chain dan aktif memimpin kegiatan organisasi kampus. Mencari posisi management trainee.',
        experiences: [],
        activities: [
            activity(
                'internship',
                job(
                    'Magang Supply Chain',
                    'PT Logistik Prima Nusantara',
                    'Makassar',
                    '2023-06',
                    '2023-08',
                    [
                        'Membantu pemantauan stok gudang dan menyusun laporan pergerakan barang harian.',
                        'Membuat dasbor Excel untuk memantau keterlambatan pengiriman ke 30 toko.',
                    ],
                ),
            ),
            activity(
                'organization',
                job(
                    'Ketua Divisi Acara',
                    'Himpunan Mahasiswa Manajemen',
                    'Makassar',
                    '2021',
                    '2022',
                    [
                        'Memimpin 15 anggota menyelenggarakan seminar karier dengan 600 peserta.',
                    ],
                ),
            ),
        ],
        educations: [
            school(
                'Universitas Hasanuddin',
                'S1',
                'Manajemen',
                'Makassar',
                '2020',
                '2024',
                '3,71/4,00',
                [
                    'Skripsi: Pengaruh ketepatan pengiriman terhadap kepuasan pelanggan ritel modern.',
                ],
            ),
        ],
        projects: [],
        skill_groups: [
            {
                category: 'Bisnis',
                skills: 'Analisis data penjualan, perencanaan persediaan, presentasi bisnis',
            },
            {
                category: 'Perangkat',
                skills: 'Microsoft Excel, PowerPoint, Google Sheets',
            },
        ],
        certifications: [
            cert(
                'Microsoft Office Specialist: Excel Associate',
                'Microsoft',
                '2023-02',
            ),
        ],
        languages: [
            { name: 'Indonesia', proficiency: 'Penutur asli' },
            { name: 'Inggris', proficiency: 'Menengah' },
        ],
    },
};
