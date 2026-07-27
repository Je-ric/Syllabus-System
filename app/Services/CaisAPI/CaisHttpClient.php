<?php

namespace App\Services\CaisAPI;

use App\Exceptions\CaisApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shared HTTP transport and crypto for all CAIS sub-services.
 * Domain services extend this — never instantiate directly.
 */
abstract class CaisHttpClient
{
    protected int $timeout;

    public function __construct()
    {
        // How long (seconds) to wait before giving up on a CAIS request
        $this->timeout = config('cais.timeout');
    }

    // -------------------------------------------------------------------------
    // HTTP internals
    // -------------------------------------------------------------------------

    /**
     * GET a list endpoint by its config key (e.g. 'colleges' → CAIS_COLLEGES_API_ENDPOINT).
     * Throws if the endpoint is not configured in .env.
     */
    protected function get(string $endpointKey): array
    {
        $url = config("cais.endpoints.{$endpointKey}");

        if (empty($url)) {
            throw new CaisApiException("CAIS endpoint not configured: {$endpointKey}", 0);
        }

        return $this->request($url);
    }

    /**
     * GET a single-record endpoint. Swaps {id} in the URL template with the real ID.
     * e.g. /api/faculty/{id} → /api/faculty/25
     */
    protected function getWithId(string $endpointKey, int $id): array
    {
        $template = config("cais.endpoints.{$endpointKey}");

        if (empty($template)) {
            throw new CaisApiException("CAIS endpoint not configured: {$endpointKey}", 0);
        }

        return $this->request(str_replace('{id}', $id, $template));
    }

