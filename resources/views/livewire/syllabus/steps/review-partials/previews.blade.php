<x-wizard.section 
    title="Previews" 
    icon="show" 
    color="slate">
    <div class="flex flex-col sm:flex-row gap-2">
        <x-button href="{{ route('syllabus.preview.complete', ['syllabus' => $syllabus->id]) }}" 
            variant="outline"
            target="_blank" 
            rel="noopener" 
            class="flex-1 justify-center">
            <i class="bx bx-file-blank"></i> Complete
        </x-button>
        <x-button href="{{ route('syllabus.preview.abridged', ['syllabus' => $syllabus->id]) }}" 
            variant="outline"
            target="_blank" 
            rel="noopener" 
            class="flex-1 justify-center">
            <i class="bx bx-file"></i> Abridged
        </x-button>
        <x-button href="{{ route('syllabus.preview.assessment', ['syllabus' => $syllabus->id]) }}" 
            variant="outline"
            target="_blank" 
            rel="noopener" 
            class="flex-1 justify-center">
            <i class="bx bx-clipboard"></i> Assessment Plan
        </x-button>
    </div>
</x-wizard.section>
