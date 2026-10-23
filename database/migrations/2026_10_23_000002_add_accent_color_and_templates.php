<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private array $templates = [
        ['executive', 'Executive ATS', 'Tipografi serif yang formal dengan nama di tengah dan garis ganda. Cocok untuk posisi senior dan instansi.'],
        ['accent', 'Accent ATS', 'Nama dan judul bagian berwarna aksen pilihan Anda. Isi tetap hitam dan satu kolom.'],
        ['banner', 'Banner ATS', 'Header berwarna penuh di bagian atas halaman, isi tetap hitam-putih dan satu kolom.'],
        ['block', 'Block ATS', 'Judul bagian berupa blok warna yang tegas sehingga setiap bagian mudah dipindai.'],
    ];

    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('accent_color', 20)->default('navy')->after('paragraph_align');
        });

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

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('accent_color');
        });
    }
};
