<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Template metadata only. Each slug maps to a layout component in
     * resources/js/cv/templates, so the rows ship with the schema instead of
     * being created from the admin panel.
     */
    public function up(): void
    {
        Schema::create('cv_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 80);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $rows = [
            ['classic', 'Classic ATS', 'Nama di atas, kontak satu baris, judul bagian kapital dengan garis tipis. Aman untuk hampir semua lamaran.'],
            ['compact', 'Compact ATS', 'Satu kolom yang lebih rapat untuk CV dengan banyak pengalaman, tanpa mengorbankan keterbacaan.'],
            ['modern', 'Modern ATS', 'Tipografi lebih modern dengan header rata kiri. Tetap hitam-putih dan satu kolom.'],
            ['professional', 'Professional ATS', 'Hierarki jabatan dan pendidikan yang tegas dengan tanggal rata kanan.'],
        ];

        $now = now();

        foreach ($rows as $index => [$slug, $name, $description]) {
            DB::table('cv_templates')->insert([
                'slug' => $slug,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_templates');
    }
};
