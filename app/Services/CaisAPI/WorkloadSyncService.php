<?php

namespace App\Services\CaisAPI;

use App\Models\CaisClassSchedule;
use App\Models\CaisSemester;
use App\Models\CaisTeachingLoad;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CaisAPI\CaisHttpClient;

/**
 * Verifies CAIS credentials for a user, fetches their workload,
 * and syncs semesters, class schedules, and teaching loads into local DB.
 *
 * Used by: WorkloadController
 */
class WorkloadSyncService extends CaisHttpClient
{
    public function __construct(private readonly CaisAuthService $auth) {}

    /**
     * Full sync flow:
     *  1. Verify credentials against CAIS
     *  2. Store the CAIS token in session
     *  3. Fetch workloads using that token
     *  4. Persist semesters → schedules → teaching loads
     *
     * Returns the synced teaching loads for display, or throws on failure.
     */
    public function syncForUser(User $user, string $email, string $password): array
    {
        // Step 1 — verify credentials; returns ['token' => ..., 'user' => ...] or null
        $result = $this->auth->verifyUser($email, $password);

        if ($result === null) {
            throw new \RuntimeException('Invalid CAIS credentials. Please check your email and password.');
        }

        // Step 2 — store token in session so getWithUserToken() can use it
        session(['cais_token' => $result['token']]);

        // Step 3 — update user's cais_user_id if we got one back
        $caisUserId = data_get($result['user'], 'cais_user_id');
        if ($caisUserId && $user->cais_user_id !== $caisUserId) {
            $user->update(['cais_user_id' => $caisUserId]);
        }

        // Step 4 — fetch workloads (requires the session token set above)
        $url = config('cais.endpoints.workloads');
        if (empty($url)) {
            throw new \RuntimeException('CAIS workloads endpoint is not configured.');
        }

        $payload = $this->getWithUserToken($url);

        // Workload response shape: { workloads: [ { semester, course, schedule, teaching_load } ] }
        // Also support flat array at root
        $workloads = data_get($payload, 'workloads', data_get($payload, 'data', $payload));

        if (! is_array($workloads) || empty($workloads)) {
            // No workloads returned — still a valid sync, just nothing to store
            return [];
        }

        return $this->persist($user, $workloads);
    }

    /**
     * Persist the workload data into local DB inside a transaction.
     * Uses updateOrCreate on external_id so re-syncing is safe (idempotent).
     */
    private function persist(User $user, array $workloads): array
    {
        $syncedLoads = [];

        DB::transaction(function () use ($user, $workloads, &$syncedLoads) {
            foreach ($workloads as $item) {
                $semesterRaw = data_get($item, 'semester',      $item);
                $courseRaw   = data_get($item, 'course',        null);
                $scheduleRaw = data_get($item, 'schedule',      data_get($item, 'class_schedule', $item));
                $loadRaw     = data_get($item, 'teaching_load', $item);

                // ── Semester ──────────────────────────────────────────────
                $externalSemesterId = data_get($semesterRaw, 'semester_id', data_get($semesterRaw, 'id'));

                $semester = null;
                if ($externalSemesterId) {
                    $semester = CaisSemester::updateOrCreate(
                        ['external_id' => $externalSemesterId],
                        [
                            'name'      => data_get($semesterRaw, 'semester_name', data_get($semesterRaw, 'name', '')),
                            'number'    => data_get($semesterRaw, 'semester_no',   data_get($semesterRaw, 'number')),
                            'year'      => data_get($semesterRaw, 'semester_year', data_get($semesterRaw, 'year')),
                            'status'    => data_get($semesterRaw, 'semester_status', data_get($semesterRaw, 'status')),
                            'synced_at' => now(),
                        ]
                    );
                }

                // ── Course ────────────────────────────────────────────────
                // Match an existing local Course by cais_course_id.
                // If matched, update its code/title/units from CAIS to keep them fresh.
                // We never CREATE a course here — courses need program_id and other
                // required fields that CAIS doesn't provide.
                $externalCourseId = data_get($courseRaw, 'course_id', data_get($courseRaw, 'id'));
                $localCourse      = null;

                if ($externalCourseId) {
                    $localCourse = Course::where('cais_course_id', $externalCourseId)->first();

                    if ($localCourse) {
                        $courseUpdates = [];

                        $code  = data_get($courseRaw, 'course_code',  data_get($courseRaw, 'subject_code'));
                        $title = data_get($courseRaw, 'course_title', data_get($courseRaw, 'subject_title', data_get($courseRaw, 'course_name')));
                        $units = data_get($courseRaw, 'units',        data_get($courseRaw, 'credit_units'));

                        if ($code  && $localCourse->course_code  !== $code)  $courseUpdates['course_code']  = $code;
                        if ($title && $localCourse->course_title !== $title) $courseUpdates['course_title'] = $title;
                        if ($units !== null && (string) $localCourse->credit_units !== (string) $units) {
                            $courseUpdates['credit_units'] = $units;
                        }

                        if (! empty($courseUpdates)) {
                            $localCourse->update($courseUpdates);
                        }
                    } else {
                        Log::info('WorkloadSync: no local course matched cais_course_id', [
                            'cais_course_id' => $externalCourseId,
                            'subject_code'   => data_get($courseRaw, 'course_code', data_get($courseRaw, 'subject_code')),
                        ]);
                    }
                }

                // ── Class Schedule ────────────────────────────────────────
                $externalSchedId = data_get($scheduleRaw, 'schedId', data_get($scheduleRaw, 'sched_id', data_get($scheduleRaw, 'id')));

                $schedule = null;
                if ($externalSchedId) {
                    $schedule = CaisClassSchedule::updateOrCreate(
                        ['external_id' => $externalSchedId],
                        [
                            'external_semester_id'   => $externalSemesterId,
                            'external_department_id' => data_get($scheduleRaw, 'dept_id'),
                            'external_course_id'     => $externalCourseId,
                            'cais_semester_id'        => $semester?->id,
                            'course_id'               => $localCourse?->id,
                            'subject_code'            => data_get($scheduleRaw, 'subject_code'),
                            'subject_title'           => data_get($scheduleRaw, 'subject_title'),
                            'units'                   => data_get($scheduleRaw, 'units'),
                            'section'                 => data_get($scheduleRaw, 'section'),
                            'room'                    => data_get($scheduleRaw, 'room'),
                            'time'                    => data_get($scheduleRaw, 'time'),
                            'class_type'              => data_get($scheduleRaw, 'class_type'),
                            'lab_type'                => data_get($scheduleRaw, 'lab_type'),
                            'synced_at'               => now(),
                        ]
                    );
                }

                // ── Teaching Load ─────────────────────────────────────────
                $externalLoadId = data_get($loadRaw, 'teaching_load_id', data_get($loadRaw, 'id'));

                if ($externalLoadId) {
                    $load = CaisTeachingLoad::updateOrCreate(
                        ['external_id' => $externalLoadId],
                        [
                            'external_user_id'       => data_get($loadRaw, 'user_id'),
                            'external_semester_id'   => $externalSemesterId,
                            'external_schedule_id'   => $externalSchedId,
                            'user_id'                => $user->id,
                            'cais_semester_id'        => $semester?->id,
                            'cais_class_schedule_id'  => $schedule?->id,
                            'is_deleted'              => (bool) data_get($loadRaw, 'is_deleted', false),
                            'synced_at'               => now(),
                        ]
                    );

                    $load->load('classSchedule.course', 'caisSemester');
                    $syncedLoads[] = $load;
                }
            }
        });

        return $syncedLoads;
    }
}
