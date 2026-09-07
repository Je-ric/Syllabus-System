<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /* Dedicated print styles: browser preview CSS must not enter this template. */
        @page { size: A4 {{ $orientation }}; margin: {{ $orientation === 'landscape' ? '12mm' : '19mm' }}; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; color: #111; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; font-size: 11pt; line-height: 1.2; }
        p { margin: 4px 0; line-height: 1.2; }
        h1, h2, h3, h4 { font-size: 11pt; margin: 8pt 0 5pt; page-break-after: avoid; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; }
        strong, b { font-weight: bold; }
        img { max-width: 100%; }
        a { color: #111; text-decoration: underline; overflow-wrap: break-word; }
        .a4-title { text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; letter-spacing: 0.04em; }
        .a4-subtitle { text-align: center; font-size: 11pt; margin-top: 2px; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; }
        .a4-section { margin-top: 16px; break-inside: auto; page-break-inside: auto; }
        .a4-section h3 { margin: 10px 0 6px; font-size: 11pt; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; font-weight: bold; text-align: justify; }
        .title-numbered, .title-lettered { font-size: 11pt; font-weight: bold; page-break-after: avoid; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; }
        .title-numbered { text-transform: none; }
        .title-lettered { text-transform: uppercase; padding-top: 15px; }
        .indent-level-1 { display: block; padding-left: 25px; }
        .indent-level-1-5 { display: block; padding-left: 45px; padding-bottom: 5px; }
        .indent-level-1-5-text { padding-left: 35px; text-indent: 10px; }
        .indent-level-2 { padding-left: 45px; text-indent: 40px; }
        .indent-level-2-text { padding-left: 85px; }
        .table-indent { margin-left: 25px; }
        .abridged-indent { text-indent: 20px; padding: 15px; }
        .abridged-indent-table { margin-left: 15px; width: calc(100% - 15px); }
        .a4-alpha-list { counter-reset: alpha; text-align: justify; }
        .a4-alpha-list > div { display: flex; align-items: flex-start; margin-bottom: 4px; }
        .a4-alpha-list > div:before { counter-increment: alpha; content: counter(alpha, lower-alpha) '. '; min-width: 20px; font-weight: 500; }
        .a4-coded-list { display: flex; align-items: flex-start; margin-bottom: 4px; text-align: justify; }
        .a4-coded-list .code { min-width: 22px; font-weight: 500; }
        .a4-coded-list .text { flex: 1; }
        .a4-list { display: block; }
        .a4-row { margin-bottom: 4px; }
        .a4-row > :first-child { float: left; width: 15pt; }
        .a4-row > :last-child { margin-left: 20pt; }
        ul, ol { margin: 3pt 0; padding-left: 80px; }
        li { margin-bottom: 2pt; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 11pt; font-family: Tahoma, 'Tahoma MT', Geneva, Verdana, sans-serif; border: 1px solid #000; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; overflow-wrap: break-word; }
        th { background: #d9d9d9; font-weight: bold; }
        th p { font-weight: normal; font-size: smaller; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .kv-table td:first-child { width: 5%; white-space: nowrap; }
        .abridged-table td { width: 50%; vertical-align: top; word-break: break-word; }
        .abridged-table { margin-bottom: 15px; }
        .weekly-coverage-table { table-layout: fixed; }
        .weekly-coverage-table, .weekly-coverage-table th, .weekly-coverage-table td,
        .weekly-coverage-table p, .weekly-coverage-table li, .weekly-coverage-table strong {
            font-family: 'Times New Roman', Times, serif; font-size: 9pt !important; line-height: 1.15;
        }
        .weekly-coverage-table th, .weekly-coverage-table td { width: auto !important; }
        .weekly-coverage-table th:first-child { width: 7% !important; }
        .weekly-coverage-table ul, .weekly-coverage-table ol { padding-left: 1.5em; margin: 0; }
        .weekly-coverage-table ul li { list-style-type: disc; list-style-position: outside; padding-left: 0; }
        .weekly-coverage-table ol li { list-style-type: decimal; list-style-position: outside; padding-left: 0; }
        .weekly-coverage-table ol li::before { display: none !important; }
        .evaluation-table { table-layout: fixed; }
        .evaluation-table th, .evaluation-table td { font-size: 9pt; white-space: normal !important; padding: 2pt; font-family: 'Times New Roman', Times, serif; }
        .evaluation-table td { padding: 0 0 0 2px; }
        .fcas, .fcas td, .fcas th { border: none; }
        .section-e-title { padding-bottom: 8px; }
        .section-e-subtitle { padding-bottom: 10px; padding-top: 15px; text-align: center; }
        .section-e-em, .section-e-description { padding-bottom: 15px; text-align: justify; }
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
