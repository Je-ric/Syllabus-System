@props([
    'id',
    'maxWidth' => 'max-w-2xl',
    'width'    => 'w-full sm:w-11/12',
    'variant'  => 'default',
    'class'    => '',
])

@php
    /*
    ┌─────────────────────────────────────────────────────────────────────────┐
    │  modal.dialog — Design.md compliant                                     │
    │  • Radius  : --radius-lg (16px) = rounded-2xl                           │
    │  • Border  : --border-default #E3E8EB (Grey 400)                        │
    │  • Accent  : 2.5 px top gradient rail using design-token colours        │
    │  • Shadow  : per-variant tinted multi-layer elevation                   │
    └─────────────────────────────────────────────────────────────────────────┘
    */
    $accentGradient = match($variant) {
        'delete'  => 'linear-gradient(90deg,#E52F28 0%,#F45855 55%,rgba(229,47,40,0) 100%)',
        'warning' => 'linear-gradient(90deg,#F5B126 0%,#FFC646 55%,rgba(245,177,38,0) 100%)',
        'info'    => 'linear-gradient(90deg,#3197D6 0%,#71BFF1 55%,rgba(49,151,214,0) 100%)',
        default   => 'linear-gradient(90deg,#00D88B 0%,#00C075 50%,rgba(0,216,139,0) 100%)',
    };
    $shadowStyle = match($variant) {
        'delete'  => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(229,47,40,0.12),0 12px 40px rgba(229,47,40,0.10);',
        'warning' => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(245,177,38,0.12),0 12px 40px rgba(245,177,38,0.10);',
        'info'    => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(49,151,214,0.12),0 12px 40px rgba(49,151,214,0.10);',
        default   => 'box-shadow:0 1px 2px rgba(16,24,40,0.04),0 4px 16px rgba(0,216,139,0.10),0 12px 40px rgba(0,150,95,0.10);',
    };
@endphp

<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-2xl bg-white flex flex-col
                border border-[#E3E8EB] relative {{ $class }}"
        style="{{ $shadowStyle }} min-width: min(540px, 94vw);">

        {{-- 2.5 px brand accent rail — Design.md gradient, fades right --}}
        <div class="absolute inset-x-0 top-0 h-[2.5px] rounded-t-2xl pointer-events-none z-10"
             style="background:{{ $accentGradient }};" aria-hidden="true"></div>

        {{ $slot }}
    </div>
</dialog>
