<?php

namespace App\Services\Syllabus\Snapshots;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

/** Print layout for immutable snapshots; no browser CSS or JavaScript required. */
class SyllabusDompdfService
{
    public const CACHE_SUFFIX = '.dompdf-v3.pdf';

    public function render(string $html): string
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">' . $html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $xpath = new DOMXPath($document);
        $source = $xpath->query('//*[@id="syllabus-content"]')->item(0);
        if (! $source instanceof DOMElement) {
            throw new RuntimeException('This snapshot does not contain the syllabus source required by Dompdf.');
        }

        // Serialize only the source's children, never the XML encoding hint,
        // preview toolbar, browser page containers, or live database content.
        foreach (iterator_to_array($xpath->query('.//script | .//style | .//link | .//iframe', $source)) as $node) {
            $node->parentNode->removeChild($node);
        }
        $this->prepareMarkup($source, $xpath);
        $sections = $this->sections($source, $document);
        if ($sections === []) {
            throw new RuntimeException('The saved syllabus contains no printable content.');
        }

        $merged = new Fpdi();
        $merged->SetAutoPageBreak(false);
        $merged->SetMargins(0, 0, 0);
        $merged->AliasNbPages();
        $merged->SetTitle(trim($document->getElementsByTagName('title')->item(0)?->textContent ?? 'Course syllabus'), true);

        foreach ($sections as $section) {
            $options = new Options();
            $options->set('isRemoteEnabled', false);
            $options->set('isPhpEnabled', false);
            $options->set('isJavascriptEnabled', false);
            $options->set('defaultFont', 'DejaVu Sans');
            $pdf = new Dompdf($options);
            $pdf->setPaper('a4', $section['orientation']);
            $pdf->loadHtml(view('Syllabus.pdf.dompdf', $section)->render(), 'UTF-8');
            $pdf->render();

            $count = $merged->setSourceFile(StreamReader::createByString($pdf->output()));
            for ($page = 1; $page <= $count; $page++) {
                $template = $merged->importPage($page);
                $size = $merged->getTemplateSize($template);
                $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merged->useTemplate($template);
                $merged->SetFont('Helvetica', '', 8);
                $merged->SetTextColor(90, 90, 90);
                $merged->SetXY(0, $size['height'] - 9);
                $merged->Cell($size['width'], 4, 'Page ' . $merged->PageNo() . ' of {nb}', 0, 0, 'C');
            }
            unset($pdf);
        }

        return $merged->Output('S');
    }

    private function prepareMarkup(DOMElement $source, DOMXPath $xpath): void
    {
        // The institutional letterhead is a three-column grid in the browser.
        // A table gives Dompdf the same centered title and separate logo column.
        foreach (iterator_to_array($xpath->query('./div[contains(@style, "grid-template-columns")]', $source)) as $header) {
            $table = $source->ownerDocument->createElement('table');
            $table->setAttribute('class', 'pdf-letterhead');
            $row = $table->appendChild($source->ownerDocument->createElement('tr'));
            foreach (iterator_to_array($header->childNodes) as $child) {
                if ($child instanceof DOMElement) {
                    $cell = $row->appendChild($source->ownerDocument->createElement('td'));
                    $cell->appendChild($child);
                }
            }
            // Older snapshots have no right-hand spacer in their header grid.
            if ($row->childNodes->length === 2) {
                $row->appendChild($source->ownerDocument->createElement('td'));
            }
            $header->parentNode->replaceChild($table, $header);
        }

        // Dompdf cannot carry a body rowspan over a page boundary. Repeat its
        // label in each affected evaluation row so no CO/standard disappears.
        foreach ($xpath->query('.//table[contains(concat(" ", normalize-space(@class), " "), " evaluation-table ")]/tbody', $source) as $tbody) {
            $spans = [];
            foreach ($xpath->query('./tr', $tbody) as $row) {
                $column = 0;
                foreach (iterator_to_array($xpath->query('./td | ./th', $row)) as $cell) {
                    while (isset($spans[$column])) {
                        $span = $spans[$column];
                        $row->insertBefore($span['cell']->cloneNode(true), $cell);
                        if (--$spans[$column]['remaining'] === 0) {
                            unset($spans[$column]);
                        }
                        $column += max(1, (int) $span['cell']->getAttribute('colspan'));
                    }
                    $count = (int) $cell->getAttribute('rowspan');
                    $cell->removeAttribute('rowspan');
                    if ($count > 1) {
                        $spans[$column] = ['cell' => $cell, 'remaining' => $count - 1];
                    }
                    $column += max(1, (int) $cell->getAttribute('colspan'));
                }
                while (isset($spans[$column])) {
                    $span = $spans[$column];
                    $row->appendChild($span['cell']->cloneNode(true));
                    if (--$spans[$column]['remaining'] === 0) {
                        unset($spans[$column]);
                    }
                    $column += max(1, (int) $span['cell']->getAttribute('colspan'));
                }
            }
        }

        // Keep semantic inline formatting, but discard browser-only geometry.
        foreach ($xpath->query('.//*[@style]', $source) as $node) {
            $style = preg_replace('/(?:^|;)\s*(?:display|grid[^:;]*|flex[^:;]*|gap|column-gap|overflow[^:;]*|height|min-height|max-height|position|float)\s*:[^;]*/i', '', $node->getAttribute('style'));
            $style = preg_replace('/var\(--font-body\)/', 'DejaVu Sans', $style);
            $style = preg_replace('/var\(--font-table\)/', 'DejaVu Serif', $style);
            $style = str_replace(['var(--fs-body)', 'var(--fs-table)', 'var(--lh-body)'], ['11pt', '9pt', '1.2'], $style);
            $node->setAttribute('style', $style);
        }
    }

    /** Match explicit section boundaries, retaining source order and orientation. */
    private function sections(DOMElement $source, DOMDocument $document): array
    {
        $sections = [];
        $orientation = 'portrait';
        $content = '';
        $hasLandscape = false;
        foreach ($source->childNodes as $node) {
            if ($node instanceof DOMElement && preg_match('/\blandscape\b/', $node->getAttribute('class'))) {
                $hasLandscape = true;
            }
        }
        foreach ($source->childNodes as $node) {
            if (! $node instanceof DOMElement && trim($node->textContent) === '') {
                continue;
            }
            if ($node instanceof DOMElement && preg_match('/\b(landscape|portrait)\b/', $node->getAttribute('class'), $match)) {
                if ($content !== '' && ($hasLandscape || $match[1] !== $orientation)) {
                    $sections[] = compact('orientation', 'content');
                    $content = '';
                }
                $orientation = $match[1];
            }
            $content .= $document->saveHTML($node);
        }
        if (trim($content) !== '') {
            $sections[] = compact('orientation', 'content');
        }

        return $sections;
    }
}
