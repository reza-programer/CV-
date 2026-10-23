<?php

namespace App\Actions\Resumes;

use App\Enums\ExperienceType;
use App\Enums\ResumeSection;
use App\Models\CvTemplate;
use App\Models\Resume;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Persists a validated editor payload. The editor always sends the complete
 * document, so child rows are replaced wholesale inside one transaction; their
 * ids carry no meaning outside the parent CV.
 */
class SaveResumeContent
{
    /**
     * @param  array<string, mixed>  $data  Output of UpdateResumeRequest::validated()
     */
    public function handle(Resume $resume, array $data): Resume
    {
        DB::transaction(function () use ($resume, $data): void {
            $personal = $data['personal'] ?? [];

            $resume->fill([
                'title' => $data['title'],
                'locale' => $data['locale'],
                'paragraph_align' => $data['paragraph_align'],
                'accent_color' => $data['accent_color'],
                'cv_template_id' => CvTemplate::query()->where('slug', $data['template'])->value('id') ?? $resume->cv_template_id,
                'section_order' => ResumeSection::normalizeOrder($data['section_order'] ?? null),
                'full_name' => $personal['full_name'] ?? null,
                'headline' => $personal['headline'] ?? null,
                'email' => $personal['email'] ?? null,
                'phone' => $personal['phone'] ?? null,
                'location' => $personal['location'] ?? null,
                'website' => $personal['website'] ?? null,
                'linkedin' => $personal['linkedin'] ?? null,
                'summary' => $data['summary'] ?? null,
            ]);

            // Child rows may change while the parent row stays identical, so
            // always bump updated_at for the dashboard's "last edited" column.
            $resume->updated_at = $resume->freshTimestamp();
            $resume->save();

            // Work entries and activities share one table, so clear it once.
            $resume->allExperiences()->delete();
            $this->insert($resume->allExperiences(), $data['experiences'], fn (array $item) => [
                ...$this->experience($item),
                'type' => ExperienceType::Work,
            ]);
            $this->insert($resume->allExperiences(), $data['activities'], fn (array $item) => [
                ...$this->experience($item),
                'type' => ExperienceType::from($item['type']),
            ]);

            $this->replace($resume->educations(), $data['educations'], fn (array $item) => [
                'institution' => $item['institution'] ?? null,
                'degree' => $item['degree'] ?? null,
                'field_of_study' => $item['field_of_study'] ?? null,
                'location' => $item['location'] ?? null,
                ...$this->period($item),
                'gpa' => $item['gpa'] ?? null,
                'highlights' => $this->highlights($item),
            ]);

            $this->replace($resume->projects(), $data['projects'], fn (array $item) => [
                'name' => $item['name'] ?? null,
                'role' => $item['role'] ?? null,
                'url' => $item['url'] ?? null,
                ...$this->period($item),
                'highlights' => $this->highlights($item),
            ]);

            $this->replace($resume->skillGroups(), $data['skill_groups'], fn (array $item) => [
                'category' => $item['category'] ?? null,
                'skills' => $item['skills'] ?? null,
            ]);

            $this->replace($resume->certifications(), $data['certifications'], fn (array $item) => [
                'name' => $item['name'] ?? null,
                'issuer' => $item['issuer'] ?? null,
                'issued_at' => $item['issued_at'] ?? null,
                'expires_at' => $item['expires_at'] ?? null,
                'credential_id' => $item['credential_id'] ?? null,
                'url' => $item['url'] ?? null,
            ]);

            $this->replace($resume->languages(), $data['languages'], fn (array $item) => [
                'name' => $item['name'] ?? null,
                'proficiency' => $item['proficiency'] ?? null,
            ]);
        });

        return $resume->unsetRelations();
    }

    /**
     * @param  HasMany<*, Resume>  $relation
     * @param  array<int, array<string, mixed>>|null  $items
     * @param  callable(array<string, mixed>): array<string, mixed>  $map
     */
    private function replace(HasMany $relation, ?array $items, callable $map): void
    {
        $relation->delete();
        $this->insert($relation, $items, $map);
    }

    /**
     * @param  HasMany<*, Resume>  $relation
     * @param  array<int, array<string, mixed>>|null  $items
     * @param  callable(array<string, mixed>): array<string, mixed>  $map
     */
    private function insert(HasMany $relation, ?array $items, callable $map): void
    {
        foreach (array_values($items ?? []) as $index => $item) {
            $relation->create([...$map($item), 'sort_order' => $index]);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function experience(array $item): array
    {
        return [
            'position' => $item['position'] ?? null,
            'organization' => $item['organization'] ?? null,
            'location' => $item['location'] ?? null,
            ...$this->period($item),
            'highlights' => $this->highlights($item),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{start_date: mixed, end_date: mixed, is_current: bool}
     */
    private function period(array $item): array
    {
        $isCurrent = (bool) ($item['is_current'] ?? false);

        return [
            'start_date' => $item['start_date'] ?? null,
            'end_date' => $isCurrent ? null : ($item['end_date'] ?? null),
            'is_current' => $isCurrent,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return list<string>
     */
    private function highlights(array $item): array
    {
        return array_values(array_filter(
            $item['highlights'] ?? [],
            fn ($line) => is_string($line) && trim($line) !== '',
        ));
    }
}
