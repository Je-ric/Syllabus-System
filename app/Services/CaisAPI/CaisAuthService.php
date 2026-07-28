<?php

namespace App\Services\CaisAPI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles CAIS credential verification.
 * Used by: AuthController::login()
 */
class CaisAuthService extends CaisHttpClient
{
    /**
     * Verify a user's credentials against CAIS.
     *
     * Returns ['token' => string, 'user' => normalizedArray] on success.
     * null tells AuthController to fall back to local password auth.
     *
     * Supports two response shapes:
     *   - Encrypted: { ciphertext, iv, tag }  — used by admissions.clsu.edu.ph
     *   - Plain JSON: { token, user }          — used by local/dev systems
     */
    public function verifyUser(string $employeeId, string $password): ?array
    {
        $url = config('cais.endpoints.verify_user');

        if (empty($url)) {
            Log::warning('CAIS verify_user endpoint not configured — skipping CAIS auth.');
            return null;
        }

        try {
            $sharedKey = config('cais.shared_key');

            // If a shared key is set, encrypt the credentials before sending.
            // Without a key (ex. local dev), send plain JSON.
            $body = ! empty($sharedKey)
                ? $this->encryptPayload(['employee_id' => $employeeId, 'password' => $password])
                : ['employee_id' => $employeeId, 'password' => $password];

            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->timeout($this->timeout)
                ->post($url, $body);

            if (! $response->successful()) {
                if ($response->status() !== 401) {
                    Log::warning('CAIS verifyUser unexpected response', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
                return null; // Either way, return null
            }

            $json = $response->json();

            // If the response contains 'ciphertext', it's encrypted — decrypt it first
            if (! empty($sharedKey) && isset($json['ciphertext'])) {
                $json = $this->decryptResponse($json);
                if ($json === null) {
                    // Decryption failed — wrong key or tampered response, do not proceed
                    Log::warning('CAIS verifyUser: decryption failed.');
                    return null;
                }
            }

            $token = data_get($json, 'token');

            // CAIS may wrap the user under 'user', 'faculty', or return it at the root level
            $raw = data_get($json, 'user', data_get($json, 'faculty', $json));

            if (! $token || ! is_array($raw)) {
                // Response was 200 but missing the data we need — log and bail
                Log::warning('CAIS verifyUser: missing token or user in response', ['body' => $json]);
                return null;
            }

            // Normalize the user array to a consistent shape before returning
            return ['token' => $token, 'user' => $this->normalizeUser($raw)];

        } catch (ConnectionException $e) {
            // fall back to local auth silently
            Log::warning('CAIS verifyUser connection failed — falling back to local auth', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
