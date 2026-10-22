<?php

namespace App\Http\Requests\Resumes;

use App\Enums\ExperienceType;
use App\Enums\ResumeLocale;
use App\Enums\ResumeSection;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Rules\PartialDate;
use App\Rules\WebAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateResumeRequest extends FormRequest
{
    /**
     * Upper bounds keep a single entry short enough to fit on one A4 page and
     * stop a single request from writing unbounded rows.
     */
    public const MAX_ITEMS = 30;

    public const MAX_HIGHLIGHTS = 12;

    public const MAX_HIGHLIGHT_LENGTH = 600;

    public const MAX_SUMMARY_LENGTH = 2000;

    /**
     * Repeatable sections in the payload.
     */
    private const LISTS = ['experiences', 'activities', 'educations', 'projects', 'skill_groups', 'certifications', 'languages'];

    /**
     * Date pairs checked for order after the field rules pass.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const DATE_RANGES = [
        'experiences' => ['start_date', 'end_date'],
        'activities' => ['start_date', 'end_date'],
        'educations' => ['start_date', 'end_date'],
        'projects' => ['start_date', 'end_date'],
        'certifications' => ['issued_at', 'expires_at'],
    ];

    public function authorize(): bool
    {
        $resume = $this->route('resume');

        return $resume instanceof Resume && $this->user()?->can('update', $resume);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $text = fn (int $max) => ['nullable', 'string', "max:{$max}"];
        $date = ['nullable', 'string', new PartialDate];
        $highlights = ['array', 'max:'.self::MAX_HIGHLIGHTS];
        $highlight = ['nullable', 'string', 'max:'.self::MAX_HIGHLIGHT_LENGTH];
        $list = ['present', 'array', 'max:'.self::MAX_ITEMS];

        $experience = fn (string $key) => [
            $key => $list,
            "{$key}.*.position" => $text(150),
            "{$key}.*.organization" => $text(150),
            "{$key}.*.location" => $text(120),
            "{$key}.*.start_date" => $date,
            "{$key}.*.end_date" => $date,
            "{$key}.*.is_current" => ['boolean'],
            "{$key}.*.highlights" => $highlights,
            "{$key}.*.highlights.*" => $highlight,
        ];

        return [
            'title' => ['required', 'string', 'max:120'],
            'template' => ['required', 'string', Rule::in($this->allowedTemplates())],
            'locale' => ['required', Rule::enum(ResumeLocale::class)],
            'section_order' => ['required', 'array', 'max:'.count(ResumeSection::cases())],
            'section_order.*' => ['string', 'distinct', Rule::enum(ResumeSection::class)],

            'personal' => ['required', 'array'],
            'personal.full_name' => $text(120),
            'personal.headline' => $text(150),
            'personal.email' => ['nullable', 'string', 'email', 'max:150'],
            'personal.phone' => ['nullable', 'string', 'max:40', 'regex:/^\+?[0-9][0-9\s().-]{5,38}$/'],
            'personal.location' => $text(120),
            'personal.website' => ['nullable', 'string', 'max:255', new WebAddress],
            'personal.linkedin' => ['nullable', 'string', 'max:255', new WebAddress],
            'summary' => $text(self::MAX_SUMMARY_LENGTH),

            ...$experience('experiences'),
            ...$experience('activities'),
            'activities.*.type' => ['required', Rule::in(ExperienceType::activityValues())],

            'educations' => $list,
            'educations.*.institution' => $text(150),
            'educations.*.degree' => $text(150),
            'educations.*.field_of_study' => $text(150),
            'educations.*.location' => $text(120),
            'educations.*.start_date' => $date,
            'educations.*.end_date' => $date,
            'educations.*.is_current' => ['boolean'],
            'educations.*.gpa' => $text(20),
            'educations.*.highlights' => $highlights,
            'educations.*.highlights.*' => $highlight,

            'projects' => $list,
            'projects.*.name' => $text(150),
            'projects.*.role' => $text(150),
            'projects.*.url' => ['nullable', 'string', 'max:255', new WebAddress],
            'projects.*.start_date' => $date,
            'projects.*.end_date' => $date,
            'projects.*.is_current' => ['boolean'],
            'projects.*.highlights' => $highlights,
            'projects.*.highlights.*' => $highlight,

            'skill_groups' => $list,
            'skill_groups.*.category' => $text(80),
            'skill_groups.*.skills' => $text(500),

            'certifications' => $list,
            'certifications.*.name' => $text(150),
            'certifications.*.issuer' => $text(150),
            'certifications.*.issued_at' => $date,
            'certifications.*.expires_at' => $date,
            'certifications.*.credential_id' => $text(100),
            'certifications.*.url' => ['nullable', 'string', 'max:255', new WebAddress],

            'languages' => $list,
            'languages.*.name' => $text(80),
            'languages.*.proficiency' => $text(80),
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                foreach (self::DATE_RANGES as $list => [$startKey, $endKey]) {
                    foreach ((array) $this->input($list, []) as $index => $item) {
                        if (! is_array($item) || ! empty($item['is_current'])) {
                            continue;
                        }

                        $start = $item[$startKey] ?? null;
                        $end = $item[$endKey] ?? null;

                        if ($this->isPartialDate($start) && $this->isPartialDate($end) && ! PartialDate::isBefore($start, $end)) {
                            $validator->errors()->add(
                                "{$list}.{$index}.{$endKey}",
                                'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
                            );
                        }
                    }
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'personal.phone.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda +, -, titik, dan tanda kurung.',
            'template.in' => 'Template yang dipilih tidak tersedia.',
        ];

        foreach (self::LISTS as $list) {
            $messages["{$list}.max"] = 'Maksimal :max entri pada bagian ini.';
            $messages["{$list}.*.highlights.max"] = 'Maksimal :max poin per entri.';
        }

        return $messages;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $fields = [
            'position' => 'posisi',
            'organization' => 'nama perusahaan atau organisasi',
            'location' => 'lokasi',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'highlights.*' => 'poin',
            'institution' => 'institusi',
            'degree' => 'jenjang atau gelar',
            'field_of_study' => 'jurusan',
            'gpa' => 'IPK',
            'name' => 'nama',
            'role' => 'peran',
            'url' => 'tautan',
            'category' => 'kategori',
            'skills' => 'daftar keahlian',
            'issuer' => 'penerbit',
            'issued_at' => 'tanggal terbit',
            'expires_at' => 'tanggal kedaluwarsa',
            'credential_id' => 'ID kredensial',
            'proficiency' => 'tingkat kemahiran',
            'type' => 'jenis kegiatan',
        ];

        $attributes = [
            'title' => 'judul CV',
            'locale' => 'bahasa dokumen',
            'personal.full_name' => 'nama lengkap',
            'personal.headline' => 'jabatan atau headline',
            'personal.email' => 'email',
            'personal.phone' => 'nomor telepon',
            'personal.location' => 'domisili',
            'personal.website' => 'website',
            'personal.linkedin' => 'LinkedIn',
            'summary' => 'profil singkat',
        ];

        foreach (self::LISTS as $list) {
            foreach ($fields as $field => $label) {
                $attributes["{$list}.*.{$field}"] = $label;
            }
        }

        return $attributes;
    }

    /**
     * Active templates plus the one already assigned, so deactivating a
     * template in the admin panel never blocks saving an existing CV.
     *
     * @return list<string>
     */
    private function allowedTemplates(): array
    {
        $resume = $this->route('resume');
        $active = CvTemplate::query()->active()->pluck('slug')->all();

        if ($resume instanceof Resume && $resume->template !== null) {
            $active[] = $resume->template->slug;
        }

        return array_values(array_unique($active));
    }

    private function isPartialDate(mixed $value): bool
    {
        return is_string($value) && preg_match('/^\d{4}(-(0[1-9]|1[0-2]))?$/', $value) === 1;
    }
}
