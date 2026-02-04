<?php

namespace App\Livewire\Syllabus;

use App\Models\Course;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseSelector extends Component
{
    public ?int $programId = null;
    public $courses = [];

    public function mount(): void
    {
        $this->courses = [];
    }

    #[On('programSelected')]
    public function setProgram($programId): void
    {
        $this->programId = $programId ? (int) $programId : null;
        $this->loadCourses();
    }

    private function loadCourses(): void
    {
        if (!$this->programId) {
            $this->courses = [];
            return;
        }

        $this->courses = Course::where('program_id', $this->programId)
            ->with('components')
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->get();
    }

    public function render()
    {
        return view('livewire.syllabus.course-selector');
    }
}
