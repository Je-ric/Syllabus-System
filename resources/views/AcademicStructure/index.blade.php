@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    <h1 class="text-xl font-bold">Academic Structure Management</h1>

    {{-- ADD COLLEGE --}}
    @include('AcademicStructure.partials.college-form')

    {{-- COLLEGES --}}
    @foreach ($colleges as $college)
        @include('AcademicStructure.partials.college-item', [
            'college' => $college,
            'departments' => $departments
        ])
    @endforeach

</div>
@endsection

<script>
function toggle(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>


