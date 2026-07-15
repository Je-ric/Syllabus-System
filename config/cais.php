<?php

return [
    'key'     => env('CAIS_API_KEY', ''),
    'timeout' => (int) env('CAIS_API_TIMEOUT', 10),

    'endpoints' => [
        'colleges'        => env('CAIS_COLLEGES_API_ENDPOINT'),
        'departments'     => env('CAIS_DEPARTMENTS_API_ENDPOINT'),
        'courses'         => env('CAIS_COURSES_API_ENDPOINT'),

        'users'           => env('CAIS_USERS_API_ENDPOINT'),
        'user_profile'    => env('CAIS_USER_PROFILE_API_ENDPOINT'),

        'semesters'       => env('CAIS_SEMESTERS_API_ENDPOINT'),
        'semester'        => env('CAIS_SEMESTER_API_ENDPOINT'),
        'activeSemester'  => env('CAIS_ACTIVE_SEMESTER_API_ENDPOINT'),

        'teaching_loads'  => env('CAIS_TEACHING_LOADS_API_ENDPOINT'),
        'teaching_load'   => env('CAIS_TEACHING_LOAD_API_ENDPOINT'),

        'schedules'       => env('CAIS_SCHEDULES_API_ENDPOINT'),
    ],

    'cache' => [
        'colleges'        => (int) env('CAIS_CACHE_COLLEGES',        86400),  // 24h
        'departments'     => (int) env('CAIS_CACHE_DEPARTMENTS',      86400),  // 24h
        'user_profile'    => (int) env('CAIS_CACHE_USER_PROFILE',     3600),   // 1h
        'user_list'       => (int) env('CAIS_CACHE_USER_LIST',        1800),   // 30min
        'semesters'       => (int) env('CAIS_CACHE_SEMESTERS',        21600),  // 6h
        'active_semester' => (int) env('CAIS_CACHE_ACTIVE_SEMESTER',  1800),   // 30min
        'teaching_loads'  => (int) env('CAIS_CACHE_TEACHING_LOADS',   900),    // 15min
        'schedule'        => (int) env('CAIS_CACHE_SCHEDULE',         900),    // 15min
    ],
];
