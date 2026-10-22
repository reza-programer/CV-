<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF rendering
    |--------------------------------------------------------------------------
    |
    | PDFs are printed by headless Chrome (Puppeteer via spatie/browsershot)
    | from the same Vue layout components that power the live preview. The
    | print bundle is built by `npm run build` into bootstrap/cv-print.
    |
    | chrome_path: leave empty to use the browser Puppeteer downloads
    | (`npx puppeteer browsers install chrome`), or point it at an installed
    | Chrome/Chromium binary.
    |
    */

    'pdf' => [
        'node_binary' => env('CVFORGE_NODE_BINARY', 'node'),
        'chrome_path' => env('CVFORGE_CHROME_PATH'),
        'no_sandbox' => (bool) env('CVFORGE_CHROME_NO_SANDBOX', false),
        'timeout' => (int) env('CVFORGE_PDF_TIMEOUT', 60),
        'bundle_path' => base_path('bootstrap/cv-print'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI writing assistant
    |--------------------------------------------------------------------------
    |
    | Optional. When no API key is configured the assistant is hidden from
    | the editor entirely.
    |
    */

    'ai' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model' => env('CVFORGE_AI_MODEL', 'claude-opus-5-5'),
        // Short rewrites are quick work; raise to "medium" for more polish at higher latency.
        'effort' => env('CVFORGE_AI_EFFORT', 'low'),
        'base_url' => env('ANTHROPIC_BASE_URL'),
        'timeout' => (int) env('CVFORGE_AI_TIMEOUT', 60),
    ],

];
