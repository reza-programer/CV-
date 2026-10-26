<p align="center"><img src="docs/brand/kezot-logo.png" alt="kezot — Buat CV Profesional, Raih Peluangmu" width="460"></p>

# CV by kezottt_ — Pembuat CV ATS-Friendly

**CV by kezottt_** adalah aplikasi web untuk menyusun CV profesional dalam format **A4** dan mengunduhnya sebagai **PDF berteks asli** (teks dapat diseleksi dan dicari, sehingga terbaca oleh sistem pelacak lamaran/ATS). Data CV diisi sekali, lalu tampilannya bisa diganti ke salah satu dari **100 template** kapan saja tanpa mengetik ulang.

> Yang Anda lihat di pratinjau adalah yang tercetak: pratinjau di layar dan PDF dirender oleh komponen tata letak yang sama.

![Halaman utama CV by kezottt_](docs/screenshots/landing.png)

---

## Daftar isi

1. [Fitur](#fitur)
2. [Tangkapan layar](#tangkapan-layar)
3. [Teknologi](#teknologi)
4. [Alur pengguna](#alur-pengguna)
5. [Arsitektur dan alur data](#arsitektur-dan-alur-data)
6. [Panel admin dan pemantauan](#panel-admin-dan-pemantauan)
7. [Template](#template)
8. [Instalasi](#instalasi)
9. [Konfigurasi `.env`](#konfigurasi-env)
10. [Akun demo dan data contoh](#akun-demo-dan-data-contoh)
11. [Perintah sehari-hari](#perintah-sehari-hari)
12. [Struktur folder](#struktur-folder)
13. [Panduan pengembang](#panduan-pengembang)
14. [Keamanan dan privasi](#keamanan-dan-privasi)
15. [Pemecahan masalah](#pemecahan-masalah)
16. [Pembuat](#pembuat)

---

## Fitur

**Untuk pengguna**

- **Coba dulu tanpa daftar** (`/coba`): pengunjung langsung masuk editor berisi contoh CV, bisa mengubah isi, template, warna, dan bahasa dengan pratinjau langsung. Saat ingin menyimpan, mengunggah foto, atau mengunduh PDF, mereka diminta daftar atau masuk, dan isi yang sudah diketik otomatis menjadi CV pertama di akun.
- **Editor dua panel**: formulir di kiri, pratinjau A4 langsung di kanan. Di ponsel keduanya dipindah lewat tab **Isi data / Pratinjau** di bawah layar, dan posisi formulir diingat.
- **Foto profil akun**: diunggah di halaman Profil, dipotong persegi, disimpan privat, dan tampil di menu akun.
- **Bagian CV lengkap**: data diri dan kontak, foto (opsional), profil singkat, pengalaman kerja, magang, organisasi, pendidikan, proyek, keahlian, sertifikasi, dan bahasa. Setiap entri dapat ditambah, disunting, dihapus, dan diurutkan ulang; urutan bagian juga dapat diatur.
- **Bagian kosong tidak tercetak**, jadi tidak ada judul bagian yang menggantung.
- **100 template**: 74 satu kolom dan 26 dua kolom berlabel _Kreatif_. Ada yang hitam-putih murni untuk ATS, ada yang bergaya desainer dengan foto, ornamen sudut, gelombang, dan judul berbentuk pil. Sebagian berwarna dengan 6 pilihan warna aksen.
- **Template Rekomendasi**: 8 template satu kolom yang paling aman untuk melamar diberi label _Rekomendasi_, tampil paling depan, dan punya saringan sendiri. Admin dapat mengubah pilihannya.
- **Contoh CV dari berbagai bidang** di galeri template: akuntansi, keperawatan, pendidikan, pemasaran, teknik sipil, desain, SDM, hukum, farmasi, perhotelan (bahasa Inggris), lulusan baru, dan pengembang perangkat lunak.
- **Ganti template, warna aksen, perataan paragraf (rata kiri/rata kanan-kiri), dan bahasa dokumen** tanpa kehilangan isi.
- **Dua bahasa dokumen** (Bahasa Indonesia dan English): judul bagian dan format tanggal ikut berganti.
- **Pergantian halaman yang rapi**: judul bagian tidak tertinggal sendirian di bawah halaman, satu entri tidak terpotong bila muat di halaman berikutnya.
- **Unduh PDF A4** berteks asli, hasilnya sama dengan pratinjau.
- **Tersimpan otomatis**, plus dasbor untuk membuka, menduplikat, mengunduh, dan menghapus CV.
- **Asisten AI (opsional)**: menyempurnakan profil singkat dan poin pengalaman, mencocokkan kata kunci lowongan, dan menerjemahkan isi CV ke bahasa lain. Asisten tidak mengarang pengalaman, dan setiap hasilnya ditampilkan untuk diperiksa dan disunting sebelum diterapkan.

**Untuk admin** (Filament, di `/admin`)

- **Ringkasan langsung**: jumlah pengguna, yang sedang online, CV dibuat, dan PDF diunduh, plus grafik aktivitas 14 hari. Diperbarui otomatis.
- **Aktivitas terbaru**: siapa melakukan apa dan kapan (mendaftar, masuk, membuat/menyunting/menghapus CV, mengunduh PDF, memakai asisten AI, dan lainnya).
- **Sedang online**: pengguna yang aktif dalam 15 menit terakhir.
- **Notifikasi** di ikon lonceng setiap ada pengguna baru mendaftar.
- **Riwayat per pengguna** di halaman detail pengguna.
- **Ekspor CSV** untuk daftar pengguna dan log aktivitas (mengikuti filter yang aktif).
- Mengelola pengguna (termasuk memberi akses admin) dan template, termasuk menandai template _Rekomendasi_.
- Admin yang masuk lewat `/login` langsung diarahkan ke panel admin.

---

## Tangkapan layar

| Pratinjau template                                           | Dasbor CV                                 |
| ------------------------------------------------------------ | ----------------------------------------- |
| ![Pratinjau template](docs/screenshots/template-preview.png) | ![Dasbor](docs/screenshots/dashboard.png) |

| Editor                                 | Masuk                                |
| -------------------------------------- | ------------------------------------ |
| ![Editor](docs/screenshots/editor.png) | ![Masuk](docs/screenshots/login.png) |

| Panel admin: ringkasan                                   | Panel admin: pengguna                                               |
| -------------------------------------------------------- | ------------------------------------------------------------------- |
| ![Ringkasan admin](docs/screenshots/admin-dashboard.png) | ![Daftar pengguna di panel admin](docs/screenshots/admin-users.png) |

Contoh hasil PDF: [`docs/contoh-cv-fullstack-developer.pdf`](docs/contoh-cv-fullstack-developer.pdf).

---

## Teknologi

| Lapisan       | Teknologi                                                              |
| ------------- | ---------------------------------------------------------------------- |
| Backend       | PHP 8.3+, Laravel 13, Laravel Fortify (autentikasi)                    |
| Frontend      | Vue 3 + TypeScript, Inertia.js 3, Tailwind CSS 4, shadcn-vue (reka-ui) |
| Build         | Vite (vite-plus), Laravel Wayfinder (route helper bertipe)             |
| Database      | MySQL 8                                                                |
| Panel admin   | Filament 5                                                             |
| PDF           | spatie/browsershot + Puppeteer, headless Chrome/Chromium               |
| AI (opsional) | Anthropic PHP SDK (Claude)                                             |
| Pengujian     | PHPUnit 12, Laravel Pint, PHPStan, vue-tsc                             |
| Font CV       | Arimo, Carlito, Tinos (metrik setara Arial, Calibri, Times)            |
| Font UI       | Inter dan Source Serif 4 (di-bundle, tanpa CDN)                        |

---

## Alur pengguna

```mermaid
flowchart TD
    A[Halaman utama] -->|Lihat template| B[Galeri 100 template]
    B -->|Pakai template ini| C{Sudah masuk?}
    A -->|Buat CV| C
    C -- Belum --> T[Mode coba /coba: editor berisi contoh]
    T -->|Simpan / Unggah foto / Unduh PDF| D[Daftar / Masuk]
    D -->|Ada draf coba| F
    D -->|Tanpa draf| E
    C -- Sudah --> E[Dasbor: CV Saya]
    E -->|Buat CV baru: judul, bahasa, template| F[Editor]
    E -->|Buka CV lama| F
    E -->|Duplikat / Hapus / Unduh PDF| E

    subgraph Editor
        F --> G[Isi formulir per bagian]
        G --> H[Pratinjau A4 berubah seketika]
        G --> I[Simpan otomatis]
        F --> J[Unggah / hapus foto]
        F --> K[Ganti template, warna, perataan, bahasa]
        K --> H
        F -.->|bila AI aktif| L[Asisten AI: profil, poin, kata kunci, terjemahan]
        L -->|diperiksa lalu diterapkan| G
    end

    H --> M[Unduh PDF]
    I --> M
    M --> N[PDF A4 berteks asli]
```

Langkah-langkahnya:

1. **Halaman utama**: pengunjung melihat contoh CV, membandingkan template, lalu memilih **Buat CV** atau **Pakai template ini**.
2. **Mode coba** (tanpa akun): editor terbuka berisi contoh CV dengan template yang dipilih. Pengunjung bebas mengubah isi atau menekan **Kosongkan isi**. Drafnya disimpan sementara di sesi (divalidasi dengan aturan yang sama seperti CV biasa). Tombol **Simpan ke akun**, **Unduh PDF**, dan **Unggah foto** membuka ajakan daftar.
3. **Daftar atau masuk**: akun dibuat dengan nama, email, dan kata sandi. Bila ada draf dari mode coba, draf itu langsung disimpan sebagai CV baru dan pengguna dibawa ke editornya. Nama dan email yang dikosongkan diisi dari akun.
4. **Dasbor (CV Saya)**: daftar CV dalam bentuk lembar kecil. Tombol **CV baru** membuka dialog untuk memilih judul, bahasa dokumen, dan template. Nama dan email akun diisikan otomatis ke CV baru.
5. **Editor**: isi setiap bagian di panel kiri; pratinjau A4 di kanan langsung berubah. Perubahan tersimpan otomatis beberapa saat setelah mengetik, dan status simpan terlihat di bilah atas.
6. **Atur tampilan**: ganti template, warna aksen (untuk template berwarna), perataan paragraf, dan bahasa dokumen. Isi CV tidak berubah.
7. **Unduh PDF**: editor memastikan semua perubahan (termasuk foto) sudah tersimpan, lalu server mencetak PDF A4.

---

## Arsitektur dan alur data

Isi CV disimpan **terpisah dari tata letak**. Template hanya menentukan cara blok ditampilkan, sehingga satu data dapat dirender oleh 100 template dan menghasilkan pratinjau yang sama dengan PDF.

```mermaid
flowchart LR
    subgraph Browser
        ED[Editor Vue<br/>useResumeEditor] -->|PUT /resumes/id<br/>simpan otomatis| API
        ED --> PV[CvDocument<br/>pratinjau A4]
    end

    subgraph Laravel
        API[ResumeController@update] --> RQ[UpdateResumeRequest<br/>validasi]
        RQ --> SV[SaveResumeContent<br/>transaksi]
        SV --> DB[(MySQL)]
        PDF[DownloadResumePdfController] --> RD[ResumePdfRenderer]
        RD --> PL[ResumePayload<br/>data CV + foto]
        PL --> DB
        RD --> BL[Blade pdf/resume<br/>+ bundel cetak]
        BL --> CH[Browsershot<br/>headless Chrome]
    end

    ED -->|GET /resumes/id/pdf| PDF
    CH -->|application/pdf| ED
```

**Mesin tata letak CV** (dipakai bersama oleh pratinjau dan PDF, di `resources/js/cv`):

```mermaid
flowchart LR
    D[ResumeData] --> B[buildBlocks<br/>blocks.ts]
    B --> R[splitRegions<br/>regions.ts]
    R --> M[Ukur tinggi tiap blok<br/>di halaman uji]
    M --> P[paginate<br/>paginate.ts]
    P --> V[CvDocument.vue<br/>halaman A4 per kolom]
    V --> S[Layar: pratinjau / thumbnail]
    V --> X[Cetak: bootstrap/cv-print → PDF]
```

1. **`buildBlocks`** memecah CV menjadi blok: header, judul bagian, paragraf, entri, poin, dan baris (keahlian/bahasa). Bagian kosong dilewati.
2. **`splitRegions`** membagi blok ke area halaman: satu kolom, atau untuk template _Kreatif_: area atas, kolom utama, dan kolom samping.
3. Setiap blok **diukur** di halaman uji tak terlihat dengan font dan lebar kolom yang sebenarnya.
4. **`paginate`** menyusun blok ke halaman A4: judul bagian selalu ikut blok berikutnya, entri pendek beserta poinnya tidak dipecah, dan kapasitas halaman pertama boleh berbeda (misalnya di bawah header berfoto).
5. **`CvDocument`** merender halaman. Untuk PDF, komponen yang sama dibundel menjadi `bootstrap/cv-print` dan dicetak Chrome dengan ukuran A4 dan font yang disematkan.

**Model data utama**

| Tabel                                                                                                      | Isi                                                                                    |
| ---------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------- |
| `users`                                                                                                    | Akun, termasuk penanda `is_admin` dan `last_seen_at`                                   |
| `activity_logs`                                                                                            | Aktivitas pengguna untuk panel admin (tanpa isi CV)                                    |
| `notifications`                                                                                            | Notifikasi admin, mis. pengguna baru                                                   |
| `cv_templates`                                                                                             | Daftar template (slug, nama, deskripsi, urutan, aktif)                                 |
| `resumes`                                                                                                  | Judul, template, bahasa, warna aksen, perataan, urutan bagian, data diri, profil, foto |
| `resume_experiences`                                                                                       | Pengalaman kerja, magang, dan organisasi (dibedakan dengan `type`)                     |
| `resume_educations`, `resume_projects`, `resume_skill_groups`, `resume_certifications`, `resume_languages` | Bagian-bagian CV lainnya                                                               |

**Rute utama** (semua rute CV memerlukan login dan hanya dapat diakses pemiliknya, kecuali mode coba yang khusus untuk tamu)

| Metode        | URL                                   | Fungsi                                  |
| ------------- | ------------------------------------- | --------------------------------------- |
| GET           | `/`                                   | Halaman utama                           |
| GET           | `/coba`                               | Mode coba untuk tamu (`?template=slug`) |
| PUT           | `/coba`                               | Simpan draf tamu ke sesi (maks. 90/mnt) |
| GET           | `/dashboard`                          | Daftar CV                               |
| POST          | `/resumes`                            | Buat CV baru                            |
| GET           | `/resumes/{id}/edit`                  | Editor                                  |
| PUT           | `/resumes/{id}`                       | Simpan isi CV (simpan otomatis)         |
| DELETE        | `/resumes/{id}`                       | Hapus CV beserta fotonya                |
| POST          | `/resumes/{id}/duplicate`             | Duplikat CV                             |
| GET           | `/resumes/{id}/pdf`                   | Unduh PDF (maks. 10 per menit)          |
| POST / DELETE | `/resumes/{id}/photo`                 | Unggah / hapus foto                     |
| POST          | `/resumes/{id}/assist`                | Asisten AI (opsional, dibatasi)         |
| POST          | `/resumes/{id}/translate`             | Terjemahan AI (opsional, dibatasi)      |
| —             | `/admin`                              | Panel admin: ringkasan dan pemantauan   |
| —             | `/admin/activity-logs`                | Semua aktivitas pengguna, dengan filter |
| —             | `/admin/users`, `/admin/cv-templates` | Kelola pengguna dan template            |

---

## Panel admin dan pemantauan

Panel admin ada di `/admin`. Tampilannya putih bersih seperti konsol operasional (WMS): tabel padat bergaris dengan nomor dan kode berhuruf monospace, tanpa warna yang tidak perlu. Gayanya ada di `resources/css/filament/admin/theme.css` dan ikut dibangun oleh `npm run build`.

### Membuat akun admin

Jalankan perintah ini dan isi nama serta kata sandi saat diminta (kata sandi tidak tampil saat diketik):

```bash
php artisan cvforge:admin
```

Bisa juga langsung menyebut email. Bila akun dengan email itu sudah ada, perintah ini menawarkan untuk menjadikannya admin:

```bash
php artisan cvforge:admin nama@domain.com
```

Admin juga dapat memberi atau mencabut akses admin pengguna lain dari menu **Pengguna**. Untuk pengembangan lokal, seeder menyediakan `admin@cvforge.test` (lihat [Akun demo](#akun-demo-dan-data-contoh)).

### Alur pemantauan

```mermaid
flowchart LR
    U[Pengguna memakai situs] --> C[Controller dan listener<br/>daftar, masuk, CV, PDF, foto, AI]
    U --> M[Middleware TrackLastSeen<br/>maks. 1x per menit]
    C --> L[(activity_logs)]
    M --> S[(users.last_seen_at)]
    C -->|pendaftaran baru| N[(notifications)]
    L --> D[Panel admin<br/>diperbarui tiap 15-60 detik]
    S --> D
    N --> B[Lonceng notifikasi admin]
```

### Yang dicatat

| Aktivitas                             | Kapan tercatat                                            | Detail yang disimpan           |
| ------------------------------------- | --------------------------------------------------------- | ------------------------------ |
| Mendaftar                             | Akun baru dibuat (admin juga mendapat notifikasi)         | —                              |
| Masuk                                 | Pengguna masuk                                            | —                              |
| Membuat CV                            | CV baru dibuat                                            | Nomor CV, template             |
| Menyunting CV                         | Simpan otomatis; paling banyak sekali per 30 menit per CV | Nomor CV, template             |
| Menduplikat / Menghapus CV            | Tombol duplikat atau hapus                                | Nomor CV, template             |
| Mengunduh PDF                         | PDF berhasil dibuat                                       | Nomor CV, template, bahasa     |
| Mengunggah foto                       | Foto tersimpan                                            | Nomor CV, template             |
| Memakai asisten AI / Menerjemahkan CV | Hasil AI berhasil dibuat                                  | Jenis tugas atau bahasa tujuan |

**Privasi:** isi CV (nama di CV, pengalaman, judul CV, dan lainnya) tidak pernah dicatat, dan admin tetap tidak dapat membuka isi CV pengguna. Riwayat aktivitas ikut terhapus bila akun pengguna dihapus.

"Sedang online" berarti pengguna membuka aplikasi dalam 5 menit terakhir; daftar di ringkasan menampilkan yang aktif dalam 15 menit terakhir.

---

## Template

### Rekomendasi untuk melamar

Delapan template ini diberi label **Rekomendasi**: satu kolom, teks asli, dan terbaca rapi oleh sistem ATS. Labelnya dapat diubah admin di `/admin/cv-templates`.

| Template         | Paling cocok untuk                                                                 |
| ---------------- | ---------------------------------------------------------------------------------- |
| Professional ATS | Pilihan utama untuk hampir semua lowongan; jabatan dan tanggal mudah dipindai.     |
| Classic ATS      | Bank, BUMN, instansi pemerintah, dan perusahaan yang konservatif.                  |
| Modern ATS       | Startup, teknologi, pemasaran, dan bidang kreatif yang tetap melamar lewat portal. |
| Executive ATS    | Posisi senior, hukum, keuangan, dan konsultan; huruf serif yang formal.            |
| Compact ATS      | Pengalaman panjang yang perlu muat di satu atau dua halaman.                       |
| Minimal ATS      | Lulusan baru dan magang; bersih dan mudah dibaca.                                  |
| Portrait ATS     | Lowongan yang meminta pas foto, tetap satu kolom dan aman untuk ATS.               |
| Clean ATS        | CV yang rapi di tengah untuk lamaran umum dan dikirim lewat email.                 |

Template _Kreatif_ (dua kolom) dan yang penuh ornamen lebih cocok dikirim langsung ke perekrut, misalnya lewat email, LinkedIn, atau dicetak saat wawancara.

### Semua template

| Slug            | Nama             | Kolom | Warna               | Foto                                |
| --------------- | ---------------- | ----- | ------------------- | ----------------------------------- |
| `classic`       | Classic ATS      | 1     | Hitam-putih         | Opsional                            |
| `compact`       | Compact ATS      | 1     | Hitam-putih         | Opsional                            |
| `modern`        | Modern ATS       | 1     | Hitam-putih         | Opsional                            |
| `professional`  | Professional ATS | 1     | Hitam-putih         | Opsional                            |
| `executive`     | Executive ATS    | 1     | Hitam-putih (serif) | Opsional                            |
| `accent`        | Accent ATS       | 1     | Aksen               | Opsional                            |
| `banner`        | Banner ATS       | 1     | Aksen               | Opsional                            |
| `block`         | Block ATS        | 1     | Aksen               | Opsional                            |
| `portrait`      | Portrait ATS     | 1     | Aksen               | Bulat                               |
| `timeline`      | Timeline ATS     | 1     | Aksen               | Bulat                               |
| `gutter`        | Gutter ATS       | 1     | Hitam-putih         | Opsional                            |
| `minimal`       | Minimal ATS      | 1     | Hitam-putih         | Opsional                            |
| `monogram`      | Monogram ATS     | 1     | Aksen               | Opsional; tanpa foto tampil inisial |
| `numbered`      | Numbered ATS     | 1     | Aksen               | Opsional                            |
| `leader`        | Leader ATS       | 1     | Hitam-putih         | Opsional                            |
| `bold`          | Bold ATS         | 1     | Hitam-putih         | Opsional                            |
| `ribbon`        | Ribbon ATS       | 1     | Aksen               | Opsional                            |
| `corporate`     | Corporate ATS    | 1     | Hitam-putih         | Opsional                            |
| `tabbed`        | Tab ATS          | 1     | Aksen               | Opsional                            |
| `elegant`       | Elegant ATS      | 1     | Hitam-putih (serif) | Opsional                            |
| `lumen`         | Lumen            | 1     | Aksen               | Bulat bercincin                     |
| `heritage`      | Heritage         | 1     | Hitam-putih (serif) | Bulat kecil                         |
| `skyline`       | Skyline          | 1     | Hitam-putih         | Persegi                             |
| `prestige`      | Prestige         | 1     | Aksen + emas        | Persegi berbingkai emas             |
| `wave`          | Gelombang        | 1     | Aksen               | Bulat bercincin                     |
| `geo`           | Geometri         | 1     | Aksen               | Bulat                               |
| `orbit`         | Orbit            | 1     | Aksen               | Bulat                               |
| `fresh`         | Segar            | 1     | Aksen               | Sudut membulat                      |
| `contrast`      | Kontras          | 1     | Aksen               | Setinggi header, di tepi kiri       |
| `label`         | Pita             | 1     | Aksen               | Persegi                             |
| `sidebar`       | Sidebar Kreatif  | 2     | Aksen               | Bulat                               |
| `sidebar-light` | Sidebar Terang   | 2     | Aksen               | Bulat                               |
| `two-column`    | Dua Kolom        | 2     | Aksen               | Bulat                               |
| `split`         | Split            | 2     | Hitam-putih         | Opsional                            |
| `magazine`      | Majalah          | 2     | Aksen               | Bulat                               |
| `folio`         | Folio            | 2     | Hitam-putih         | Opsional                            |
| `noir`          | Noir             | 2     | Arang + emas        | Bulat                               |
| `slate`         | Slate            | 2     | Aksen               | Bulat                               |
| `pastel`        | Pastel           | 2     | Aksen               | Melengkung di atas                  |
| `atelier`       | Atelier          | 2     | Aksen (serif)       | Bulat                               |
| `frame`         | Bingkai          | 2     | Hitam-putih         | Persegi                             |
| `clean`         | Clean ATS        | 1     | Hitam-putih         | Opsional                            |
| `simple`        | Simple ATS       | 1     | Hitam-putih         | Opsional                            |
| `formal`        | Formal ATS       | 1     | Hitam-putih (serif) | Opsional                            |
| `academic`      | Akademik ATS     | 1     | Hitam-putih (serif) | Opsional                            |
| `crisp`         | Crisp ATS        | 1     | Hitam-putih         | Opsional                            |
| `nordic`        | Nordic ATS       | 1     | Hitam-putih         | Opsional                            |
| `office`        | Office ATS       | 1     | Hitam-putih         | Opsional                            |
| `ledger`        | Ledger ATS       | 1     | Hitam-putih         | Opsional                            |
| `concise`       | Ringkas ATS      | 1     | Hitam-putih         | Opsional                            |
| `serif`         | Serif ATS        | 1     | Hitam-putih (serif) | Opsional                            |
| `swiss`         | Swiss ATS        | 1     | Hitam-putih         | Opsional                            |
| `outline`       | Garis Tepi ATS   | 1     | Hitam-putih         | Opsional                            |
| `bar`           | Bar ATS          | 1     | Hitam-putih         | Opsional                            |
| `centered`      | Center ATS       | 1     | Hitam-putih         | Opsional                            |
| `graphite`      | Grafit ATS       | 1     | Hitam-putih         | Opsional                            |
| `sided`         | Dua Sisi ATS     | 1     | Hitam-putih         | Opsional                            |
| `tidy`          | Rapi ATS         | 1     | Hitam-putih         | Opsional                            |
| `plain`         | Polos ATS        | 1     | Hitam-putih         | Opsional                            |
| `azure`         | Azure            | 1     | Aksen               | Bulat bercincin                     |
| `horizon`       | Horizon          | 1     | Aksen               | Opsional                            |
| `stripe`        | Garis Samping    | 1     | Aksen               | Opsional                            |
| `corner`        | Sudut            | 1     | Aksen               | Bulat                               |
| `sunrise`       | Fajar            | 1     | Aksen               | Bulat                               |
| `ink`           | Tinta            | 1     | Aksen (serif)       | Opsional                            |
| `vista`         | Vista            | 1     | Aksen               | Bulat bercincin                     |
| `marker`        | Stabilo          | 1     | Aksen               | Persegi                             |
| `emblem`        | Lencana          | 1     | Aksen               | Bulat bercincin                     |
| `pulse`         | Denyut           | 1     | Aksen               | Opsional                            |
| `crest`         | Puncak           | 1     | Aksen               | Bulat                               |
| `harbor`        | Dermaga          | 1     | Aksen               | Persegi                             |
| `bloom`         | Mekar            | 1     | Aksen               | Bulat                               |
| `spark`         | Percik           | 1     | Aksen               | Opsional                            |
| `linear`        | Linear           | 1     | Aksen               | Opsional                            |
| `metro`         | Metro            | 1     | Aksen               | Opsional                            |
| `canvas`        | Kanvas           | 1     | Aksen               | Bulat                               |
| `aurora`        | Aurora           | 1     | Aksen               | Bulat bercincin                     |
| `sage`          | Sage             | 1     | Aksen               | Bulat                               |
| `royal`         | Royal            | 1     | Aksen (serif)       | Opsional                            |
| `zenith`        | Zenit            | 1     | Aksen               | Melengkung                          |
| `ember`         | Bara             | 1     | Aksen               | Bulat                               |
| `coral`         | Koral            | 1     | Aksen               | Bulat                               |
| `tide`          | Pasang           | 1     | Aksen               | Opsional                            |
| `mint`          | Mint             | 1     | Aksen               | Bulat                               |
| `ridge`         | Punggung         | 1     | Aksen               | Persegi                             |
| `harmony`       | Harmoni          | 2     | Aksen               | Bulat                               |
| `summit`        | Summit           | 2     | Aksen               | Bulat bercincin                     |
| `balance`       | Seimbang         | 2     | Aksen               | Persegi                             |
| `ivory`         | Gading           | 2     | Hitam-putih (serif) | Bulat                               |
| `duo`           | Duo              | 2     | Aksen               | Bulat                               |
| `cobalt`        | Kobalt           | 2     | Aksen               | Melengkung                          |
| `terra`         | Terra            | 2     | Aksen               | Bulat                               |
| `mosaic`        | Mozaik           | 2     | Aksen               | Persegi                             |
| `crystal`       | Kristal          | 2     | Aksen               | Melengkung                          |
| `quartz`        | Kuarsa           | 2     | Hitam-putih         | Bulat                               |
| `sierra`        | Sierra           | 2     | Arang               | Persegi                             |
| `pixel`         | Piksel           | 2     | Aksen               | Bulat                               |
| `lotus`         | Teratai          | 2     | Aksen               | Bulat                               |
| `vertex`        | Verteks          | 2     | Aksen               | Bulat bercincin                     |
| `echo`          | Gema             | 2     | Hitam-putih         | Opsional                            |

Template dari `lumen` sampai `frame` adalah keluarga **studio**: gaya desainer seperti di aplikasi pembuat CV daring, dengan foto, ornamen sudut, gelombang, judul bagian berbentuk pil atau pita, dan tabel data diri. Template dari `clean` sampai `echo` adalah **katalog**: dibangun di atas komponen studio dari gabungan beberapa _trait_ gaya (huruf, kepala, judul bagian, ornamen halaman, bentuk foto, panel samping) di `resources/js/cv/templates/kit/`. Ornamen selalu digambar dengan CSS di belakang teks, jadi PDF-nya tetap berteks asli. Semua foto bersifat opsional; tanpa foto, tata letaknya menyesuaikan.

Warna aksen: navy, teal, hijau (forest), merah marun (burgundy), ungu (plum), dan arang (charcoal).

> **Catatan ATS:** template satu kolom paling aman untuk portal lowongan. Template _Kreatif_ tetap berteks asli, tetapi sebagian ATS lama dapat membaca urutan kolomnya kurang tepat, jadi lebih cocok untuk dikirim langsung ke perekrut.

---

## Instalasi

### Prasyarat

- PHP **8.3+** dengan ekstensi `pdo_mysql`, `gd`, `exif`, `mbstring`, `fileinfo`
- Composer 2
- Node.js **20+** dan npm
- MySQL **8**
- Google Chrome/Chromium (untuk mencetak PDF), atau Chrome yang diunduh Puppeteer
- Opsional: [Laravel Herd](https://herd.laravel.com) untuk domain lokal seperti `http://cv.test`

### Langkah

```bash
git clone https://github.com/reza-programer/CV-.git cvforge
```

```bash
cd cvforge
```

```bash
composer install
```

```bash
npm install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

Buat database kosong `cvforge` di MySQL, lalu sesuaikan `DB_*` di `.env` bila perlu. Setelah itu jalankan migrasi (template ikut dibuat oleh migrasi) dan data contoh:

```bash
php artisan migrate --seed
```

Bangun aset frontend dan **bundel cetak PDF** (`npm run build` menjalankan keduanya):

```bash
npm run build
```

Jalankan aplikasi. Dengan Herd, cukup buka `http://cv.test`. Tanpa Herd:

```bash
composer run dev
```

Lalu buka alamat yang ditampilkan (biasanya `http://127.0.0.1:8000`).

### Chrome untuk PDF

Isi `CVFORGE_CHROME_PATH` dengan lokasi Chrome yang terpasang, misalnya:

- Windows: `C:/Program Files/Google/Chrome/Application/chrome.exe`
- macOS: `/Applications/Google Chrome.app/Contents/MacOS/Google Chrome`
- Linux: `/usr/bin/google-chrome`

Atau kosongkan dan unduh Chrome milik Puppeteer:

```bash
npx puppeteer browsers install chrome
```

Di server Linux tanpa sandbox (misalnya kontainer), set `CVFORGE_CHROME_NO_SANDBOX=true`.

---

## Konfigurasi `.env`

| Variabel                        | Bawaan                     | Keterangan                                                                  |
| ------------------------------- | -------------------------- | --------------------------------------------------------------------------- |
| `APP_URL`                       | `http://localhost`         | URL aplikasi, mis. `http://cv.test`                                         |
| `APP_LOCALE`                    | `id`                       | Bahasa antarmuka                                                            |
| `APP_TIMEZONE`                  | `UTC`                      | Zona waktu, mis. `Asia/Jakarta`                                             |
| `DB_CONNECTION` … `DB_PASSWORD` | `mysql`, `cvforge`, `root` | Koneksi MySQL                                                               |
| `CVFORGE_NODE_BINARY`           | `node`                     | Lokasi Node.js untuk Browsershot (pakai garis miring `/` di Windows)        |
| `CVFORGE_CHROME_PATH`           | kosong                     | Lokasi Chrome/Chromium; kosong = Chrome dari Puppeteer                      |
| `CVFORGE_CHROME_NO_SANDBOX`     | `false`                    | `true` untuk kontainer/server tanpa sandbox                                 |
| `CVFORGE_PDF_TIMEOUT`           | `60`                       | Batas waktu cetak PDF (detik)                                               |
| `ANTHROPIC_API_KEY`             | kosong                     | Mengaktifkan asisten dan terjemahan AI. Bila kosong, fitur AI disembunyikan |
| `CVFORGE_CREATOR_NAME`          | Mohamad Reza Nurpadilah    | Nama pembuat di footer situs                                                |
| `CVFORGE_CREATOR_INSTAGRAM`     | `kezottt_`                 | Username Instagram di footer (kosongkan untuk menyembunyikan)               |
| `CVFORGE_CREATOR_LINKEDIN`      | pencarian nama di LinkedIn | URL profil LinkedIn di footer                                               |
| `CVFORGE_CREATOR_EMAIL`         | `reza25022003@gmail.com`   | Email kontak di footer                                                      |
| `CVFORGE_AI_MODEL`              | `claude-opus-5-5`          | Model Claude yang dipakai                                                   |
| `CVFORGE_AI_EFFORT`             | `low`                      | Tingkat usaha model (`low`, `medium`, `high`)                               |
| `CVFORGE_AI_TIMEOUT`            | `60`                       | Batas waktu permintaan AI (detik)                                           |

Jangan pernah meng-commit file `.env`; file ini sudah ada di `.gitignore`.

---

## Akun demo dan data contoh

`php artisan migrate --seed` membuat dua akun lokal dengan kata sandi `password`:

| Email                | Peran                          |
| -------------------- | ------------------------------ |
| `admin@cvforge.test` | Admin (dapat membuka `/admin`) |
| `user@cvforge.test`  | Pengguna biasa                 |

Contoh CV lengkap untuk posisi Fullstack Developer (atas nama Mohamad Reza Nurpadilah) dapat ditambahkan ke akun `user@cvforge.test`:

```bash
php artisan db:seed --class=FullstackExampleSeeder
```

Perusahaan, angka, email, dan tautan pada contoh hanya ilustrasi. Ganti dengan data asli sebelum dikirim.

> Akun dan kata sandi di atas hanya untuk pengembangan lokal. Jangan menjalankan seeder ini di server produksi.

---

## Perintah sehari-hari

| Perintah              | Fungsi                                                 |
| --------------------- | ------------------------------------------------------ |
| `composer run dev`    | Server Laravel, antrean, dan Vite dev server sekaligus |
| `npm run dev`         | Vite dev server saja                                   |
| `npm run build`       | Build produksi + bundel cetak PDF                      |
| `npm run build:print` | Hanya bundel cetak PDF (`bootstrap/cv-print`)          |
| `php artisan test`    | Menjalankan seluruh pengujian PHPUnit                  |
| `composer run test`   | Pint (cek), PHPStan, lalu PHPUnit                      |
| `vendor/bin/pint`     | Merapikan kode PHP                                     |
| `npm run check:fix`   | Lint dan format kode frontend                          |
| `npm run types:check` | Pemeriksaan tipe TypeScript/Vue                        |

Pengujian PDF memerlukan Chrome dan bundel cetak (`npm run build`); bila salah satunya tidak ada, pengujian tersebut otomatis dilewati.

---

## Struktur folder

```text
app/
├── Actions/Resumes/        CreateResume, SaveResumeContent, DuplicateResume
├── Console/Commands/       CreateAdmin (php artisan cvforge:admin)
├── Enums/                  ActivityEvent, ResumeLocale, ResumeAccent, ParagraphAlign, ...
├── Filament/
│   ├── Pages/              Dashboard (Ringkasan)
│   ├── Resources/          ActivityLogs, Users (+ riwayat aktivitas), CvTemplates
│   └── Widgets/            UsageOverview, ActivityChart, OnlineUsers, LatestActivity
├── Http/
│   ├── Controllers/        HomeController, Resumes/* (CRUD, PDF, foto, AI)
│   ├── Middleware/         TrackLastSeen (status online)
│   ├── Requests/Resumes/   UpdateResumeRequest (validasi isi CV)
│   └── Responses/          LoginResponse (admin → /admin)
├── Listeners/              RecordSignIn, RecordRegistration (+ notifikasi admin), ClaimGuestResume (draf mode coba → CV)
├── Models/                 User, Resume, CvTemplate, ActivityLog, Resume* (bagian CV)
├── Policies/               ResumePolicy (hanya pemilik)
├── Services/               ResumePdfRenderer, ResumePhotos, ResumeAssistant, ResumeTranslator
└── Support/Resumes/        ResumePayload, TranslatableFields
config/cvforge.php          Pengaturan PDF dan AI
database/
├── migrations/             Skema + daftar template
└── seeders/                Akun demo, contoh CV fullstack
resources/
├── css/app.css             Token desain antarmuka
├── js/
│   ├── cv/                 Mesin CV: blocks, regions, paginate, CvDocument, templates/*
│   ├── components/
│   │   ├── editor/         Formulir editor, toolbar, asisten AI
│   │   ├── resumes/        Kartu CV, dialog CV baru, pemilih template
│   │   ├── site/           Header, footer, dan bagian halaman utama
│   │   └── cv/             Pratinjau, thumbnail, lembar CV
│   ├── composables/        useResumeEditor (simpan otomatis)
│   ├── layouts/            AppLayout, AuthLayout
│   └── pages/              Welcome, resumes/Index, resumes/Edit, auth/*, settings/*
└── views/pdf/resume.blade.php   Halaman HTML yang dicetak menjadi PDF
tests/                      Feature dan Unit (PHPUnit)
vite.print.config.ts        Build bundel cetak → bootstrap/cv-print
docs/                       Contoh PDF dan tangkapan layar
```

---

## Panduan pengembang

### Prinsip

- **Data terpisah dari tampilan.** Validasi ada di `UpdateResumeRequest`, penyimpanan di `SaveResumeContent`, dan tata letak di `resources/js/cv`. Template tidak boleh mengubah data.
- **Pratinjau = PDF.** Jangan menambahkan gaya khusus layar ke template; gaya CV hanya berada di CSS template dan `resources/js/cv/styles/document.css`.
- **Ramah ATS.** Hindari teks di dalam gambar, bar keahlian, ikon yang menggantikan teks kontak, dan `opacity` pada teks (urutan teks di PDF dapat berubah).
- **Otorisasi.** Setiap rute CV memakai `ResumePolicy`; pengguna hanya dapat mengakses CV miliknya.

### Menambah template baru

1. Buat folder `resources/js/cv/templates/<slug>/` berisi `<Nama>Template.vue` dan `<slug>.css`. Gunakan template yang sudah ada sebagai acuan (mis. `classic`). Untuk template bergaya desainer, cukup tambahkan `<slug>.css` di `templates/studio/` dan daftarkan dengan `studio({...})` atau `studioPanel({...})`; komponennya sudah tersedia. Cara tercepat: tambahkan satu baris resep di `templates/kit/catalog.ts` (`single({...})` atau `columns({...})`) dengan gabungan _trait_ dari `kit.css`, tanpa file CSS baru.
2. Tambahkan slug ke tipe `TemplateSlug` di `resources/js/cv/types.ts`.
3. Daftarkan di `resources/js/cv/templates/index.ts`: komponen, `layout` (`single` atau `columns`), dan `coloured`. Impor CSS-nya di file yang sama.
4. Tambahkan slug ke `CvTemplate::SLUGS` (`app/Models/CvTemplate.php`).
5. Buat migrasi yang menambahkan baris ke `cv_templates` (slug, nama, deskripsi, urutan).
6. Jalankan `npm run build` agar bundel cetak PDF ikut diperbarui, lalu `php artisan migrate`.
7. Periksa CV pendek, panjang (3+ halaman), dan dengan foto di pratinjau dan PDF.

### Menambah bagian CV baru

Tambahkan kolom/tabel lewat migrasi, model dan relasinya, aturan di `UpdateResumeRequest`, penyimpanan di `SaveResumeContent`, payload di `ResumePayload`, tipe di `resources/js/cv/types.ts`, blok di `blocks.ts`, formulir di `components/editor/sections`, dan judul bagian di `format.ts` (ID dan EN).

---

## Keamanan dan privasi

- Foto disimpan di disk privat dan hanya dapat diambil pemilik CV melalui rute yang diotorisasi; foto ikut terhapus saat CV dihapus.
- Unduhan PDF dan permintaan AI dibatasi (rate limit) per pengguna.
- Asisten AI hanya menerima teks CV yang relevan, tidak menulis ke database, dan hasilnya selalu ditinjau pengguna sebelum diterapkan.
- Log aktivitas untuk admin hanya menyimpan jenis aktivitas, waktu, nomor CV, template, dan bahasa, tanpa isi CV. Log tidak dapat diubah atau dihapus dari panel admin.
- Kunci API dan kredensial disimpan di `.env`, bukan di repositori.

---

## Pemecahan masalah

| Gejala                                            | Penyebab dan solusi                                                                                                                        |
| ------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ |
| PDF gagal dibuat                                  | Periksa `CVFORGE_CHROME_PATH` dan `CVFORGE_NODE_BINARY`, lalu pastikan `npm run build` sudah dijalankan sehingga `bootstrap/cv-print` ada. |
| Halaman kosong setelah menghentikan `npm run dev` | Hapus file `public/hot`, lalu jalankan `npm run build`.                                                                                    |
| Foto gagal diunggah                               | Pastikan ekstensi PHP `gd` (dan `exif` untuk foto dari ponsel) aktif, serta folder `storage/` dapat ditulis.                               |
| Tombol AI tidak muncul                            | `ANTHROPIC_API_KEY` belum diisi; jalankan `php artisan config:clear` setelah mengubah `.env`.                                              |
| `SQLSTATE[HY000] [2002]`                          | Layanan MySQL belum berjalan atau `DB_*` di `.env` salah.                                                                                  |

---

## Pembuat

Dibuat oleh **Mohamad Reza Nurpadilah**.

- Instagram: [@kezottt_](https://www.instagram.com/kezottt_/)
- LinkedIn: [Mohamad Reza Nurpadilah](https://www.linkedin.com/search/results/people/?keywords=Mohamad%20Reza%20Nurpadilah)
- Email: [reza25022003@gmail.com](mailto:reza25022003@gmail.com)

### Logo

Logo **kezot**: lembar CV berfoto dengan pensil, ditambah tulisan kezot dan slogan "Buat CV Profesional, Raih Peluangmu". File ada di `docs/brand/`:

| File                     | Kegunaan                                             |
| ------------------------ | ---------------------------------------------------- |
| `kezot-logo.png`         | Logo lengkap: ikon, tulisan kezot, dan slogan        |
| `kezot-icon.svg`         | Ikon vektor, tajam di ukuran berapa pun              |
| `kezot-icon-1024.png`    | Ikon PNG transparan 1024 × 1024                      |
| `kezot-wordmark.png`     | Tulisan kezot, latar transparan                      |
| `kezot-profile-1080.png` | Foto profil Instagram/LinkedIn (aman dipotong bulat) |

Favicon situs (`public/favicon.svg`, `favicon.ico`, `apple-touch-icon.png`) memakai ikon yang sama.

Nama teknis proyek di kode tetap `cvforge` (mis. `config/cvforge.php`, `php artisan cvforge:admin`, variabel `CVFORGE_*`).
