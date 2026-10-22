<?php

namespace App\Http\Controllers;

use App\Models\CvTemplate;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'templates' => CvTemplate::query()->active()->get()->map->toPublicArray(),
        ]);
    }
}
