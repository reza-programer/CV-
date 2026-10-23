<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Things a user does that the admin dashboard shows.
 */
enum ActivityEvent: string implements HasLabel
{
    case Registered = 'registered';
    case LoggedIn = 'logged_in';
    case ResumeCreated = 'resume_created';
    case ResumeEdited = 'resume_edited';
    case ResumeDuplicated = 'resume_duplicated';
    case ResumeDeleted = 'resume_deleted';
    case PdfDownloaded = 'pdf_downloaded';
    case PhotoUploaded = 'photo_uploaded';
    case AssistantUsed = 'assistant_used';
    case Translated = 'translated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Registered => 'Mendaftar',
            self::LoggedIn => 'Masuk',
            self::ResumeCreated => 'Membuat CV',
            self::ResumeEdited => 'Menyunting CV',
            self::ResumeDuplicated => 'Menduplikat CV',
            self::ResumeDeleted => 'Menghapus CV',
            self::PdfDownloaded => 'Mengunduh PDF',
            self::PhotoUploaded => 'Mengunggah foto',
            self::AssistantUsed => 'Memakai asisten AI',
            self::Translated => 'Menerjemahkan CV',
        };
    }
}
