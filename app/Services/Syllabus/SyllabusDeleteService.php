<?php

namespace App\Services\Syllabus;

use App\Models\Syllabus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Centralises cascade-delete for a single syllabus.
// Used by SyllabusController::destroy() and CourseService::deleteCourse().
class SyllabusDeleteService
{
    // Delete a syllabus and all its child records + disk files.
    // The transaction is owned here so every caller is safe regardless of context.
    // Callers that already have an open transaction are fine — DB::transaction()
    // is re-entrant via Laravel's savepoint mechanism.
    public function delete(Syllabus $syllabus): void
    {
        DB::transaction(function () use ($syllabus) {
            $this->performDelete($syllabus);
        });
    }

    private function performDelete(Syllabus $syllabus): void
    {
        foreach ($syllabus->completeSyllabi as $snapshot) {
            foreach (['pdf_path', 'abridged_path', 'evaluation_path'] as $field) {
                $path = $snapshot->$field ?? '';
                if ($path === '') {
                    continue;
                }
                foreach (['local', 'google'] as $disk) {
                    try {
                        if (Storage::disk($disk)->exists($path)) {
                            Storage::disk($disk)->delete($path);
                        }
                    } catch (\Throwable) {
                        // non-fatal — continue cleanup
                    }
                }
            }
            $snapshot->delete();
        }

        $syllabus->components()->delete();
        $syllabus->courseOutcomes()->delete();

        foreach ($syllabus->weeks as $week) {
            $week->contents()->each(function ($content) {
                $content->evaluation()->delete();
                $content->delete();
            });
            $week->delete();
        }

        $syllabus->references()->delete();
        $syllabus->onlineMaterials()->delete();
        $syllabus->revisions()->delete();
        $syllabus->reviewers()->delete();
        $syllabus->delete();
    }
}
