<?php

namespace App\Livewire\Syllabus\Steps;

use App\Models\Syllabus;
use Livewire\Attributes\On;
use Livewire\Component;

class CoPoMappingStep extends Component
{
    public int $syllabusId;
    public bool $isLoaded = false;
    public $course;
    public array $courseOutcomes = [];
    public array $coPoMappings = [];

    public function mount(int $syllabusId): void
    {
        $this->syllabusId = $syllabusId;
        $this->loadData();
    }

    #[On('syllabus-step-changed')]
    public function onStepChanged(string $step): void
    {
        if ($step !== 'co_po_mapping') {
            return;
        }

        $this->loadData(force: true);
    }

    #[On('syllabus-course-outcomes-updated')]
    public function onCourseOutcomesUpdated(): void
    {
        $this->loadData(force: true);
    }

    #[On('syllabus-save-step')]
    public function onSaveRequested(string $step): void
    {
        if ($step !== 'co_po_mapping') {
            return;
        }

        if ($this->saveMappings()) {
            $this->dispatch('syllabus-step-saved', step: 'co_po_mapping');
        }
    }

    public function updatedCoPoMappings(): void
    {
        if (!$this->isLoaded) {
            return;
        }

        $this->dispatch('syllabus-step-dirty', step: 'co_po_mapping', dirty: true);
        if ($this->saveMappings()) {
            $this->dispatch('syllabus-step-saved', step: 'co_po_mapping');
        }
    }

    public function render()
    {
        return view('livewire.syllabus.steps.co-po-mapping', [
            'course' => $this->course,
        ]);
    }

    private function loadData(bool $force = false): void
    {
        if ($this->isLoaded && !$force) {
            return;
        }

        $syllabus = Syllabus::query()
            ->with([
                'course.program.outcomes',
                'course.programOutcomes',
                'courseOutcomes.programOutcomes',
            ])
            ->findOrFail($this->syllabusId);

        $this->course = $syllabus->course;
        $this->courseOutcomes = $syllabus->courseOutcomes
            ->map(fn($co) => [
                'id' => $co->id,
                'co_code' => $co->co_code,
                'description' => $co->description,
            ])->values()->all();

        $mappings = [];
        foreach ($syllabus->courseOutcomes as $co) {
            $mappings[$co->id] = [];
            foreach ($co->programOutcomes as $po) {
                $mappings[$co->id][$po->id] = true;
            }
        }
        $this->coPoMappings = $mappings;
        $this->isLoaded = true;
    }

    private function saveMappings(): bool
    {
        if (empty($this->coPoMappings)) {
            return false;
        }

        $syllabus = Syllabus::query()->with('courseOutcomes')->findOrFail($this->syllabusId);
        $outcomesById = $syllabus->courseOutcomes->keyBy('id');
        $saved = false;

        foreach ($this->coPoMappings as $coId => $poMappings) {
            $coId = (int) $coId;
            if ($coId <= 0 || !$outcomesById->has($coId) || !is_array($poMappings)) {
                continue;
            }

            $syncData = [];
            foreach ($poMappings as $poId => $isConnected) {
                if (!$isConnected || !is_numeric($poId)) {
                    continue;
                }
                $syncData[(int) $poId] = ['ied' => 'I'];
            }

            $outcomesById[$coId]->programOutcomes()->sync($syncData);
            $saved = true;
        }

        return $saved;
    }
}
