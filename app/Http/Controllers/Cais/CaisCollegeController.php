<?php

namespace App\Http\Controllers\Cais;

use App\Exceptions\CaisApiException;
use App\Http\Controllers\Controller;
use App\Services\System\CaisApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaisCollegeController extends Controller
{
    public function __construct(private readonly CaisApiService $cais) {}

    public function index(): JsonResponse
    {
        try {
            return response()->json(['data' => $this->cais->getColleges()]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    public function show(int $caisCollegeId): JsonResponse
    {
        try {
            $college = $this->cais->getCollege($caisCollegeId);

            if (empty($college)) {
                return response()->json(['message' => 'College not found.'], 404);
            }

            return response()->json(['data' => $college]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    public function bustCache(Request $request): JsonResponse
    {
        $id = $request->integer('cais_college_id') ?: null;
        $this->cais->bustCollegeCache($id);

        return response()->json(['message' => 'College cache cleared.']);
    }

    private function apiError(CaisApiException $e): JsonResponse
    {
        $status = $e->isUnavailable() ? 503 : ($e->getStatusCode() ?: 502);
        return response()->json(['message' => $e->getMessage()], $status);
    }
}
