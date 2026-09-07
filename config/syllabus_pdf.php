<?php

return [
    'driver' => env('SYLLABUS_PDF_DRIVER', 'chrome'),
    'chrome_path' => env('SYLLABUS_PDF_CHROME_PATH'),
    'timeout' => (int) env('SYLLABUS_PDF_TIMEOUT', 60),
    'virtual_time_budget' => (int) env('SYLLABUS_PDF_VIRTUAL_TIME_BUDGET', 5000),
    'no_sandbox' => (bool) env('SYLLABUS_PDF_NO_SANDBOX', false),
];
