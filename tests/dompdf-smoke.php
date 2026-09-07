<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$renderer = new App\Services\Syllabus\Snapshots\SyllabusDompdfService();
$output = __DIR__ . '/../storage/app/private/dompdf-qa';
if (!is_dir($output)) { mkdir($output, 0700, true); }
function verifyText(string $pdf): void {
    preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdf, $streams);
    foreach ($streams[1] as $stream) {
        $decoded = @gzuncompress($stream);
        if ($decoded !== false && preg_match('/\bBT\b.*\bT[Jj]\b/s', $decoded)) { return; }
    }
    throw new RuntimeException('PDF contains no rendered text');
}
$pdf = $renderer->render('<html><head></head><body><div id="toolbar">Do not print</div><div id="syllabus-content" style="display:none"><h1>Syllabus</h1><table><tr><td>Grade</td><td>1.00</td></tr></table></div><div id="a4-container"></div></body></html>');
verifyText($pdf);
file_put_contents($output . '/smoke.pdf', $pdf);
if (in_array('--snapshots', $argv, true)) {
    foreach (['Complete', 'Abridged', 'Assessment'] as $type) {
        $matches = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../storage/app/private/syllabus-snapshots'));
        foreach ($files as $file) {
            if ($file->isFile() && str_starts_with($file->getFilename(), $type . ' - ') && $file->getExtension() === 'html') {
                $matches[] = $file->getPathname();
            }
        }
        usort($matches, fn ($a, $b) => filesize($b) <=> filesize($a));
        if (!$matches) { throw new RuntimeException('No snapshot found for ' . $type); }
        $pdf = $renderer->render(file_get_contents($matches[0]));
        verifyText($pdf);
        file_put_contents($output . '/' . $type . '.html', file_get_contents($matches[0]));
        file_put_contents($output . '/' . $type . '.pdf', $pdf);
        echo $type . ' contains rendered text; source: ' . basename($matches[0]) . PHP_EOL;
    }
}
echo "Dompdf smoke test passed: PDF contains text drawing commands.\n";
