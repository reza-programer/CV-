<?php

namespace App\Http\Controllers;

use App\Models\CvTemplate;
use App\Services\ResumeTranslator;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(ResumeTranslator $translator): Response
    {
        return Inertia::render('Welcome', [
            'templates' => CvTemplate::query()->active()->get()->map->toPublicArray(),
            // The landing page only mentions AI translation when it is available.
            'translationEnabled' => $translator->enabled(),
        ]);
    }
}
