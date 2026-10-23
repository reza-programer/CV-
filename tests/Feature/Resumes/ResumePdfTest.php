<?php

namespace Tests\Feature\Resumes;

use App\Models\Resume;
use App\Services\ResumePdfRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_print_page_embeds_user_content_as_inert_json(): void
    {
        $this->requirePrintBundle();

        $resume = Resume::factory()->create([
            'full_name' => 'Rina </script><script>alert(1)</script>',
        ]);

        $html = app(ResumePdfRenderer::class)->html($resume);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('</script>', $html);
        $this->assertStringContainsString('<title>CV - Rina &lt;/script&gt;', $html);
    }

    public function test_short_and_multipage_resumes_render_to_a4_pdf(): void
    {
        $this->requireBrowser();

        $renderer = app(ResumePdfRenderer::class);

        $short = Resume::factory()->create();
        $long = Resume::factory()->template('professional')->withContent(3)->create();

        $shortPdf = $renderer->render($short);
        $longPdf = $renderer->render($long);

        $this->assertStringStartsWith('%PDF-', $shortPdf);
        $this->assertSame(1, $this->pageCount($shortPdf));
        $this->assertGreaterThan(1, $this->pageCount($longPdf));

        // A4 is 595.28 × 841.89 pt; Chrome rounds it to 594.96 × 841.92.
        $this->assertMatchesRegularExpression('#/MediaBox \[0 0 59[45]\.\d+ 84[12]\.\d+\]#', $longPdf);
    }

    public function test_the_download_route_returns_an_attachment(): void
    {
        $this->requireBrowser();

        $resume = Resume::factory()->create(['full_name' => 'Dimas Prasetyo']);

        $this->actingAs($resume->user)
            ->get(route('resumes.pdf', $resume))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'attachment; filename="CV - Dimas Prasetyo.pdf"; filename*=UTF-8\'\'CV%20-%20Dimas%20Prasetyo.pdf');
    }

    /**
     * Page count from the root of the page tree.
     */
    private function pageCount(string $pdf): int
    {
        preg_match_all('#/Count (\d+)#', $pdf, $matches);

        return max(array_map('intval', $matches[1] ?: [0]));
    }

    private function requirePrintBundle(): void
    {
        if (! is_file(config('cvforge.pdf.bundle_path').'/cv-print.js')) {
            $this->markTestSkipped('Print bundle not built; run `npm run build`.');
        }
    }

    private function requireBrowser(): void
    {
        $this->requirePrintBundle();

        $chrome = config('cvforge.pdf.chrome_path');

        if ($chrome && ! is_file($chrome)) {
            $this->markTestSkipped("Chrome not found at [{$chrome}].");
        }
    }
}
