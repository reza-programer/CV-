<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alignment of running text (profile and bullet points): "left" or
     * "justify", like the paragraph alignment buttons in a word processor.
     */
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('paragraph_align', 10)->default('left')->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn('paragraph_align');
        });
    }
};
