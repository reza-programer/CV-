<?php

namespace App\Http\Requests\Resumes;

use App\Enums\ResumeLocale;
use App\Models\CvTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResumeRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:120'],
            'template' => ['required', 'string', Rule::exists(CvTemplate::class, 'slug')->where('is_active', true)],
            'locale' => ['required', Rule::enum(ResumeLocale::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul CV',
            'locale' => 'bahasa dokumen',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'template.exists' => 'Template yang dipilih tidak tersedia.',
        ];
    }
}
