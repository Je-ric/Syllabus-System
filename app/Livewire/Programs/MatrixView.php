<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use Livewire\Component;
use Livewire\Attributes\On;

class MatrixView extends Component
{
    public Program $program;
    public array $peos    = [];
    public array $pos     = [];
    public array $mapping = [];

    public function mount(Program $program): void
    {
        $this->program = $program;
        $this->load();
    }

    private function load(): void
    {
        $this->peos = $this->program->peos()
            ->orderBy('peo_code')
            ->get(['id', 'peo_code', 'peo_text'])
            ->toArray();

        $this->pos = $this->program->outcomes()
            ->orderBy('po_code')
            ->get(['id', 'po_code', 'po_text'])
            ->toArray();

        $this->mapping = $this->program->outcomes()->with('peos')->get()
            ->mapWithKeys(fn($po) => [$po->id => $po->peos->pluck('id')->all()])
            ->all();
    }

    #[On('peosUpdated')]
    public function onPeosUpdated(int $programId): void
    {
        if ($this->program->id === $programId) $this->load();
    }

    #[On('pos-saved')]
    public function onPosSaved(): void
    {
        $this->load();
    }

    public function render()
    {
        return view('livewire.programs.matrix-view');
    }
}
