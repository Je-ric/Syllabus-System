<?php

namespace App\Http\Controllers\Cais;

use App\Exceptions\CaisApiException;
use App\Http\Controllers\Controller;
use App\Services\System\CaisApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisTeachingLoadController extends Controller
{
    public function __construct(private readonly CaisApiService $cais) {}

    /**
     * Teaching loads for the currently authenticated CSMS user.
     * Resolves their cais_user_id from the local users table.
     * Optional: ?semester_id=8
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->cais_user_id) {
            return response()->json([
                'message' => 'Your account is not linked to a CAIS user. Contact the administrator.',
            ], 422);
        }

        try {
            $semesterId = $request->integer('semester_id') ?: null;
            $loads      = $this->cais->getTeachingLoads($user->cais_user_id, $semesterId);

            return response()->json(['data' => $loads]);
        } catch (CaisApiException $e) {
            return $this->apiError($e);
        }
    }

    /**
     * Single teaching load detail by CAIS teaching load ID.
     */
    public function show(int $teachingLoadId): JsonResponse
    {
        try {
            $load = $this->cais->getTeachingLoad($teachingLoadId);

            if (empty($load)) {
                return response()->json(['message' => 'Teaching load not found.'], 404);
            }

            return response()->json(['data' => $load]);
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
