<?php

namespace App\Services\Syllabus\Snapshots;

use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class SyllabusPdfService
{
    public function render(string $html): string
    {
        $workDir = storage_path('app/private/pdf-render/' . bin2hex(random_bytes(12)));
        $profilePath = $workDir . DIRECTORY_SEPARATOR . 'chrome-profile';
        $htmlPath = $workDir . DIRECTORY_SEPARATOR . 'snapshot.html';
        $pdfPath = $workDir . DIRECTORY_SEPARATOR . 'snapshot.pdf';

        if (! mkdir($profilePath, 0700, true) && ! is_dir($profilePath)) {
            throw new RuntimeException('Unable to create the temporary PDF directory.');
        }

        try {
            if (file_put_contents($htmlPath, $html) === false) {
                throw new RuntimeException('Unable to write the temporary syllabus snapshot.');
            }

            $arguments = [
                $this->chromeExecutable(),
                '--headless=new',
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--disable-breakpad',
                '--disable-crash-reporter',
                '--no-first-run',
                '--no-default-browser-check',
                '--user-data-dir=' . $profilePath,
                '--allow-file-access-from-files',
                '--run-all-compositor-stages-before-draw',
                '--no-pdf-header-footer',
                '--virtual-time-budget=' . max(1000, (int) config('syllabus_pdf.virtual_time_budget', 5000)),
                '--print-to-pdf=' . $pdfPath,
                $this->fileUrl($htmlPath),
            ];

            // Chrome's Windows sandbox cannot initialize reliably when PHP is
            // running under a service/IIS account. The isolated, one-use
            // profile keeps this render separate from the user's browser.
            if (PHP_OS_FAMILY === 'Windows' || config('syllabus_pdf.no_sandbox', false)) {
                array_splice($arguments, 2, 0, '--no-sandbox');
            }

            $process = new Process($arguments);
            $process->setTimeout(max(10, (int) config('syllabus_pdf.timeout', 60)));
            $process->run();

            if (! $process->isSuccessful() || ! is_file($pdfPath)) {
                $details = trim($process->getErrorOutput() ?: $process->getOutput());
                throw new RuntimeException('Chrome could not generate the syllabus PDF.' . ($details ? ' ' . $details : ''));
            }

            $pdf = file_get_contents($pdfPath);
            if ($pdf === false || ! str_starts_with($pdf, '%PDF-')) {
                throw new RuntimeException('Chrome returned an invalid syllabus PDF.');
            }

            return $pdf;
        } finally {
            File::deleteDirectory($workDir);
        }
    }

    private function chromeExecutable(): string
    {
        $candidates = array_filter([
            trim((string) config('syllabus_pdf.chrome_path')),
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        throw new RuntimeException('Chrome or Chromium is required. Configure SYLLABUS_PDF_CHROME_PATH.');
    }

    private function fileUrl(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);
        $segments = explode('/', $normalized);

        if (isset($segments[0]) && preg_match('/^[A-Za-z]:$/', $segments[0])) {
            $drive = array_shift($segments);

            return 'file:///' . $drive . '/' . implode('/', array_map('rawurlencode', $segments));
        }

        return 'file://' . implode('/', array_map('rawurlencode', $segments));
    }
}
