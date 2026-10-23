<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ten more layouts: eight single-column ATS templates and two
     * two-column "Kreatif" ones.
     *
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private array $templates = [
        ['monogram', 'Monogram ATS', 'Inisial nama dalam kotak warna aksen di samping nama, judul bagian bergaris pendek. Satu kolom.'],
        ['numbered', 'Numbered ATS', 'Setiap bagian bernomor 01, 02, 03 dengan angka warna aksen dan garis sampai tepi. Satu kolom.'],
        ['leader', 'Leader ATS', 'Jabatan dan tanggal dihubungkan garis titik seperti daftar isi. Hitam-putih, satu kolom.'],
        ['bold', 'Bold ATS', 'Nama besar dan tebal di atas garis hitam tegas. Hitam-putih untuk kesan percaya diri.'],
        ['ribbon', 'Ribbon ATS', 'Pita warna aksen di tepi kiri setiap halaman, isi tetap satu kolom.'],
        ['corporate', 'Corporate ATS', 'Header abu-abu muda dengan kontak tersusun di sisi kanan. Formal dan hitam-putih.'],
        ['tabbed', 'Tab ATS', 'Judul bagian berupa label warna aksen di atas garis, seperti pembatas map. Satu kolom.'],
        ['elegant', 'Elegant ATS', 'Huruf serif, nama besar yang ringan, dan judul bagian miring. Hitam-putih dengan banyak ruang putih.'],
        ['magazine', 'Majalah', 'Header warna penuh dengan foto bulat, profil selebar halaman, lalu kolom lebar dan kolom samping.'],
        ['folio', 'Folio', 'Header selebar halaman, lalu panel abu-abu di kiri untuk pendidikan dan keahlian. Hitam-putih.'],
    ];

    /**
     * Single-column templates first, then the two-column ones, so the
     * gallery and picker keep the safest choices together.
     *
     * @var list<string>
     */
    private array $order = [
        'classic', 'compact', 'modern', 'professional', 'executive', 'accent', 'banner', 'block',
        'portrait', 'timeline', 'gutter', 'minimal',
        'monogram', 'numbered', 'leader', 'bold', 'ribbon', 'corporate', 'tabbed', 'elegant',
        'sidebar', 'sidebar-light', 'two-column', 'split', 'magazine', 'folio',
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
