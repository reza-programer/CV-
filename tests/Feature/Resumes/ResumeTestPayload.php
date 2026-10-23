<?php

namespace Tests\Feature\Resumes;

/**
 * A complete, valid editor payload for PUT /resumes/{resume}.
 */
trait ResumeTestPayload
{
    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'title' => 'CV Backend',
            'template' => 'classic',
            'locale' => 'id',
            'paragraph_align' => 'left',
            'accent_color' => 'navy',
            'section_order' => ['summary', 'experience', 'internship', 'organization', 'education', 'projects', 'skills', 'certifications', 'languages'],
            'personal' => [
                'full_name' => 'Dimas Prasetyo',
                'headline' => 'Backend Engineer',
                'email' => 'dimas@example.com',
                'phone' => '+62 811 2233 4455',
                'location' => 'Surabaya',
                'website' => 'github.com/dimas',
                'linkedin' => 'https://www.linkedin.com/in/dimas',
            ],
            'summary' => 'Backend engineer dengan lima tahun pengalaman.',
            'experiences' => [
                [
                    'position' => 'Backend Engineer',
                    'organization' => 'PT Kirim Cepat',
                    'location' => 'Surabaya',
                    'start_date' => '2021-03',
                    'end_date' => '2024-01',
                    'is_current' => false,
                    'highlights' => ['Membangun API pelacakan paket.', '', 'Menulis pengujian otomatis.'],
                ],
            ],
            'activities' => [
                [
                    'type' => 'organization',
                    'position' => 'Ketua Divisi Acara',
                    'organization' => 'Himpunan Mahasiswa',
                    'location' => '',
                    'start_date' => '2017',
                    'end_date' => '2018',
                    'is_current' => false,
                    'highlights' => [],
                ],
            ],
            'educations' => [
                [
                    'institution' => 'Institut Teknologi Sepuluh Nopember',
                    'degree' => 'S1',
                    'field_of_study' => 'Informatika',
                    'location' => 'Surabaya',
                    'start_date' => '2015',
                    'end_date' => '2019',
                    'is_current' => false,
                    'gpa' => '3,61/4,00',
                    'highlights' => [],
                ],
            ],
            'projects' => [],
            'skill_groups' => [['category' => 'Backend', 'skills' => 'PHP, Laravel, MySQL']],
            'certifications' => [],
            'languages' => [['name' => 'Inggris', 'proficiency' => 'Profesional']],
        ], $overrides);
    }
}
