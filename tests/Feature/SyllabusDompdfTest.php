<?php

namespace Tests\Feature;

use App\Services\Syllabus\Snapshots\SyllabusDompdfService;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use Tests\TestCase;

class SyllabusDompdfTest extends TestCase
{
    public function test_preserves_content_and_mixed_page_sizes_with_continuous_numbers(): void
    {
        $pdf = app(SyllabusDompdfService::class)->render('<html><head><style>body{display:none}</style></head><body>
            <div id="toolbar">DO NOT PRINT</div><div id="syllabus-content" style="display:none">
            <p>Portrait start café</p><div class="landscape"><p>Landscape middle</p></div>
            <div class="portrait"><p>Portrait end</p></div></div></body></html>');
        $reader = new Fpdi();
        $this->assertSame(3, $reader->setSourceFile(StreamReader::createByString($pdf)));
        foreach (['P', 'L', 'P'] as $index => $orientation) {
            $size = $reader->getTemplateSize($reader->importPage($index + 1));
            $this->assertSame($orientation, $size['orientation']);
            $this->assertEqualsWithDelta(210, min($size['width'], $size['height']), 0.1);
            $this->assertEqualsWithDelta(297, max($size['width'], $size['height']), 0.1);
        }
        $streams = $this->drawingStreams($pdf);
        foreach (['Portrait start café', 'Landscape middle', 'Portrait end'] as $text) {
            $this->assertStringContainsString(mb_convert_encoding($text, 'UTF-16BE', 'UTF-8'), $streams);
        }
        $this->assertStringNotContainsString(mb_convert_encoding('DO NOT PRINT', 'UTF-16BE'), $streams);
        foreach (range(1, 3) as $page) {
            $this->assertStringContainsString('Page ' . $page . ' of 3', $streams);
        }
    }

    public function test_long_tables_paginate_repeat_headers_and_keep_every_row(): void
    {
        $rows = '';
        foreach (range(1, 90) as $row) {
            $rows .= '<tr><td>ROW-' . $row . '</td><td>Learning outcome and assessment</td></tr>';
        }
        $pdf = app(SyllabusDompdfService::class)->render('<html><body><div id="syllabus-content">
            <table><thead><tr><th>Repeated heading</th><th>Topic</th></tr></thead><tbody>' . $rows . '</tbody></table>
            <p>END OF DOCUMENT</p></div></body></html>');
        $reader = new Fpdi();
        $pages = $reader->setSourceFile(StreamReader::createByString($pdf));
        $this->assertGreaterThan(1, $pages);
        $streams = $this->drawingStreams($pdf);
        $this->assertSame($pages, substr_count($streams, mb_convert_encoding('Repeated heading', 'UTF-16BE')));
        foreach (range(1, 90) as $row) {
            $this->assertStringContainsString(mb_convert_encoding('ROW-' . $row, 'UTF-16BE'), $streams);
        }
        $this->assertStringContainsString(mb_convert_encoding('END OF DOCUMENT', 'UTF-16BE'), $streams);
    }

    public function test_rejects_a_snapshot_without_printable_source(): void
    {
        $this->expectException(\RuntimeException::class);
        app(SyllabusDompdfService::class)->render('<html><body>Missing source</body></html>');
    }

    private function drawingStreams(string $pdf): string
    {
        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $matches);
        $text = '';
        foreach ($matches[1] as $stream) {
            $decoded = @gzuncompress($stream);
            if ($decoded !== false && str_contains($decoded, 'BT')) {
                $text .= $decoded;
            }
        }

        return $text;
    }
}
