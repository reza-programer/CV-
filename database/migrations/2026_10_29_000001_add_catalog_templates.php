<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fifty-nine catalogue templates (resources/js/cv/templates/kit), which
     * bring the total to a hundred: eighteen plain black-and-white ATS ones,
     * twenty-six coloured single-column ones and fifteen with two columns.
     *
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private array $templates = [
        ['clean', 'Clean ATS', 'Nama dan kontak di tengah, judul bagian kapital bergaris penuh. Hitam-putih, aman untuk semua portal lowongan.'],
        ['simple', 'Simple ATS', 'Tanpa garis dan ornamen: judul bagian kapital tebal dengan jarak lega. Hitam-putih.'],
        ['formal', 'Formal ATS', 'Huruf serif, nama kapital di tengah, judul bagian diapit dua garis. Cocok untuk instansi dan bidang hukum.'],
        ['academic', 'Akademik ATS', 'Huruf serif dengan judul bagian di tengah, diapit dua garis. Cocok untuk pendidikan dan riset.'],
        ['crisp', 'Crisp ATS', 'Nama besar dan judul bagian yang diikuti garis tipis sampai tepi. Hitam-putih.'],
        ['nordic', 'Nordic ATS', 'Nama besar bergaya ringan, jarak lega, judul bagian kapital renggang tanpa garis.'],
        ['office', 'Office ATS', 'Huruf Calibri, garis tebal di bawah nama, judul bagian dalam kotak abu-abu muda.'],
        ['ledger', 'Ledger ATS', 'Rapat dan rapi: judul bagian bergaris, tanggal dicetak tebal. Muat banyak pengalaman.'],
        ['concise', 'Ringkas ATS', 'Huruf Calibri yang rapat dengan garis bawah tebal pada judul bagian. Pas untuk CV satu halaman.'],
        ['serif', 'Serif ATS', 'Huruf serif klasik, titik di depan judul bagian, dan keterangan bercetak miring.'],
        ['swiss', 'Swiss ATS', 'Nama besar dengan garis tebal di bawahnya, judul bagian diikuti garis tipis.'],
        ['outline', 'Garis Tepi ATS', 'Bingkai tipis di sekeliling halaman dan judul bagian bergaris. Hitam-putih.'],
        ['bar', 'Bar ATS', 'Batang hitam di kiri judul bagian dan poin berbentuk kotak kecil.'],
        ['centered', 'Center ATS', 'Kepala dan judul bagian rata tengah tanpa garis. Huruf Calibri.'],
        ['graphite', 'Grafit ATS', 'Kepala dalam kartu abu-abu muda dan judul bagian bergaris. Hitam-putih.'],
        ['sided', 'Dua Sisi ATS', 'Nama di kiri, kontak bersusun di kanan, judul bagian bergaris.'],
        ['tidy', 'Rapi ATS', 'Nama dan judul bagian berhuruf serif, isi sans-serif, poin berupa garis pendek.'],
        ['plain', 'Polos ATS', 'Sepolos mungkin: judul bagian tebal tanpa garis atau warna. Huruf Calibri.'],

        ['azure', 'Azure', 'Pita warna aksen di atas halaman dengan foto bercincin putih; judul bagian dengan batang warna.'],
        ['horizon', 'Horizon', 'Garis tebal warna aksen di tepi atas, nama berwarna, judul bagian diikuti garis.'],
        ['stripe', 'Garis Samping', 'Garis warna aksen di sepanjang tepi kiri halaman, nama kapital, judul bagian renggang.'],
        ['corner', 'Sudut', 'Dua segitiga di sudut kanan atas, foto bulat, dan tabel data diri.'],
        ['sunrise', 'Fajar', 'Kartu kepala berwarna lembut dengan foto bersudut bulat dan judul bagian berbentuk pil.'],
        ['ink', 'Tinta', 'Huruf serif dengan nama dan garis judul bagian berwarna aksen.'],
        ['vista', 'Vista', 'Lengkung lembut di atas, foto bercincin di tengah, judul bagian di antara dua garis.'],
        ['marker', 'Stabilo', 'Judul bagian disorot seperti stabilo, foto persegi, dan nama berwarna.'],
        ['emblem', 'Lencana', 'Foto bercincin di kanan dan judul bagian berbentuk label panah.'],
        ['pulse', 'Denyut', 'Pola titik di sudut, titik warna di depan judul bagian, tanggal dan poin berwarna.'],
        ['crest', 'Puncak', 'Bidang diagonal lembut di kiri atas, foto bulat di kanan, judul bagian renggang.'],
        ['harbor', 'Dermaga', 'Pita warna aksen dengan foto berbingkai putih dan tabel data diri; judul bagian dalam kotak lembut.'],
        ['bloom', 'Mekar', 'Lingkaran lembut di sudut halaman, foto bulat, judul bagian berbentuk pil lembut.'],
        ['spark', 'Percik', 'Nama kapital dengan garis tebal, judul bagian bernomor, poin kotak berwarna.'],
        ['linear', 'Linear', 'Garis warna aksen di tepi bawah, nama kapital, judul bagian diikuti garis.'],
        ['metro', 'Metro', 'Rapat, kontak bersusun di kanan nama, judul bagian berupa pita warna penuh.'],
        ['canvas', 'Kanvas', 'Bingkai warna aksen di sekeliling halaman, foto bulat, dan judul bagian rata tengah.'],
        ['aurora', 'Aurora', 'Pita warna aksen dengan kepala rata tengah dan foto bercincin; judul bagian bergaris bawah.'],
        ['sage', 'Sage', 'Huruf Calibri, kartu kepala lembut dengan foto bersudut bulat, judul bagian dengan batang.'],
        ['royal', 'Royal', 'Huruf serif, bingkai garis ganda, nama kapital berwarna di tengah.'],
        ['zenith', 'Zenit', 'Garis warna di tepi kanan, foto melengkung di kanan, sub-judul kapital renggang.'],
        ['ember', 'Bara', 'Garis warna di tepi atas dan bawah, foto bulat, judul bagian berbentuk pil.'],
        ['coral', 'Koral', 'Lengkung lembut di atas, foto bersudut bulat, nama kapital dan judul berbentuk pil lembut.'],
        ['tide', 'Pasang', 'Pita warna aksen di atas dan garis di bawah halaman; judul bagian diikuti garis.'],
        ['mint', 'Mint', 'Huruf Calibri, foto bulat di tengah, judul bagian dalam kotak lembut.'],
        ['ridge', 'Punggung', 'Nama dan judul bagian berhuruf serif, judul bernomor diikuti garis, foto persegi.'],

        ['harmony', 'Harmoni', 'Panel kiri berwarna lembut dengan foto bulat; judul bagian berbentuk pil lembut.'],
        ['summit', 'Summit', 'Panel kiri warna aksen pekat dengan foto bercincin; pendidikan di kolom samping.'],
        ['balance', 'Seimbang', 'Pita kepala warna aksen di atas, panel kanan bergaris tipis, foto persegi.'],
        ['ivory', 'Gading', 'Panel kiri abu-abu muda, huruf serif, foto bersudut bulat. Hitam-putih.'],
        ['duo', 'Duo', 'Kepala rata tengah dengan foto bulat, panel kanan berwarna lembut.'],
        ['cobalt', 'Kobalt', 'Panel kiri warna aksen pekat dengan foto melengkung dan nama kapital.'],
        ['terra', 'Terra', 'Panel kanan warna aksen pekat dengan foto bulat; judul bagian dengan batang.'],
        ['mosaic', 'Mozaik', 'Rapat, panel kiri lembut dengan foto persegi, judul bagian dalam kotak.'],
        ['crystal', 'Kristal', 'Panel kiri dipisah garis tipis, foto melengkung, judul bagian bergaris bawah.'],
        ['quartz', 'Kuarsa', 'Panel kanan abu-abu muda dengan foto bulat dan judul bagian berbatang. Hitam-putih.'],
        ['sierra', 'Sierra', 'Panel kiri arang dengan foto persegi; judul bagian diikuti garis. Hitam-putih.'],
        ['pixel', 'Piksel', 'Panel kiri lembut dengan foto bersudut bulat dan judul bagian bernomor.'],
        ['lotus', 'Teratai', 'Panel kanan lembut, nama berhuruf serif, foto bulat, titik di depan judul bagian.'],
        ['vertex', 'Verteks', 'Panel kiri warna aksen pekat, segitiga di sudut, judul bagian berbentuk label panah.'],
        ['echo', 'Gema', 'Kepala rata tengah dengan judul bagian di antara dua garis; panel kanan bergaris tipis. Hitam-putih.'],
    ];

    /**
     * Safe, proven picks for job applications: one column, real text, read
     * cleanly by applicant tracking systems. Shown first and labelled.
     *
     * @var list<string>
     */
    private array $recommended = ['professional', 'classic', 'modern', 'executive', 'compact', 'minimal', 'portrait', 'clean'];

    /**
     * Recommended first, then the other single-column templates, then the
     * two-column ones.
     *
     * @var list<string>
     */
    private array $order = [
        'professional', 'classic', 'modern', 'executive', 'compact', 'minimal', 'portrait', 'clean',
        'accent', 'banner', 'block', 'timeline', 'gutter', 'monogram', 'numbered', 'leader', 'bold', 'ribbon', 'corporate', 'tabbed', 'elegant',
        'simple', 'formal', 'academic', 'crisp', 'nordic', 'office', 'ledger', 'concise', 'serif', 'swiss', 'outline', 'bar', 'centered', 'graphite', 'sided', 'tidy', 'plain',
        'lumen', 'heritage', 'skyline', 'prestige', 'wave', 'geo', 'orbit', 'fresh', 'contrast', 'label',
        'azure', 'horizon', 'stripe', 'corner', 'sunrise', 'ink', 'vista', 'marker', 'emblem', 'pulse', 'crest', 'harbor', 'bloom',
        'spark', 'linear', 'metro', 'canvas', 'aurora', 'sage', 'royal', 'zenith', 'ember', 'coral', 'tide', 'mint', 'ridge',
        'sidebar', 'sidebar-light', 'two-column', 'split', 'magazine', 'folio', 'noir', 'slate', 'pastel', 'atelier', 'frame',
        'harmony', 'summit', 'balance', 'ivory', 'duo', 'cobalt', 'terra', 'mosaic', 'crystal', 'quartz', 'sierra', 'pixel', 'lotus', 'vertex', 'echo',
    ];

    public function up(): void
    {
        Schema::table('cv_templates', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false)->after('is_active');
        });

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

        DB::table('cv_templates')->whereIn('slug', $this->recommended)->update(['is_recommended' => true]);

        foreach ($this->order as $position => $slug) {
            DB::table('cv_templates')->where('slug', $slug)->update(['sort_order' => $position + 1]);
        }
    }

    public function down(): void
    {
        DB::table('cv_templates')->whereIn('slug', array_column($this->templates, 0))->delete();

        Schema::table('cv_templates', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });
    }
};
