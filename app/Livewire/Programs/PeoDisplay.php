<?php

namespace App\Livewire\Programs;

use App\Models\Program;
use Livewire\Component;
use Livewire\Attributes\On;

class PeoDisplay extends Component
{
    public Program $program;
    public array $peos = [];

    public function mount(Program $program): void
    {
        $this->program = $program;
        $this->loadPeos();
    }

    private function loadPeos(): void
    {
        $this->peos = $this->program->peos()
            ->orderBy('peo_code')
            ->get(['peo_code', 'peo_text'])
            ->toArray();
    }

    #[On('peosUpdated')] // listener
    public function refreshPeos(int $programId): void
    {
        if ($this->program->id !== $programId) {
            return;
        }

        $this->loadPeos();
    }

    public function render()
    {
        return view('livewire.programs.peo-display');
    }
}
