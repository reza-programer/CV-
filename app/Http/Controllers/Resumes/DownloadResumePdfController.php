<?php

namespace App\Http\Controllers\Resumes;

use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Resume;
use App\Services\ResumePdfRenderer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class DownloadResumePdfController extends Controller
{
    public function __invoke(Request $request, Resume $resume, ResumePdfRenderer $renderer): Response
    {
        try {
            $pdf = $renderer->render($resume);
        } catch (Throwable $exception) {
            Log::error('CV PDF rendering failed.', ['resume_id' => $resume->id, 'exception' => $exception]);

            throw new HttpException(503, 'PDF belum dapat dibuat. Silakan coba lagi beberapa saat lagi.', $exception);
        }

        ActivityLog::record($request->user(), ActivityEvent::PdfDownloaded, $resume, ['locale' => $resume->locale->value]);

        $filename = $renderer->filename($resume);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'attachment; filename="%s"; filename*=UTF-8\'\'%s',
                preg_replace('/[^\x20-\x7E]/', '', str_replace('"', '', $filename)),
                rawurlencode($filename),
            ),
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
