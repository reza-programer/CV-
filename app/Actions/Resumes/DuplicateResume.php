<?php

namespace App\Actions\Resumes;

use App\Models\Resume;
use App\Services\ResumePhotos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DuplicateResume
{
    private const CHILD_RELATIONS = ['allExperiences', 'educations', 'projects', 'skillGroups', 'certifications', 'languages'];

    public function __construct(private ResumePhotos $photos) {}

    public function handle(Resume $resume): Resume
    {
        $resume->load(self::CHILD_RELATIONS);

        return DB::transaction(function () use ($resume): Resume {
            $copy = $resume->replicate(['photo_path']);
            $copy->title = Str::limit('Salinan '.$resume->title, 120, '');
            $copy->save();

            foreach (self::CHILD_RELATIONS as $relation) {
                $resume->{$relation}->each(
                    fn (Model $item) => $copy->{$relation}()->save($item->replicate()),
                );
            }

            if ($resume->photo_path !== null) {
                $copy->update(['photo_path' => $this->photos->copy($resume->photo_path, $copy)]);
            }

            return $copy;
        });
    }
}
