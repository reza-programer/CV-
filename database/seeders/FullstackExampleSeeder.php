<?php

namespace Database\Seeders;

use App\Actions\Resumes\CreateResume;
use App\Actions\Resumes\SaveResumeContent;
use App\Enums\ResumeLocale;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Adds a filled-in example CV for a fullstack developer to the local test
 * account, to show what each section should contain. It carries the owner's
 * name; the companies and figures are illustrative.
 *
 * php artisan db:seed --class=FullstackExampleSeeder
 */
class FullstackExampleSeeder extends Seeder
{
    public const TITLE = 'Contoh CV - Fullstack Developer';

    public function run(CreateResume $createResume, SaveResumeContent $saveContent): void
    {
        $user = User::query()->where('email', 'user@cvforge.test')->firstOrFail();
        $template = CvTemplate::query()->where('slug', 'professional')->firstOrFail();

        // Re-running refreshes the existing example in place, so its URL stays the same.
        $resume = $user->resumes()->where('title', self::TITLE)->first()
            ?? $createResume->handle($user, self::TITLE, $template, ResumeLocale::Indonesian);

        $saveContent->handle($resume, [
            'title' => self::TITLE,
            'template' => $template->slug,
            'locale' => ResumeLocale::Indonesian->value,
            'paragraph_align' => 'left',
            'accent_color' => 'navy',
            'section_order' => ['summary', 'experience', 'projects', 'skills', 'education', 'internship', 'organization', 'certifications', 'languages'],
            'personal' => [
                'full_name' => 'Mohamad Reza Nurpadilah',
                'headline' => 'Fullstack Developer · Laravel & Vue.js',
                'email' => 'mohamad.reza@example.com',
                'phone' => '+62 812 3456 7890',
                'location' => 'Jakarta Selatan',
                'linkedin' => 'linkedin.com/in/mohamadreza',
                'website' => 'github.com/mohamadreza',
            ],
            'summary' => 'Fullstack developer dengan empat tahun pengalaman membangun aplikasi web untuk e-commerce dan layanan B2B, dari perancangan basis data dan REST API hingga antarmuka Vue.js. '
                .'Terbiasa bekerja dengan Laravel, MySQL, Redis, dan Docker, serta menjaga kualitas lewat pengujian otomatis dan code review. '
                .'Mencari peran fullstack di tim produk yang merilis fitur secara rutin.',
            'experiences' => [
                [
                    'position' => 'Fullstack Developer',
                    'organization' => 'PT Niaga Digital Indonesia',
                    'location' => 'Jakarta',
                    'start_date' => '2022-01',
                    'end_date' => null,
                    'is_current' => true,
                    'highlights' => [
                        'Membangun modul pemesanan B2B dengan Laravel, Inertia, dan Vue.js yang dipakai lebih dari 300 toko mitra setiap hari.',
                        'Menurunkan waktu respons API katalog dari sekitar 800 ms menjadi 250 ms dengan indeks MySQL, eager loading, dan cache Redis.',
                        'Merancang REST API berdokumentasi OpenAPI untuk aplikasi mobile Android dan iOS.',
                        'Menyusun pipeline CI/CD dengan GitHub Actions dan Docker sehingga rilis berubah dari mingguan menjadi harian.',
                        'Menaikkan cakupan pengujian modul transaksi menjadi 70% dengan PHPUnit dan Vitest; mendampingi dua developer junior lewat code review.',
                    ],
                ],
                [
                    'position' => 'Web Developer',
                    'organization' => 'CV Solusi Kreatif',
                    'location' => 'Bandung',
                    'start_date' => '2020-08',
                    'end_date' => '2021-12',
                    'is_current' => false,
                    'highlights' => [
                        'Mengembangkan 12 website company profile dan toko online untuk klien UMKM dengan Laravel dan Tailwind CSS.',
                        'Mengintegrasikan pembayaran Midtrans dan cek ongkos kirim sehingga klien dapat menerima pesanan langsung dari website.',
                        'Memindahkan aplikasi klien dari shared hosting ke VPS Ubuntu dengan Nginx, SSL, dan backup harian otomatis.',
                    ],
                ],
            ],
            'activities' => [
                [
                    'type' => 'internship',
                    'position' => 'Software Engineer Intern',
                    'organization' => 'PT Logistik Cepat Nusantara',
                    'location' => 'Bandung',
                    'start_date' => '2019-07',
                    'end_date' => '2019-09',
                    'is_current' => false,
                    'highlights' => [
                        'Membuat dasbor pelacakan pengiriman internal dengan Vue.js dan Chart.js.',
                        'Menulis skrip impor data resi dari Excel ke MySQL yang menggantikan input manual.',
                    ],
                ],
                [
                    'type' => 'organization',
                    'position' => 'Kepala Divisi Teknologi',
                    'organization' => 'Himpunan Mahasiswa Informatika',
                    'location' => '',
                    'start_date' => '2018',
                    'end_date' => '2019',
                    'is_current' => false,
                    'highlights' => [
                        'Memimpin enam anggota membangun sistem pendaftaran acara yang dipakai lebih dari 800 peserta.',
                    ],
                ],
            ],
            'educations' => [
                [
                    'institution' => 'Universitas Padjadjaran',
                    'degree' => 'S1',
                    'field_of_study' => 'Teknik Informatika',
                    'location' => 'Sumedang',
                    'start_date' => '2016',
                    'end_date' => '2020',
                    'is_current' => false,
                    'gpa' => '3,58/4,00',
                    'highlights' => [
                        'Skripsi: Sistem rekomendasi produk berbasis collaborative filtering untuk toko daring.',
                    ],
                ],
            ],
            'projects' => [
                [
                    'name' => 'Antrean Klinik',
                    'role' => 'Proyek open source',
                    'url' => 'github.com/mohamadreza/antrean-klinik',
                    'start_date' => '2023-03',
                    'end_date' => '2023-06',
                    'is_current' => false,
                    'highlights' => [
                        'Aplikasi antrean pasien dengan Laravel, Vue.js, dan WebSocket (Laravel Reverb) untuk layar panggilan waktu nyata.',
                        'Dipakai dua klinik pratama; mendapat 120 bintang di GitHub.',
                    ],
                ],
                [
                    'name' => 'Kasir PWA untuk UMKM',
                    'role' => 'Proyek pribadi',
                    'url' => 'github.com/mohamadreza/kasir-pwa',
                    'start_date' => '2021-05',
                    'end_date' => '2021-08',
                    'is_current' => false,
                    'highlights' => [
                        'Aplikasi kasir yang tetap berjalan tanpa internet dan menyinkronkan transaksi saat kembali online.',
                    ],
                ],
            ],
            'skill_groups' => [
                ['category' => 'Bahasa pemrograman', 'skills' => 'PHP, JavaScript, TypeScript, SQL'],
                ['category' => 'Backend', 'skills' => 'Laravel, REST API, Queue, Redis, WebSocket'],
                ['category' => 'Frontend', 'skills' => 'Vue.js, Inertia.js, Tailwind CSS, HTML, CSS'],
                ['category' => 'Basis data', 'skills' => 'MySQL, PostgreSQL'],
                ['category' => 'DevOps & tools', 'skills' => 'Git, Docker, GitHub Actions, Linux, Nginx'],
                ['category' => 'Pengujian', 'skills' => 'PHPUnit, Pest, Vitest'],
            ],
            'certifications' => [
                [
                    'name' => 'AWS Certified Cloud Practitioner',
                    'issuer' => 'Amazon Web Services',
                    'issued_at' => '2025-05',
                    'expires_at' => '2028-05',
                    'credential_id' => '',
                    'url' => '',
                ],
                [
                    'name' => 'Junior Web Developer',
                    'issuer' => 'BNSP',
                    'issued_at' => '2021-02',
                    'expires_at' => '',
                    'credential_id' => '',
                    'url' => '',
                ],
            ],
            'languages' => [
                ['name' => 'Indonesia', 'proficiency' => 'Penutur asli'],
                ['name' => 'Inggris', 'proficiency' => 'Profesional, TOEIC 850'],
            ],
        ]);
    }
}
