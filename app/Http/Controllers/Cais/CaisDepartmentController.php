<?php

namespace App\Http\Controllers\Cais;

use App\Exceptions\CaisApiException;
use App\Http\Controllers\Controller;
use App\Services\CaisApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaisDepartmentController extends Controller
{
    public function __construct(private readonly CaisApiService $cais) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $collegeId = $request->integer('college_id') ?: null;
            return response()->json(['data' => $this->cais->getDepartments($collegeId)]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    public function show(int $caisDepartmentId): JsonResponse
    {
        try {
            $department = $this->cais->getDepartment($caisDepartmentId);

            if (empty($department)) {
                return response()->json(['message' => 'Department not found.'], 404);
            }

            return response()->json(['data' => $department]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    public function bustCache(Request $request): JsonResponse
    {
        $deptId    = $request->integer('cais_department_id') ?: null;
        $collegeId = $request->integer('cais_college_id') ?: null;
        $this->cais->bustDepartmentCache($deptId, $collegeId);

        return response()->json(['message' => 'Department cache cleared.']);
    }

    private function apiError(CaisApiException $e): JsonResponse
    {
        $status = $e->isUnavailable() ? 503 : ($e->getStatusCode() ?: 502);
        return response()->json(['message' => $e->getMessage()], $status);
    }
}
