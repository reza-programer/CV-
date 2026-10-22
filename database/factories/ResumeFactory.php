<?php

namespace Database\Factories;

use App\Enums\ExperienceType;
use App\Enums\ResumeLocale;
use App\Enums\ResumeSection;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Test data only: the application never puts generated content in a user's CV.
 *
 * @extends Factory<Resume>
 */
class ResumeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cv_template_id' => fn () => CvTemplate::query()->where('slug', CvTemplate::DEFAULT_SLUG)->value('id'),
            'title' => 'CV '.fake()->jobTitle(),
            'locale' => ResumeLocale::Indonesian,
            'section_order' => ResumeSection::defaultOrder(),
            'full_name' => fake()->name(),
            'headline' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'phone' => '+62 812 '.fake()->numerify('#### ####'),
            'location' => fake()->city().', Indonesia',
            'linkedin' => 'linkedin.com/in/'.fake()->userName(),
            'summary' => fake()->paragraph(4),
        ];
    }

    public function template(string $slug): static
    {
        return $this->state(fn () => [
            'cv_template_id' => CvTemplate::query()->where('slug', $slug)->value('id'),
        ]);
    }

    /**
     * Fill every section; `$scale` multiplies the number of entries to produce
     * multi-page documents.
     */
    public function withContent(int $scale = 1): static
    {
        return $this->afterCreating(function (Resume $resume) use ($scale): void {
            $bullets = fn (int $count) => array_map(fn () => fake()->sentence(fake()->numberBetween(12, 26)), range(1, $count));

            foreach (range(0, 3 * $scale - 1) as $index) {
                $resume->allExperiences()->create([
                    'type' => ExperienceType::Work,
                    'position' => fake()->jobTitle(),
                    'organization' => fake()->company(),
                    'location' => fake()->city(),
                    'start_date' => (2022 - 2 * $index).'-0'.fake()->numberBetween(1, 9),
                    'end_date' => $index === 0 ? null : (2023 - 2 * $index).'-1'.fake()->numberBetween(0, 2),
                    'is_current' => $index === 0,
                    'highlights' => $bullets(fake()->numberBetween(3, 5)),
                    'sort_order' => $index,
                ]);
            }

            foreach (range(0, $scale) as $index) {
                $resume->allExperiences()->create([
                    'type' => $index % 2 === 0 ? ExperienceType::Internship : ExperienceType::Organization,
                    'position' => fake()->jobTitle(),
                    'organization' => fake()->company(),
                    'location' => fake()->city(),
                    'start_date' => '2015-0'.($index + 1),
                    'end_date' => '2015-1'.($index % 3),
                    'highlights' => $bullets(2),
                    'sort_order' => $index,
                ]);
            }

            $resume->educations()->create([
                'institution' => 'Universitas '.fake()->city(),
                'degree' => 'S1',
                'field_of_study' => 'Teknik Informatika',
                'location' => fake()->city(),
                'start_date' => '2012',
                'end_date' => '2016',
                'gpa' => '3,72/4,00',
                'highlights' => [fake()->sentence(14)],
            ]);

            foreach (range(0, $scale) as $index) {
                $resume->projects()->create([
                    'name' => ucfirst(fake()->words(3, true)),
                    'role' => fake()->jobTitle(),
                    'url' => 'github.com/'.fake()->userName(),
                    'start_date' => '2023-02',
                    'end_date' => '2023-08',
                    'highlights' => $bullets(2),
                    'sort_order' => $index,
                ]);
            }

            $resume->skillGroups()->createMany([
                ['category' => 'Bahasa Pemrograman', 'skills' => 'PHP, TypeScript, Go, SQL', 'sort_order' => 0],
                ['category' => 'Framework', 'skills' => 'Laravel, Vue.js, Tailwind CSS, Filament', 'sort_order' => 1],
                ['category' => 'Tools', 'skills' => 'Git, Docker, MySQL, Redis, GitHub Actions', 'sort_order' => 2],
            ]);

            $resume->certifications()->create([
                'name' => 'AWS Certified Cloud Practitioner',
                'issuer' => 'Amazon Web Services',
                'issued_at' => '2024-03',
                'credential_id' => strtoupper(fake()->bothify('??##??##')),
            ]);

            $resume->languages()->createMany([
                ['name' => 'Indonesia', 'proficiency' => 'Penutur asli', 'sort_order' => 0],
                ['name' => 'Inggris', 'proficiency' => 'Profesional', 'sort_order' => 1],
            ]);
        });
    }
}
