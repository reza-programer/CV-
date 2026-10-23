<?php

namespace Tests\Unit;

use App\Support\Resumes\TranslatableFields;
use PHPUnit\Framework\TestCase;

class TranslatableFieldsTest extends TestCase
{
    public function test_free_text_is_extracted_by_path_in_document_order(): void
    {
        $this->assertSame([
            'personal.headline' => 'Pengembang Backend',
            'summary' => 'Pengembang backend dengan lima tahun pengalaman.',
            'experiences.0.position' => 'Pengembang Backend',
            'experiences.0.highlights.0' => 'Membangun API pelacakan paket.',
            'experiences.0.highlights.1' => 'Menulis pengujian otomatis.',
            'activities.0.position' => 'Ketua Divisi Acara',
            'educations.0.degree' => 'Sarjana Komputer',
            'educations.0.field_of_study' => 'Informatika',
            'educations.0.highlights.0' => 'Lulus dengan predikat cum laude.',
            'projects.0.role' => 'Pengembang utama',
            'projects.0.highlights.0' => 'Merancang skema basis data.',
            'skill_groups.0.category' => 'Pengembangan backend',
            'skill_groups.0.skills' => 'PHP, Laravel, MySQL, kerja tim',
            'languages.0.name' => 'Inggris',
            'languages.0.proficiency' => 'Profesional',
        ], TranslatableFields::extract($this->payload()));
    }

    public function test_proper_nouns_links_dates_and_grades_are_never_extracted(): void
    {
        $fields = TranslatableFields::extract($this->payload());

        foreach ([
            'title', 'personal.full_name', 'personal.email', 'personal.phone', 'personal.location',
            'personal.website', 'personal.linkedin', 'experiences.0.organization', 'experiences.0.location',
            'experiences.0.start_date', 'activities.0.organization', 'educations.0.institution',
            'educations.0.location', 'educations.0.gpa', 'projects.0.name', 'projects.0.url',
            'certifications.0.name', 'certifications.0.issuer', 'certifications.0.credential_id',
        ] as $path) {
            $this->assertArrayNotHasKey($path, $fields);
        }

        foreach (['Dimas Prasetyo', 'PT Kirim Cepat', 'Surabaya', 'Institut Teknologi Sepuluh Nopember', 'Lacak Paket', 'AWS Certified Developer', '3,61/4,00', '2021-03'] as $value) {
            $this->assertNotContains($value, $fields);
        }
    }

    public function test_empty_and_blank_strings_are_skipped(): void
    {
        $payload = $this->payload();
        $payload['personal']['headline'] = '';
        $payload['summary'] = "  \n ";
        $payload['experiences'][0]['position'] = '';
        $payload['experiences'][0]['highlights'] = ['', 'Menulis pengujian otomatis.', ' '];
        $payload['skill_groups'][0]['category'] = '';
        $payload['languages'] = [];

        $fields = TranslatableFields::extract($payload);

        $this->assertArrayNotHasKey('personal.headline', $fields);
        $this->assertArrayNotHasKey('summary', $fields);
        $this->assertArrayNotHasKey('experiences.0.position', $fields);
        $this->assertArrayNotHasKey('experiences.0.highlights.0', $fields);
        $this->assertArrayNotHasKey('experiences.0.highlights.2', $fields);
        $this->assertSame('Menulis pengujian otomatis.', $fields['experiences.0.highlights.1']);
        $this->assertArrayNotHasKey('skill_groups.0.category', $fields);
        $this->assertSame('PHP, Laravel, MySQL, kerja tim', $fields['skill_groups.0.skills']);
        $this->assertArrayNotHasKey('languages.0.name', $fields);

        $this->assertSame([], TranslatableFields::extract([]));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'id' => 1,
            'title' => 'CV Backend',
            'template' => 'classic',
            'locale' => 'id',
            'personal' => [
                'full_name' => 'Dimas Prasetyo',
                'headline' => 'Pengembang Backend',
                'email' => 'dimas@example.com',
                'phone' => '+62 811 2233 4455',
                'location' => 'Surabaya',
                'website' => 'github.com/dimas',
                'linkedin' => 'linkedin.com/in/dimas',
            ],
            'photo_url' => null,
            'summary' => 'Pengembang backend dengan lima tahun pengalaman.',
            'experiences' => [[
                'position' => 'Pengembang Backend',
                'organization' => 'PT Kirim Cepat',
                'location' => 'Surabaya',
                'start_date' => '2021-03',
                'end_date' => '',
                'is_current' => true,
                'highlights' => ['Membangun API pelacakan paket.', 'Menulis pengujian otomatis.'],
            ]],
            'activities' => [[
                'type' => 'organization',
                'position' => 'Ketua Divisi Acara',
                'organization' => 'Himpunan Mahasiswa Informatika',
                'location' => '',
                'start_date' => '2017',
                'end_date' => '2018',
                'is_current' => false,
                'highlights' => [],
            ]],
            'educations' => [[
                'institution' => 'Institut Teknologi Sepuluh Nopember',
                'degree' => 'Sarjana Komputer',
                'field_of_study' => 'Informatika',
                'location' => 'Surabaya',
                'start_date' => '2015',
                'end_date' => '2019',
                'is_current' => false,
                'gpa' => '3,61/4,00',
                'highlights' => ['Lulus dengan predikat cum laude.'],
            ]],
            'projects' => [[
                'name' => 'Lacak Paket',
                'role' => 'Pengembang utama',
                'url' => 'github.com/dimas/lacak-paket',
                'start_date' => '2022',
                'end_date' => '2023',
                'is_current' => false,
                'highlights' => ['Merancang skema basis data.'],
            ]],
            'skill_groups' => [['category' => 'Pengembangan backend', 'skills' => 'PHP, Laravel, MySQL, kerja tim']],
            'certifications' => [[
                'name' => 'AWS Certified Developer',
                'issuer' => 'Amazon Web Services',
                'issued_at' => '2023-05',
                'expires_at' => '',
                'credential_id' => 'ABC-123',
                'url' => 'aws.amazon.com/verification',
            ]],
            'languages' => [['name' => 'Inggris', 'proficiency' => 'Profesional']],
        ];
    }
}
