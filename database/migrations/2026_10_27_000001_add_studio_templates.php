<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fifteen photo-led designer templates in the style of popular online CV
     * makers: ten single-column and five two-column.
     *
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private array $templates = [
        ['lumen', 'Lumen', 'Foto bulat di kanan dengan cincin warna aksen, judul bagian kapital tebal, dan pola titik di sudut.'],
        ['heritage', 'Heritage', 'Huruf serif dengan foto bulat kecil di samping nama dan tab gelap di tepi atas. Hitam-putih.'],
        ['skyline', 'Skyline', 'Nama kapital berjarak lebar, foto di kanan, kontak berikon, dan judul bagian renggang. Hitam-putih.'],
        ['prestige', 'Prestige', 'Segitiga warna aksen dan emas di dua sudut, foto berbingkai, judul bagian berbentuk pil, dan tabel data diri.'],
        ['wave', 'Gelombang', 'Gelombang warna aksen di atas dan bawah halaman, foto bulat, judul bagian berbentuk pil, dan tabel data diri.'],
        ['geo', 'Geometri', 'Segitiga berlapis di sudut kiri atas dan penanda kotak pada judul bagian.'],
        ['orbit', 'Orbit', 'Foto bulat di atas dua lingkaran warna aksen di sudut kiri atas.'],
        ['fresh', 'Segar', 'Kartu header berwarna lembut dengan foto bersudut bulat dan judul bagian warna aksen.'],
        ['contrast', 'Kontras', 'Header selebar halaman: nama di panel warna aksen, kontak di panel abu-abu.'],
        ['label', 'Pita', 'Judul bagian di atas pita warna aksen dengan ujung berlekuk.'],
        ['noir', 'Noir', 'Panel samping arang dengan foto bulat dan label emas, aksen emas di sudut kanan atas.'],
        ['slate', 'Slate', 'Panel samping gelap dengan foto bulat; judul bagian berbentuk pil di kedua kolom.'],
        ['pastel', 'Pastel', 'Panel samping berwarna lembut dengan foto melengkung dan judul bagian pil yang lembut.'],
        ['atelier', 'Atelier', 'Foto bulat dan nama serif di atas, lengkung warna aksen di dua sudut, lalu dua kolom.'],
        ['frame', 'Bingkai', 'Garis hitam di tepi atas dan bawah, foto berbingkai di kolom samping, dan ikon pada judul bagian.'],
    ];

    /**
     * Single-column templates first, then the two-column ones.
     *
     * @var list<string>
     */
    private array $order = [
        'classic', 'compact', 'modern', 'professional', 'executive', 'accent', 'banner', 'block',
        'portrait', 'timeline', 'gutter', 'minimal',
        'monogram', 'numbered', 'leader', 'bold', 'ribbon', 'corporate', 'tabbed', 'elegant',
        'lumen', 'heritage', 'skyline', 'prestige', 'wave', 'geo', 'orbit', 'fresh', 'contrast', 'label',
        'sidebar', 'sidebar-light', 'two-column', 'split', 'magazine', 'folio',
        'noir', 'slate', 'pastel', 'atelier', 'frame',
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->templates as [$slug, $name, $description]) {
            DB::table('cv_templates')->insertOrIgnore([
                'slug' => $slug,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($this->order as $position => $slug) {
            DB::table('cv_templates')->where('slug', $slug)->update(['sort_order' => $position + 1]);
        }
    }

    public function down(): void
    {
        DB::table('cv_templates')->whereIn('slug', array_column($this->templates, 0))->delete();
    }
};
