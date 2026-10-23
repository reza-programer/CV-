<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Single-column templates with photos and icons, then two-column
     * "creative" layouts that trade some ATS compatibility for a more
     * graphic page.
     *
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private array $templates = [
        ['portrait', 'Portrait ATS', 'Foto bulat di tengah, kontak berikon, dan judul bagian berikon. Tetap satu kolom.'],
        ['timeline', 'Timeline ATS', 'Pengalaman disusun sebagai garis waktu dengan titik aksen dan tanggal di depan. Satu kolom.'],
        ['gutter', 'Gutter ATS', 'Judul bagian di kolom kiri yang sempit, isi di kanan. Hitam-putih dan mudah dipindai.'],
        ['minimal', 'Minimal ATS', 'Tipografi ringan dengan banyak ruang putih dan tanpa garis. Hitam-putih.'],
        ['sidebar', 'Sidebar Kreatif', 'Panel samping berwarna dengan foto bulat, kontak, pendidikan, dan keahlian; pengalaman berupa garis waktu.'],
        ['sidebar-light', 'Sidebar Terang', 'Panel samping kanan berwarna lembut dengan foto bulat dan label keahlian.'],
        ['two-column', 'Dua Kolom', 'Header berfoto dan profil selebar halaman, lalu dua kolom yang dipisahkan garis.'],
        ['split', 'Split', 'Hitam-putih: riwayat kerja di kolom lebar, pendidikan dan keahlian di kolom sempit.'],
    ];

    public function up(): void
    {
        $now = now();
        $order = (int) DB::table('cv_templates')->max('sort_order');

        foreach ($this->templates as [$slug, $name, $description]) {
            DB::table('cv_templates')->insertOrIgnore([
                'slug' => $slug,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'sort_order' => ++$order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('cv_templates')->whereIn('slug', array_column($this->templates, 0))->delete();
    }
};
