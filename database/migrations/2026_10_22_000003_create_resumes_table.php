<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cv_template_id')->constrained()->restrictOnDelete();
            $table->string('title', 120);
            $table->char('locale', 2)->default('id');

            $table->string('full_name', 120)->nullable();
            $table->string('headline', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->text('summary')->nullable();
            $table->string('photo_path')->nullable();

            $table->json('section_order');
            $table->timestamps();

            $table->index(['user_id', 'updated_at']);
        });

        Schema::create('resume_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->index();
            $table->string('position', 150)->nullable();
            $table->string('organization', 150)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('start_date', 7)->nullable();
            $table->string('end_date', 7)->nullable();
            $table->boolean('is_current')->default(false);
            $table->json('highlights')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('resume_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('institution', 150)->nullable();
            $table->string('degree', 150)->nullable();
            $table->string('field_of_study', 150)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('start_date', 7)->nullable();
            $table->string('end_date', 7)->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('gpa', 20)->nullable();
            $table->json('highlights')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('resume_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150)->nullable();
            $table->string('role', 150)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('start_date', 7)->nullable();
            $table->string('end_date', 7)->nullable();
            $table->boolean('is_current')->default(false);
            $table->json('highlights')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('resume_skill_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('category', 80)->nullable();
            $table->string('skills', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('resume_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150)->nullable();
            $table->string('issuer', 150)->nullable();
            $table->string('issued_at', 7)->nullable();
            $table->string('expires_at', 7)->nullable();
            $table->string('credential_id', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('resume_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80)->nullable();
            $table->string('proficiency', 80)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_languages');
        Schema::dropIfExists('resume_certifications');
        Schema::dropIfExists('resume_skill_groups');
        Schema::dropIfExists('resume_projects');
        Schema::dropIfExists('resume_educations');
        Schema::dropIfExists('resume_experiences');
        Schema::dropIfExists('resumes');
    }
};