    /**
     * GET an endpoint using the logged-in user's Bearer token (stored in session after CAIS login).
     * Used for user-scoped data: teaching loads, schedules, workloads.
     * Throws if the user hasn't logged in via CAIS yet (no token in session).
     */
    protected function getWithUserToken(string $url): array
    {
        // The CAIS token is saved to session during login — see AuthController
        $token = session('cais_token');

        if (empty($token)) {
            // No token means the user either logged in locally or the session expired
            throw new CaisApiException('No CAIS session token — user must log in first.', 401);
        }

        try {
            $response = Http::withToken($token)           // Bearer <token>
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout($this->timeout)
                ->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $status  = $response->status();
            $message = $response->json('message') ?? "CAIS API error: {$url}";

            Log::warning('CAIS user-token request failed', ['url' => $url, 'status' => $status]);

            throw new CaisApiException($message, $status);

        } catch (ConnectionException $e) {
            Log::error('CAIS user-token connection failed', ['url' => $url, 'error' => $e->getMessage()]);
            throw new CaisApiException("Could not connect to CAIS: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Core GET request for shared/public CAIS data (colleges, departments, semesters, faculty list).
     * No API key — CAIS identifies the caller by network/IP on these endpoints.
     */
    protected function request(string $url): array
    {
        try {
            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->timeout($this->timeout)
                ->get($url);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            $status  = $response->status();
            $message = $response->json('message') ?? "CAIS API error: {$url}";

            Log::warning('CAIS API non-success response', ['url' => $url, 'status' => $status, 'message' => $message]);

            throw new CaisApiException($message, $status);

        } catch (ConnectionException $e) {
            Log::error('CAIS API connection failed', ['url' => $url, 'error' => $e->getMessage()]);
            throw new CaisApiException("Could not connect to CAIS: {$e->getMessage()}", 0, $e);
        }
    }

    // -------------------------------------------------------------------------
    // Crypto — AES-256-GCM encrypt/decrypt for CAIS payload exchange
    // -------------------------------------------------------------------------

    /**
     * Encrypts an array payload using AES-256-GCM before sending to CAIS.
     * The shared key (API_SHARED_KEY in .env) is a 64-char hex string — hex2bin
     * converts it to the 32-byte binary key that AES-256 expects.
     * Returns { ciphertext, iv, tag } — all base64-encoded for safe JSON transport.
     */
    public function encryptPayload(array $payload): array
    {
        // Convert the hex key string to raw binary (AES-256 needs exactly 32 bytes)
        $key = hex2bin(config('cais.shared_key'));

        // GCM mode requires a 12-byte (96-bit) IV — must be random per request
        $iv  = random_bytes(12);

        // $tag is filled by reference — openssl writes the 16-byte auth tag into it
        $tag = '';

        $ciphertext = openssl_encrypt(
            json_encode($payload),  // Serialize the array to a JSON string first
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,       // Return raw binary, not base64
            $iv,
            $tag                    // Auth tag output — proves the data wasn't tampered with
        );

        // Base64-encode everything so it's safe to send as JSON values
        return [
            'ciphertext' => base64_encode($ciphertext),
            'iv'         => base64_encode($iv),
            'tag'        => base64_encode($tag),
        ];
    }

    /**
     * Decrypts a CAIS response that came back as { ciphertext, iv, tag }.
     * GCM authentication is built into openssl_decrypt — if the tag doesn't match
     * (wrong key or tampered data), it returns false instead of garbage plaintext.
     * Returns the decoded array, or null if decryption fails for any reason.
     */
    public function decryptResponse(array $jsonResponse): ?array
    {
        try {
            $key = hex2bin(config('cais.shared_key'));

            // Decode from base64 back to raw binary before passing to openssl
            $ciphertext = base64_decode($jsonResponse['ciphertext']);
            $iv         = base64_decode($jsonResponse['iv']);
            $tag        = base64_decode($jsonResponse['tag']);

            $decrypted = openssl_decrypt(
                $ciphertext,
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag    // GCM verifies this tag — mismatch = returns false, not bad data
            );

            if ($decrypted === false) {
                // This means either the wrong key was used or the payload was modified in transit
                Log::error('CAIS decryptResponse: openssl_decrypt returned false — wrong key or tampered data.');
                return null;
            }

            // Decrypted value is a JSON string — decode it back to an array
            return json_decode($decrypted, true);

        } catch (\Throwable $e) {
            Log::error('CAIS decryptResponse exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Normalizers — map inconsistent CAIS field names to stable CSMS keys
    // -------------------------------------------------------------------------

    /**
     * Different CAIS endpoints return user fields under different names
     * (e.g. 'user_fname' vs 'first_name', 'id' vs 'user_id').
     * This flattens all variations into one consistent shape used everywhere in CSMS.
     */
    protected function normalizeUser(array $u): array
    {
        // Some systems send a combined 'name'; others send 'first_name' + 'last_name'
        $name      = data_get($u, 'name');
        $firstName = data_get($u, 'first_name', data_get($u, 'user_fname', ''));
        $lastName  = data_get($u, 'last_name',  data_get($u, 'user_lname', ''));

        return [
            'cais_user_id' => data_get($u, 'id', data_get($u, 'user_id')),
            // Use the combined name if present, otherwise join first + last
            'name'         => $name ?: trim("{$firstName} {$lastName}"),
            // If only a combined name was given, split it at the first space
            'first_name'   => $firstName ?: ($name ? explode(' ', $name, 2)[0] : ''),
            'last_name'    => $lastName  ?: ($name ? (explode(' ', $name, 2)[1] ?? '') : ''),
            'email'        => data_get($u, 'email'),
            'user_type'    => data_get($u, 'user_type'),
        ];
    }

    protected function normalizeSchedule(array $s): array
    {
        return [
            'sched_id'      => data_get($s, 'schedId'),       // CAIS uses camelCase 'schedId'
            'subject_code'  => data_get($s, 'subject_code'),
            'subject_title' => data_get($s, 'subject_title'),
            'semester_id'   => data_get($s, 'semester_id'),
            'course_id'     => data_get($s, 'course_id'),
            'dept_id'       => data_get($s, 'dept_id'),
        ];
    }

    protected function normalizeTeachingLoad(array $tl): array
    {
        return [
            'teaching_load_id' => data_get($tl, 'teaching_load_id'),
            'semester_id'      => data_get($tl, 'semester_id'),
            'user_id'          => data_get($tl, 'user_id'),
            'sched_id'         => data_get($tl, 'schedId'),   // CAIS uses camelCase 'schedId'
            'is_deleted'       => (bool) data_get($tl, 'is_deleted'),
        ];
    }
}
