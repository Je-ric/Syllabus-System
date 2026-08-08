<?php

namespace App\Http\Controllers\Cais;

use App\Exceptions\CaisApiException;
use App\Http\Controllers\Controller;
use App\Services\System\CaisApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaisSemesterController extends Controller
{
    public function __construct(private readonly CaisApiService $cais) {}

    /**
     * List semesters.
     * Optional: ?status=active  ?year=2025-2026
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $semesters = $this->cais->getSemesters(
                $request->string('status')->toString() ?: null,
                $request->string('year')->toString() ?: null,
            );
            return response()->json(['data' => $semesters]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    /**
     * The current active semester.
     */
    public function active(): JsonResponse
    {
        try {
            $semester = $this->cais->getActiveSemester();

            if (empty($semester)) {
                return response()->json(['message' => 'No active semester found.'], 404);
            }

            return response()->json(['data' => $semester]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Single semester by CAIS ID.
     */
    public function show(int $caisSemesterId): JsonResponse
    {
        try {
            $semester = $this->cais->getSemester($caisSemesterId);

            if (empty($semester)) {
                return response()->json(['message' => 'Semester not found.'], 404);
            }

            return response()->json(['data' => $semester]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    public function bustCache(Request $request): JsonResponse
    {
        $id = $request->integer('cais_semester_id') ?: null;
        $this->cais->bustSemesterCache($id);

        return response()->json(['message' => 'Semester cache cleared.']);
    }

    private function apiError(CaisApiException $e): JsonResponse
    {
        $status = $e->isUnavailable() ? 503 : ($e->getStatusCode() ?: 502);
        return response()->json(['message' => $e->getMessage()], $status);
    }
}
