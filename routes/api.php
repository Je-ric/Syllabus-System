<?php

use App\Http\Controllers\Cais\CaisCollegeController;
use App\Http\Controllers\Cais\CaisDepartmentController;
use App\Http\Controllers\Cais\CaisFacultyController;
use App\Http\Controllers\Cais\CaisSemesterController;
use App\Http\Controllers\Cais\CaisTeachingLoadController;
use App\Services\System\CaisApiService;
use App\Services\CaisAPI\CaisAuthService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CAIS Integration Test Routes (remove before production)
|--------------------------------------------------------------------------
|
| /test-cais          — quick smoke test, colleges only
| /test-cais-multiple — full integration test, all endpoints
|
| These routes are unauthenticated intentionally for local testing.
| IDs used below must match records in the mock — adjust as needed.
*/

Route::get('/test-cais', function (CaisApiService $cais) {
    return response()->json($cais->getColleges());
});

Route::get('/test-cais-multiple', function (CaisApiService $cais) {
    return response()->json([
        // Colleges
        'colleges'               => $cais->getColleges(),
        'college'                => $cais->getCollege(1),

        // Departments
        'departments'            => $cais->getDepartments(),
        'departments_by_college' => $cais->getDepartments(1),
        'department'             => $cais->getDepartment(1),

        // Faculty / Users
        'faculty'                => $cais->getUsers(),
        'faculty_profile'        => $cais->getFacultyProfile(1),
        'faculty_by_dept'        => $cais->getFacultyByDepartment(1),

        // Semesters
        'semesters'              => $cais->getSemesters(),
        'active_semester'        => $cais->getActiveSemester(),
        'semester'               => $cais->getSemester(1),

        // Teaching Loads
        'teaching_loads'         => $cais->getTeachingLoads(25),
        'teaching_loads_sem'     => $cais->getTeachingLoads(25, 1),
        'teaching_load'          => $cais->getTeachingLoad(1),

        // Schedules
        'schedules'              => $cais->getSchedules(),
        'schedule'               => $cais->getClassSchedule(103),
    ]);
});

Route::get('/test-key', function () {
    return [
        'shared_key' => config('cais.shared_key'),
        'length'     => strlen(config('cais.shared_key')),
    ];
});

Route::get('/test-encrypt', function (CaisAuthService $cais) {
    return $cais->encryptPayload([
        'email' => 'jdelacruz@clsu.edu.ph',
        'password' => 'password',
    ]);
});

Route::get('/test-crypto', function (CaisAuthService $auth) {
    $encrypted = $auth->encryptPayload([
        'email'    => 'jdelacruz@clsu.edu.ph',
        'password' => 'password',
    ]);

    return $auth->decryptResponse($encrypted);
});

Route::get('/test-login', function (CaisApiService $cais) {
    return response()->json(
        $cais->verifyUser('jdelacruz@clsu.edu.ph', 'password')
    );
});

/*
|--------------------------------------------------------------------------
| Authenticated CAIS proxy routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('cais')->name('cais.')->group(function () {

    // Colleges — consumed by: admin user assignment, college goals, org hierarchy
    Route::get('/colleges', [CaisCollegeController::class, 'index'])->name('colleges.index');
    Route::get('/colleges/{caisCollegeId}', [CaisCollegeController::class, 'show'])->name('colleges.show');
    Route::middleware(['role:admin'])->post('/colleges/cache/bust', [CaisCollegeController::class, 'bustCache'])->name('colleges.cache.bust');

    // Departments — consumed by: admin user assignment, department objectives, org hierarchy
    Route::get('/departments', [CaisDepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/{caisDepartmentId}', [CaisDepartmentController::class, 'show'])->name('departments.show');
    Route::middleware(['role:admin'])->post('/departments/cache/bust', [CaisDepartmentController::class, 'bustCache'])->name('departments.cache.bust');

    // Faculty / Users — consumed by: admin org hierarchy, syllabus course components
    Route::get('/faculty/{caisUserId}', [CaisFacultyController::class, 'show'])->name('faculty.show');
    Route::middleware(['role:admin'])->get('/faculty', [CaisFacultyController::class, 'index'])->name('faculty.index');

    // Semesters — consumed by: academic calendar, syllabus wizard step 1
    Route::get('/semesters', [CaisSemesterController::class, 'index'])->name('semesters.index');
    Route::get('/semesters/active', [CaisSemesterController::class, 'active'])->name('semesters.active');
    Route::get('/semesters/{caisSemesterId}', [CaisSemesterController::class, 'show'])->name('semesters.show');
    Route::middleware(['role:admin'])->post('/semesters/cache/bust', [CaisSemesterController::class, 'bustCache'])->name('semesters.cache.bust');

    // Teaching Loads — consumed by: syllabus wizard step 2
    Route::get('/teaching-loads', [CaisTeachingLoadController::class, 'index'])->name('teaching-loads.index');
    Route::get('/teaching-loads/{teachingLoadId}', [CaisTeachingLoadController::class, 'show'])->name('teaching-loads.show');
});
