@if(isset($data['title']))
@php
    $bgColor = $data['background_color'] ?? 'primary';
    $bgClass = match($bgColor) {
        'secondary' => 'bg-secondary',
        'light' => 'bg-neutral-100',
        'dark' => 'bg-neutral-900 text-white',
        default => 'bg-primary'
    };
    $textClass = $bgColor === 'dark' ? 'text-white' : 'text-neutral-900';
    $buttonClass = $bgColor === 'dark' ? 'bg-white text-neutral-900 hover:bg-neutral-100' : 'bg-white text-primary hover:bg-neutral-50';
@endphp
<div class="py-16 {{ $bgClass }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold {{ $textClass }} mb-4">
            {{ $data['title'] }}
        </h2>

        @if(isset($data['subtitle']) && $data['subtitle'])
        <p class="text-xl {{ $bgColor === 'dark' ? 'text-neutral-300' : 'text-neutral-600' }} mb-8">
            {{ $data['subtitle'] }}
        </p>
        @endif

        @if(isset($data['button_text']) && isset($data['button_url']))
        <a href="{{ $data['button_url'] }}"
           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md {{ $buttonClass }} transition-colors">
            {{ $data['button_text'] }}
        </a>
        @endif
    </div>
</div>
@endif
