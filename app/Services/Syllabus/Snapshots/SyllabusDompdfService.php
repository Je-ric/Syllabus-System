<?php

namespace App\Services\Syllabus\Snapshots;

use DOMDocument;
use DOMXPath;
use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

/** Experimental server-only renderer for the existing frozen HTML snapshots. */
class SyllabusDompdfService
{
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
        if (! $source) {
            throw new RuntimeException('This snapshot does not contain the syllabus source required by Dompdf.');
        }

        // Render the frozen content directly: browser JavaScript normally moves
        // these hidden nodes into measured A4 pages, which Dompdf cannot do.
        foreach (iterator_to_array($xpath->query('//script | //link')) as $node) {
            $node->parentNode->removeChild($node);
        }
        $source->removeAttribute('style');
        $body = $document->getElementsByTagName('body')->item(0);
        foreach (iterator_to_array($body->childNodes) as $node) {
            if ($node !== $source) {
                $body->removeChild($node);
            }
        }
        $landscape = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " landscape ")]')->length > 0;
        $content = $document->saveHTML();
        // Resolve typography tokens used by the frozen preview stylesheet.
        $content = str_replace(
            ['var(--font-body)', 'var(--font-table)', 'var(--fs-body)', 'var(--fs-table)', 'var(--lh-body)'],
            ['DejaVu Sans, sans-serif', 'DejaVu Serif, serif', '10pt', '8pt', '1.2'],
            $content
        );
        $css = '<style>
            @page { margin: 15mm; }
            body { padding:0 !important; margin:0; background:white; }
            #syllabus-content { display:block !important; }
            div { height:auto; overflow:visible; }
            .a4-list,.a4-row { display:block; }
            table { width:100% !important; border-collapse:collapse; table-layout:fixed; }
            th,td { overflow-wrap:break-word; }
            thead { display:table-header-group; }
            tr { page-break-inside:avoid; }
            img { max-width:100%; }
            .landscape,.portrait { page:auto; }
        </style>';
        $content = str_replace('</head>', $css . '</head>', $content);
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $pdf = new Dompdf($options);
        $pdf->loadHtml($content, 'UTF-8');
        // Trial limitation: wide Complete documents use landscape throughout.
        $pdf->setPaper('a4', $landscape ? 'landscape' : 'portrait');
        $pdf->render();

        return $pdf->output();
    }
}
