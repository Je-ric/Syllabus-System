<?php

namespace App\Livewire\AcademicCalendar;

use App\Models\AcademicCalendar;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
// use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * AcademicCalendarForm
 *
 * Handles both CREATE and EDIT for the AcademicCalendar form page.
 * Replaces the old Alpine $form() / precognition pattern.
 *
 * Real-time validation: every field runs its rules on the 'updated' hook
 * so errors appear as the user types/blurs, not only on submit.
 */
class AcademicCalendarForm extends Component
{
    // ── Public state ──────────────────────────────────────────────────────────

    public bool   $isEdit       = false;
    public string $academicYear = '';   // original AY key when editing

    public string $academic_year = '';
    public string $start_date_1  = '';
    public string $end_date_1    = '';
    public string $start_date_2  = '';
    public string $end_date_2    = '';

    // Controls the confirm-create modal visibility (Alpine listens to this)
    public bool $showConfirmModal = false;

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(
        bool   $isEdit       = false,
        string $academicYear = '',
        array  $originalValues = [],
    ): void {
        $this->isEdit       = $isEdit;
        $this->academicYear = $academicYear;

        if ($isEdit && ! empty($originalValues)) {
            $this->academic_year = $originalValues['academic_year'] ?? '';
            $this->start_date_1  = $originalValues['start_date_1']  ?? '';
            $this->end_date_1    = $originalValues['end_date_1']     ?? '';
            $this->start_date_2  = $originalValues['start_date_2']  ?? '';
            $this->end_date_2    = $originalValues['end_date_2']     ?? '';
        }
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.academic-calendar.form');
    }

    // ── Real-time validation ───────────────────────────────────────────────────
    // Livewire calls updated() on every property change (wire:model.blur fires
    // on field blur so we get per-field validation the moment the user leaves).

    public function updated(string $property): void
    {
        // For academic_year: only check format while typing, not DB uniqueness
        // (uniqueness is checked on submit — avoids a DB hit on every keystroke)
        if ($property === 'academic_year') {
            $this->validateOnly('academic_year', [
                'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            ]);
            return;
        }

        $this->validateOnly($property, $this->rules());

        if ($property === 'start_date_1' && $this->end_date_1 !== '') {
            $this->validateOnly('end_date_1', $this->rules());
        }
        if ($property === 'end_date_1' && $this->start_date_2 !== '') {
            $this->validateOnly('start_date_2', $this->rules());
        }
        if ($property === 'start_date_2' && $this->end_date_2 !== '') {
            $this->validateOnly('end_date_2', $this->rules());
        }
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Called by the "Create Calendar" button.
     * Validates everything first — if valid, opens the confirm modal.
     * Errors appear inline next to each field.
     */
    public function requestCreate(): void
    {
        $this->validate($this->rules());
        $this->showConfirmModal = true;
    }

    /**
     * Called when the user clicks "Confirm & Create" inside the modal.
     */
    public function store(): void
    {
        $validated = $this->validate($this->rules());

        DB::beginTransaction();
        try {
            $sem1 = AcademicCalendar::create([
                'academic_year' => $validated['academic_year'],
                'semester'      => '1st',
                'start_date'    => $validated['start_date_1'],
                'end_date'      => $validated['end_date_1'],
            ]);

            AcademicCalendar::create([
                'academic_year' => $validated['academic_year'],
                'semester'      => '2nd',
                'start_date'    => $validated['start_date_2'],
                'end_date'      => $validated['end_date_2'],
            ]);

            AuditLog::record(
                action: 'created',
                module: 'Academic Calendar',
                referenceId: $sem1->id,
                description: "Created academic calendar for {$validated['academic_year']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->showConfirmModal = false;
            $this->addError('academic_year', 'Failed to create academic calendar. Please try again.');
            return;
        }

        $this->showConfirmModal = false;
        $this->dispatch('lw-toast', type: 'success', message: 'Academic year created successfully. You can now add events.');
        $this->redirectRoute('academic.calendar.events.index', $validated['academic_year']);
    }

    /**
     * Called by the "Update Calendar" button.
     */
    public function update(): void
    {
        $validated = $this->validate($this->rules());

        $semesters = AcademicCalendar::where('academic_year', $this->academicYear)->get();

        if ($semesters->isEmpty()) {
            $this->addError('academic_year', 'Academic year not found.');
            return;
        }

        DB::beginTransaction();
        try {
            $sem1 = $semesters->where('semester', '1st')->first();
            $sem1?->update([
                'academic_year' => $validated['academic_year'],
                'start_date'    => $validated['start_date_1'],
                'end_date'      => $validated['end_date_1'],
            ]);

            $sem2 = $semesters->where('semester', '2nd')->first();
            $sem2?->update([
                'academic_year' => $validated['academic_year'],
                'start_date'    => $validated['start_date_2'],
                'end_date'      => $validated['end_date_2'],
            ]);

            AuditLog::record(
                action: 'updated',
                module: 'Academic Calendar',
                referenceId: $sem1?->id,
                description: "Updated academic calendar from {$this->academicYear} to {$validated['academic_year']}."
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('academic_year', 'Failed to update academic calendar. Please try again.');
            return;
        }

        $this->dispatch('lw-toast', type: 'success', message: 'Academic year updated successfully.');
        $this->redirectRoute('academic.calendars.index');
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
    }

    // ── Validation rules ──────────────────────────────────────────────────────

    private function rules(): array
    {
        // Uniqueness: ignore the current AY when editing
        $ayUnique = Rule::unique('academic_calendars', 'academic_year');
        // if ($this->isEdit && $this->academicYear !== '') {
        //     $ayUnique->where('academic_year', '!=', $this->academicYear);
        // }

        return [
            'academic_year' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{4}$/',
                // Only enforce unique on create, or on edit if AY changed
                ! $this->isEdit || $this->academic_year !== $this->academicYear
                    ? $ayUnique
                    : 'nullable',
            ],
            'start_date_1' => ['required', 'date'],
            'end_date_1'   => ['required', 'date', 'after_or_equal:start_date_1'],
            'start_date_2' => ['required', 'date', 'after:end_date_1'],
            'end_date_2'   => ['required', 'date', 'after_or_equal:start_date_2'],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year.required' => 'Academic year is required.',
            'academic_year.regex'    => 'Use the format YYYY-YYYY (e.g. 2025-2026).',
            'academic_year.unique'   => 'This academic year already exists.',
            'start_date_1.required'  => '1st semester start date is required.',
            'end_date_1.required'    => '1st semester end date is required.',
            'end_date_1.after_or_equal' => 'End date must be on or after the start date.',
            'start_date_2.required'  => '2nd semester start date is required.',
            'start_date_2.after'     => '2nd semester must start after the 1st semester ends.',
            'end_date_2.required'    => '2nd semester end date is required.',
            'end_date_2.after_or_equal' => 'End date must be on or after the start date.',
        ];
    }
}