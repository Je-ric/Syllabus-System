@extends('layouts.app')

@section('content')
    @livewire('syllabus.syllabus-review-page', ['syllabusId' => $syllabus->id])
@endsection
