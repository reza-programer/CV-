<?php

namespace App\Services;

use App\Models\Resume;
use App\Support\Resumes\ResumePayload;
use RuntimeException;
use Spatie\Browsershot\Browsershot;

/**
 * Prints a CV to A4 PDF with headless Chrome.
 *
 * The page is built from the print bundle (resources/js/cv/print.ts), which
 * mounts the same template components and paginator as the editor preview,
 * so page breaks in the PDF match what the user saw. Chrome emits real text,
 * so the result stays selectable and searchable for ATS parsers.
 */
class ResumePdfRenderer
{
    public function __construct(private ResumePhotos $photos) {}

    public function render(Resume $resume): string
    {
        $browsershot = Browsershot::html($this->html($resume))
            ->setNodeBinary((string) config('cvforge.pdf.node_binary'))
            ->newHeadless()
            ->showBackground()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->setOption('preferCSSPageSize', true)
            ->waitForFunction('window.__CV_READY__ === true', timeout: 15000)
            ->timeout((int) config('cvforge.pdf.timeout'));

        if ($chrome = config('cvforge.pdf.chrome_path')) {
            $browsershot->setChromePath((string) $chrome);
        }

        if (config('cvforge.pdf.no_sandbox')) {
            $browsershot->noSandbox();
        }

        return $browsershot->pdf();
    }

    public function html(Resume $resume): string
    {
        $payload = ResumePayload::make($resume, $this->photos->dataUri($resume));

        return view('pdf.resume', [
            'title' => $this->documentTitle($resume),
            'locale' => $payload['locale'],
            'payload' => $payload,
            'style' => $this->bundleFile('cv-print.css'),
            // A literal "</script" inside the bundle would end the inline tag early.
            'script' => str_ireplace('</script', '<\/script', $this->bundleFile('cv-print.js')),
        ])->render();
    }

    public function filename(Resume $resume): string
    {
        $name = trim(preg_replace('/[^\pL\pN\s._-]+/u', '', $this->documentTitle($resume)) ?? '');

        return ($name !== '' ? $name : 'CV').'.pdf';
    }

    private function documentTitle(Resume $resume): string
    {
        return $resume->full_name ? 'CV - '.$resume->full_name : $resume->title;
    }

    private function bundleFile(string $name): string
    {
        $path = config('cvforge.pdf.bundle_path').DIRECTORY_SEPARATOR.$name;

        if (! is_file($path)) {
            throw new RuntimeException("CV print bundle missing at [{$path}]. Run `npm run build` first.");
        }

        return (string) file_get_contents($path);
    }
}
