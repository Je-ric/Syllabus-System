<?php

use App\Http\Controllers\Cais\CaisCollegeController;
use App\Http\Controllers\Cais\CaisDepartmentController;
use App\Http\Controllers\Cais\CaisFacultyController;
use App\Http\Controllers\Cais\CaisSemesterController;
use App\Http\Controllers\Cais\CaisTeachingLoadController;
use Illuminate\Support\Facades\Route;
use App\Services\CaisApiService;

/*
    |--------------------------------------------------------------------------
    | CAIS Integration Test Routes (remove before production)
    |--------------------------------------------------------------------------
    |
    | /test-cais          — quick smoke test, colleges only
    | /test-cais-multiple — full integration test, all endpoints
    |
    | These routes are unauthenticated intentionally for local testing.
    | IDs used below (e.g. getFacultyProfile(1)) must match records in the
    | Postman mock — adjust as needed.
 */
Route::get('/test-cais', function (CaisApiService $cais) {
    return response()->json(
        $cais->getColleges()
    );
});

Route::get('/test-cais-multiple', function (CaisApiService $cais) {
    return response()->json([
        // --- Colleges ---
        // getColleges()              no args — returns all
        // getCollege(int $id)        $id = college_id from CAIS
        'colleges'           => $cais->getColleges(),
        'college'            => $cais->getCollege(1),

        // --- Departments ---
        // getDepartments(?int $collegeId)   null = all, pass college_id to filter
        // getDepartment(int $id)            $id = dept_id from CAIS
        'departments'        => $cais->getDepartments(),
        'departments_by_college' => $cais->getDepartments(1),
        'department'         => $cais->getDepartment(1),

        // --- Faculty / Users ---
        // getUsers()                 no args — returns all faculty
        // getFacultyProfile(int $id) $id = CAIS user id  → hits /api/faculty/{id}
        // getFacultyByDepartment(int $deptId)  $deptId = dept_id from CAIS
        'faculty'            => $cais->getUsers(),
        'faculty_profile'    => $cais->getFacultyProfile(1),
        'faculty_by_dept'    => $cais->getFacultyByDepartment(1),

        // --- Semesters ---
        // getSemesters(?string $status, ?string $year)  both optional filters
        // getActiveSemester()        no args → hits /api/semesters/active
        // getSemester(int $id)       $id = semester_id → hits /api/semesters/{id}
        'semesters'          => $cais->getSemesters(),
        'active_semester'    => $cais->getActiveSemester(),
        'semester'           => $cais->getSemester(1),

        // --- Teaching Loads ---
        // getTeachingLoads(int $userId, ?int $semesterId)  $userId required, $semesterId optional
        // getTeachingLoad(int $id)   $id = teaching_load_id → hits /api/teaching-loads/{id}
        'teaching_loads'     => $cais->getTeachingLoads(25),
        'teaching_loads_sem' => $cais->getTeachingLoads(25, 1),
        'teaching_load'      => $cais->getTeachingLoad(1),

        // --- Schedules ---
        // getSchedules()             no args — returns all schedules
        // getClassSchedule(int $id)  $id = schedId from CAIS (filtered from list)
        'schedules'          => $cais->getSchedules(),
        'schedule'           => $cais->getClassSchedule(103),
    ]);
});

Route::get('/test-login', function (CaisApiService $cais) {
    return $cais->verifyUser(
        'juan@clsu.edu.ph',
        'password123'
    );
});

Route::middleware(['auth'])->prefix('cais')->name('cais.')->group(function () {

    // ── Colleges ─────────────────────────────────────────────────────────────
    // Consumed by: admin user assignment, college goals, org hierarchy
    Route::get('/colleges', [CaisCollegeController::class, 'index'])->name('colleges.index');
    Route::get('/colleges/{caisCollegeId}', [CaisCollegeController::class, 'show'])->name('colleges.show');

    // Cache bust — admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/colleges/cache/bust', [CaisCollegeController::class, 'bustCache'])->name('colleges.cache.bust');
    });

    // ── Departments ───────────────────────────────────────────────────────────
    // Consumed by: admin user assignment, department objectives, program management, org hierarchy
    // ?college_id= filters by college
    Route::get('/departments', [CaisDepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/{caisDepartmentId}', [CaisDepartmentController::class, 'show'])->name('departments.show');

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/departments/cache/bust', [CaisDepartmentController::class, 'bustCache'])->name('departments.cache.bust');
    });

    // ── Faculty / Users ───────────────────────────────────────────────────────
    // Consumed by: admin org hierarchy (assign faculty), syllabus course components
    // GET /cais/faculty?department_id= — list for a department (admin)
    // GET /cais/faculty/{caisUserId}   — single profile (any auth user)
    Route::get('/faculty/{caisUserId}', [CaisFacultyController::class, 'show'])->name('faculty.show');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/faculty', [CaisFacultyController::class, 'index'])->name('faculty.index');
    });

    // ── Semesters ─────────────────────────────────────────────────────────────
    // Consumed by: academic calendar create/edit, syllabus wizard step 1
    // ?status=active  ?year=2025-2026
    Route::get('/semesters', [CaisSemesterController::class, 'index'])->name('semesters.index');
    Route::get('/semesters/active', [CaisSemesterController::class, 'active'])->name('semesters.active');
    Route::get('/semesters/{caisSemesterId}', [CaisSemesterController::class, 'show'])->name('semesters.show');

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/semesters/cache/bust', [CaisSemesterController::class, 'bustCache'])->name('semesters.cache.bust');
    });

    // ── Teaching Loads ────────────────────────────────────────────────────────
    // Consumed by: syllabus wizard step 2 (course components pre-fill)
    // GET /cais/teaching-loads?semester_id= — loads for the authenticated user
    // GET /cais/teaching-loads/{id}         — single load detail
    Route::get('/teaching-loads', [CaisTeachingLoadController::class, 'index'])->name('teaching-loads.index');
    Route::get('/teaching-loads/{teachingLoadId}', [CaisTeachingLoadController::class, 'show'])->name('teaching-loads.show');
});
