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

    <div class="mt-6 rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left">Time</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Module</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Ref</th>
                        <th class="px-4 py-3 text-left">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="border-t border-slate-200">
                            <td class="px-4 py-3 whitespace-nowrap">{{ optional($log->timestamp)->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3">{{ $log->module }}</td>
                            <td class="px-4 py-3">{{ $log->action }}</td>
                            <td class="px-4 py-3">{{ $log->reference_id ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $log->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No audit logs found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
