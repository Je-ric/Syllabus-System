@extends('layouts.app')

@section('content')
    <x-header-with-button
        title="Audit Logs"
        description="Track system actions, changes, and approvals"
    />

    <form method="GET" action="{{ route('audit.logs.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <x-form.label for="user_id" variant="title">User</x-form.label>
            <x-form.select id="user_id" name="user_id">
                <option value="">All Users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </x-form.select>
        </div>

        <div>
            <x-form.label for="module" variant="title">Module</x-form.label>
            <x-form.select id="module" name="module">
                <option value="">All Modules</option>
                @foreach ($modules as $module)
                    <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                @endforeach
            </x-form.select>
        </div>

        <div>
            <x-form.label for="action" variant="title">Action</x-form.label>
            <x-form.select id="action" name="action">
                <option value="">All Actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </x-form.select>
        </div>

        <div>
            <x-form.label for="reference_id" variant="title">Reference ID</x-form.label>
            <x-form.input id="reference_id" name="reference_id" type="number" min="1" value="{{ request('reference_id') }}" />
        </div>

        <div>
            <x-form.label for="date_from" variant="date">From</x-form.label>
            <x-form.input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}" />
        </div>

        <div>
            <x-form.label for="date_to" variant="date">To</x-form.label>
            <x-form.input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}" />
        </div>

        <div class="md:col-span-2">
            <x-form.label for="q" variant="title">Keyword</x-form.label>
            <x-form.input id="q" name="q" value="{{ request('q') }}" placeholder="Search description/module/action..." />
        </div>

        <div class="flex items-end gap-2 md:col-span-2 lg:col-span-4">
            <x-button type="submit" variant="save">
                <i class="bx bx-filter-alt mr-1"></i> Apply Filters
            </x-button>
            <x-button href="{{ route('audit.logs.index') }}" variant="cancel">
                Clear
            </x-button>
        </div>
    </form>

    <x-table.container class="mt-6">
        <x-table.table>
            <x-table.head>
                <x-table.row>
                    <x-table.th>Time</x-table.th>
                    <x-table.th>User</x-table.th>
                    <x-table.th>Module</x-table.th>
                    <x-table.th>Action</x-table.th>
                    <x-table.th>Ref</x-table.th>
                    <x-table.th>Description</x-table.th>
                </x-table.row>
            </x-table.head>

            <x-table.body>
                @forelse ($logs as $log)
                    <x-table.row striped hover>
                        <x-table.td class="whitespace-nowrap">{{ optional($log->timestamp)->format('Y-m-d H:i:s') }}</x-table.td>
                        <x-table.td>{{ $log->user?->name ?? 'System' }}</x-table.td>
                        <x-table.td>{{ $log->module }}</x-table.td>
                        <x-table.td>{{ $log->action }}</x-table.td>
                        <x-table.td>{{ $log->reference_id ?? '-' }}</x-table.td>
                        <x-table.td>{{ $log->description ?? '-' }}</x-table.td>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6" message="No audit logs found for the selected filters." />
                @endforelse
            </x-table.body>
        </x-table.table>
    </x-table.container>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
