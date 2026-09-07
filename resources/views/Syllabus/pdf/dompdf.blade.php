<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /* Dedicated print styles: browser preview CSS must not enter this template. */
        @page { size: A4 {{ $orientation }}; margin: {{ $orientation === 'landscape' ? '12mm' : '19mm' }}; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; color: #111; font: 11pt/1.2 'DejaVu Sans', sans-serif; }
        p { margin: 3pt 0; }
        h1, h2, h3, h4 { font-size: 11pt; margin: 8pt 0 5pt; page-break-after: avoid; }
        strong, b { font-weight: bold; }
        img { max-width: 100%; }
        a { color: #111; text-decoration: underline; overflow-wrap: break-word; }
        .a4-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; }
        .a4-subtitle { text-align: center; font-size: 11pt; margin-top: 1.5pt; }
        .a4-section { margin-top: 12pt; }
        .a4-section h3 { margin: 0 0 4.5pt; }
        .title-numbered, .title-lettered { font-size: 11pt; font-weight: bold; page-break-after: avoid; }
        .title-lettered { text-transform: uppercase; padding-top: 8pt; }
        .indent-level-1 { display: block; padding-left: 18.75pt; }
        .indent-level-1-5 { display: block; padding-left: 33.75pt; padding-bottom: 3.75pt; }
        .indent-level-1-5-text { padding-left: 26.25pt; text-indent: 7.5pt; }
        .indent-level-2 { padding-left: 33.75pt; text-indent: 30pt; }
        .indent-level-2-text { padding-left: 63.75pt; }
        .table-indent { margin-left: 18.75pt; }
        .abridged-indent { text-indent: 15pt; padding: 8pt; }
        .abridged-indent-table { margin-left: 11pt; width: 96%; }
        .a4-alpha-list { counter-reset: alpha; }
        .a4-alpha-list > div { margin-bottom: 3pt; padding-left: 15pt; }
        .a4-alpha-list > div:before { counter-increment: alpha; content: counter(alpha, lower-alpha) '. '; }
        .a4-coded-list { margin-bottom: 3pt; }
        .a4-coded-list .code { float: left; width: 38pt; }
        .a4-coded-list .text { margin-left: 40pt; }
        .a4-list { display: block; }
        .a4-row { margin-bottom: 3pt; }
        .a4-row > :first-child { float: left; width: 15pt; }
        .a4-row > :last-child { margin-left: 20pt; }
        ul, ol { margin: 3pt 0; padding-left: 22pt; }
        li { margin-bottom: 2pt; }
        table { width: 100%; border-collapse: collapse; margin: 9pt 0; font-size: 11pt; }
        th, td { border: 0.6pt solid #000; padding: 3pt; vertical-align: top; overflow-wrap: break-word; }
        th { background: #d9d9d9; font-weight: bold; }
        th p { font-weight: normal; font-size: 9pt; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .kv-table td:first-child { width: 24%; white-space: normal; }
        .abridged-table td { width: 50%; }
        .weekly-coverage-table { table-layout: fixed; }
        .weekly-coverage-table, .weekly-coverage-table th, .weekly-coverage-table td,
        .weekly-coverage-table p, .weekly-coverage-table li, .weekly-coverage-table strong {
            font-family: 'DejaVu Serif', serif; font-size: 9pt !important; line-height: 1.15;
        }
        .weekly-coverage-table th, .weekly-coverage-table td { width: auto !important; }
        .weekly-coverage-table th:first-child { width: 7% !important; }
        .weekly-coverage-table ul, .weekly-coverage-table ol { padding-left: 11pt; }
        .evaluation-table { table-layout: fixed; }
        .evaluation-table th, .evaluation-table td { font-size: 9pt; white-space: normal !important; padding: 2pt; }
        .fcas, .fcas td, .fcas th { border: none; }
        .section-e-title { padding-bottom: 6pt; }
        .section-e-subtitle { padding: 10pt 0 7pt; text-align: center; }
        .section-e-em, .section-e-description { padding-bottom: 10pt; }
        .pdf-letterhead { table-layout: fixed; margin: 0 0 9pt; page-break-inside: avoid; }
        .pdf-letterhead td { border: none; padding: 0; text-align: center; vertical-align: middle; }
        .pdf-letterhead td:first-child, .pdf-letterhead td:last-child { width: 21mm; }
        .pdf-letterhead img { width: 20mm !important; }
        .ql-align-center { text-align: center; }
        .ql-align-right { text-align: right; }
        .ql-align-justify { text-align: justify; }
        .ql-indent-1 { padding-left: 12pt; }
        .ql-indent-2 { padding-left: 24pt; }
        .ql-indent-3 { padding-left: 36pt; }
    </style>
</head>
<body>{!! $content !!}</body>
</html>
