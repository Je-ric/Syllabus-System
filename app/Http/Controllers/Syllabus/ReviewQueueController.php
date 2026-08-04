<?php

namespace App\Http\Controllers\Syllabus;

use App\Http\Controllers\Controller;
use App\Models\Syllabus;
use App\Models\SyllabusReviewer;
use Illuminate\Support\Facades\Auth;

class ReviewQueueController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = SyllabusReviewer::query()
            ->with([
                'syllabus.course.program',
                'syllabus.academicCalendar',
                'syllabus.preparer',
                'syllabus.reviewForm',
            ]);

        if (! $user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $assignments = $query
            ->get()
            ->sortBy(fn ($a) => match ($a->status) {
                'pending'  => 0,
                default    => 1,
            });

        $pending = $assignments->filter(fn ($a) => $a->status === 'pending');
        $done    = $assignments->filter(fn ($a) => $a->status !== 'pending');

        return view('Syllabus.review-queue', compact('assignments', 'pending', 'done'));
    }

    public function show(Syllabus $syllabus)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Admins can view any syllabus review page.
        // Everyone else must be an assigned reviewer.
        if (! $user->hasRole('admin')) {
            $assigned = $syllabus->reviewers()
                ->where('user_id', $user->id)
                ->exists();

            if (! $assigned) {
                abort(403, 'You are not assigned as a reviewer for this syllabus.');
            }
        }

        return view('Syllabus.reviewer', compact('syllabus'));
    }
}
