{{--
    Intentionally empty.
    Toast notifications are handled by two global mechanisms:
    - Session flash   → layout renders <x-feedback-status.toast> on page load
    - Livewire events → $this->dispatch('lw-toast', ...) caught by the layout's Alpine listener
    No local flash partial is needed here.
--}}
