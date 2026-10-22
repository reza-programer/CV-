<?php

namespace App\Http\Controllers\Resumes;

use App\Actions\Resumes\CreateResume;
use App\Actions\Resumes\SaveResumeContent;
use App\Enums\ResumeLocale;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resumes\StoreResumeRequest;
use App\Http\Requests\Resumes\UpdateResumeRequest;
use App\Models\CvTemplate;
use App\Models\Resume;
use App\Services\ResumeAssistant;
use App\Support\Resumes\ResumePayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function index(Request $request): Response
    {
        // Full documents are sent so each card can render a real first-page thumbnail.
        $resumes = $request->user()->resumes()
            ->with(Resume::contentRelations())
            ->latest('updated_at')
            ->get()
            ->map(fn (Resume $resume) => [
                'id' => $resume->id,
                'title' => $resume->title,
                'template_name' => $resume->template->name,
                'updated_at' => $resume->updated_at?->toIso8601String(),
                'document' => ResumePayload::make($resume, ResumePhotoController::urlFor($resume)),
            ]);

        return Inertia::render('resumes/Index', [
            'resumes' => $resumes,
            'templates' => $this->templates(),
        ]);
    }

    public function store(StoreResumeRequest $request, CreateResume $createResume): RedirectResponse
    {
        $resume = $createResume->handle(
            $request->user(),
            $request->string('title')->trim()->value() ?: 'CV Baru',
            CvTemplate::query()->where('slug', $request->string('template'))->firstOrFail(),
            ResumeLocale::from($request->string('locale')->value()),
        );

        return to_route('resumes.edit', $resume);
    }

    public function edit(Resume $resume, ResumeAssistant $assistant): Response
    {
        return Inertia::render('resumes/Edit', [
            'resume' => ResumePayload::make($resume, ResumePhotoController::urlFor($resume)),
            'templates' => $this->templates(),
            'savedAt' => $resume->updated_at?->toIso8601String(),
            'assistantEnabled' => $assistant->enabled(),
        ]);
    }

    public function update(UpdateResumeRequest $request, Resume $resume, SaveResumeContent $saveContent): JsonResponse
    {
        $resume = $saveContent->handle($resume, $request->validated());

        return response()->json([
            'saved_at' => $resume->updated_at?->toIso8601String(),
        ]);
    }

    public function destroy(Resume $resume): RedirectResponse
    {
        $resume->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'CV dihapus.']);

        return to_route('dashboard');
    }

    /**
     * @return list<array{slug: string, name: string, description: string|null}>
     */
    private function templates(): array
    {
        return CvTemplate::query()->active()->get()->map->toPublicArray()->values()->all();
    }
}
