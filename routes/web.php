<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Resumes\DownloadResumePdfController;
use App\Http\Controllers\Resumes\DuplicateResumeController;
use App\Http\Controllers\Resumes\ResumeAssistController;
use App\Http\Controllers\Resumes\ResumeController;
use App\Http\Controllers\Resumes\ResumePhotoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [ResumeController::class, 'index'])->name('dashboard');
    Route::post('resumes', [ResumeController::class, 'store'])->name('resumes.store');

    Route::get('resumes/{resume}/edit', [ResumeController::class, 'edit'])->can('view', 'resume')->name('resumes.edit');
    Route::put('resumes/{resume}', [ResumeController::class, 'update'])->can('update', 'resume')->name('resumes.update');
    Route::delete('resumes/{resume}', [ResumeController::class, 'destroy'])->can('delete', 'resume')->name('resumes.destroy');

    Route::post('resumes/{resume}/duplicate', DuplicateResumeController::class)->can('view', 'resume')->name('resumes.duplicate');

    Route::get('resumes/{resume}/pdf', DownloadResumePdfController::class)
        ->can('view', 'resume')
        ->middleware('throttle:pdf')
        ->name('resumes.pdf');

    Route::get('resumes/{resume}/photo', [ResumePhotoController::class, 'show'])->can('view', 'resume')->name('resumes.photo.show');
    Route::post('resumes/{resume}/photo', [ResumePhotoController::class, 'store'])->can('update', 'resume')->name('resumes.photo.store');
    Route::delete('resumes/{resume}/photo', [ResumePhotoController::class, 'destroy'])->can('update', 'resume')->name('resumes.photo.destroy');

    Route::post('resumes/{resume}/assist', ResumeAssistController::class)
        ->can('update', 'resume')
        ->middleware('throttle:assistant')
        ->name('resumes.assist');
});

require __DIR__.'/settings.php';
