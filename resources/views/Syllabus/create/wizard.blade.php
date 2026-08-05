@extends('layouts.app')

@section('content')
    <livewire:syllabus.syllabus-wizard :syllabusId="$syllabusId" :courseId="$courseId" />
@endsection
