<?php

namespace App\Http\Controllers\Cais;

use App\Exceptions\CaisApiException;
use App\Http\Controllers\Controller;
use App\Services\System\CaisApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaisFacultyController extends Controller
{
    public function __construct(private readonly CaisApiService $cais) {}

    /**
     * List faculty for a department.
     * Requires ?department_id=
     * Route is admin-only (see cais.php).
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['department_id' => ['required', 'integer', 'min:1']]);

        try {
            $faculty = $this->cais->getFacultyByDepartment($request->integer('department_id'));
            return response()->json(['data' => $faculty]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Single faculty profile.
     * Any authenticated CSMS user can fetch a profile (needed for syllabus display).
     */
    public function show(int $caisUserId): JsonResponse
    {
        try {
            $profile = $this->cais->getFacultyProfile($caisUserId);

            if (empty($profile)) {
                return response()->json(['message' => 'Faculty not found.'], 404);
            }

            return response()->json(['data' => $profile]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    private function apiError(CaisApiException $e): JsonResponse
    {
        $status = $e->isUnavailable() ? 503 : ($e->getStatusCode() ?: 502);
        return response()->json(['message' => $e->getMessage()], $status);
    }
}
