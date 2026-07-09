<div {{ $attributes->merge([
        'class' => 'bg-white text-sm border border-green-400 rounded-lg p-3 shadow-sm hover:shadow-md transition text-slate-700 flex gap-2'
    ]) }}>
    {{ $slot }}
</div>

