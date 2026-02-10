<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'program_id',
        'course_code',
        'course_title',
        'course_description',
        'credit_units',
        'year_level',
        'semester',
        'has_lec_lab',
        'prerequisite',
        'corequisite',
        'created_by',
    ];

    // Used in:
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Used in:
        // update() - CourseController;
        // destroy() - CourseController
    public function programOutcomes()
    {
        return $this->belongsToMany(ProgramOutcome::class, 'course_curriculum_maps')
                    ->withPivot('ied');
    }

    // Used in:
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Course has many syllabi (different semesters/years)
    // Used in:
    public function syllabi()
    {
        return $this->hasMany(Syllabus::class);
    }

    // Note: Components are now tied to Syllabus, not Course directly
    // This method is kept for backward compatibility but should use syllabus->components() instead
    // Used in:
    public function components()
    {
        // Return components from the most recent syllabus, if any
        $latestSyllabus = $this->syllabi()->latest()->first();
        return $latestSyllabus ? $latestSyllabus->components() : collect();
    }

    // Helper: Check if course has lab component
    // Used in:
    public function hasLabComponent()
    {
        return $this->has_lec_lab;
    }

    // Helper: Get LEC component (from latest syllabus)
    // Used in: loadExistingData() - SyllabusWizard
    public function getLecComponent()
    {
        $latestSyllabus = $this->syllabi()->latest()->first();
        return $latestSyllabus ? $latestSyllabus->getLecComponent() : null;
    }

    // Helper: Get LAB component (from latest syllabus)
    // Used in: loadExistingData() - SyllabusWizard
    public function getLabComponent()
    {
        $latestSyllabus = $this->syllabi()->latest()->first();
        return $latestSyllabus ? $latestSyllabus->getLabComponent() : null;
    }

    // Query: Load course with program and in it's outcomes
    // Used in:
    public function scopeWithFullDetails($query)
    {
        return $query->with(['program', 'programOutcomes', 'creator']);
    }

    // Query: Load course for editing
    // Used in:
    public function scopeWithEditData($query)
    {
        return $query->with(['program', 'programOutcomes']);
    }

    // Helper: Sync PO mappings
    // Used in: store() - CourseController
    public function syncPoMappings(array $poMapping)
    {
        // Detach all existing mappings
        $this->programOutcomes()->detach();

        // Attach new mappings with IED levels
        foreach ($poMapping as $outcomeId => $iedLevel) {
            if (in_array($iedLevel, ['I', 'E', 'D'])) {
                $this->programOutcomes()->attach($outcomeId, ['ied' => $iedLevel]);
            }
        }
    }
}
