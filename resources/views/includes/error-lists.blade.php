@if ($errors->any())
    <x-feedback-status.alert type="error" title="Please fix the following errors">
        <ul class="mt-1 list-disc list-inside space-y-0.5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-feedback-status.alert>
@endif
