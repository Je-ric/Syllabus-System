@extends('layouts.app')

@section('content')

    @php
        $dashboardTitles = [
            'admin'   => 'System Dashboard',
            'chair'   => 'Department Dashboard',
            'dean'    => 'College Dashboard',
            'faculty' => 'My Syllabi Dashboard',
        ];
        $dashboardDescriptions = [
            'admin'   => 'System-wide overview of users, academic structure, and syllabi',
            'chair'   => 'Department-level academic health, mappings, and syllabus progress',
            'dean'    => 'College-level academic completeness and department summary',
            'faculty' => 'Track your syllabi, drafts, and approvals',
        ];
        $primaryRole = match ($data['type']) {
            'admin'   => 'admin',
            'chair'   => 'chair',
            'dean'    => 'dean',
            'faculty' => 'faculty',
            default   => $user->roles->first()?->name,
        };
    @endphp

    <x-layout.page-header
        icon="bx-grid-alt"
        :title="$dashboardTitles[$data['type']] ?? 'Dashboard'"
        :desc="$dashboardDescriptions[$data['type']] ?? 'Welcome to CSMS'">
        @if ($primaryRole)
            <x-feedback-status.status-indicator :status="$primaryRole" />
        @endif
    </x-layout.page-header>

    <x-layout.panel>
        <div class="space-y-4">

            @include('System.Dashboard.partials.active-semester', ['activeSemester' => $data['active_semester']])

            @switch($data['type'])
                @case('admin')
                    @include('System.Dashboard.partials.admin')
                    @break

                @case('chair')
                    @include('System.Dashboard.partials.chair')
                    @break

                @case('dean')
                    @include('System.Dashboard.partials.dean')
                    @break

                @case('faculty')
                    @include('System.Dashboard.partials.faculty')
                    @break

                @default
                    <x-layout.card-section title="Welcome" icon="bx-home">
                        <x-feedback-status.empty-state
                            icon="bx-home"
                            title="Welcome back, {{ $user->name }}"
                            message="Use the sidebar to navigate to your available modules.">
                            @if ($user->hasRole('faculty') || $user->hasRole('ovpaa'))
                                <x-ui.button href="{{ route('syllabus.index') }}" variant="primary">
                                    <i class="bx bx-notepad text-base leading-none"></i> Go to Syllabi
                                </x-ui.button>
                            @endif
                        </x-feedback-status.empty-state>
                    </x-layout.card-section>
            @endswitch

        </div>
    </x-layout.panel>

@endsection
