@if(isset($data['title']))
@php
    $bgColor = $data['background_color'] ?? 'primary';
    $bgClass = match($bgColor) {
        'secondary' => 'bg-gradient-to-r from-secondary via-secondary/90 to-neutral-900 text-white',
        'light' => 'bg-neutral-100',
        'dark' => 'bg-neutral-900 text-white',
        default => 'bg-gradient-to-r from-primary via-primary/90 to-secondary text-white'
    };
    $isDark = \Illuminate\Support\Str::contains($bgClass, 'text-white');
    $textClass = $isDark ? 'text-white' : 'text-neutral-900';
    $buttonClass = $isDark
        ? 'bg-white/95 text-secondary hover:bg-white transition-colors'
        : 'bg-white text-primary hover:bg-neutral-50';
@endphp
<div class="{{ $bgClass }} h-full flex items-center justify-center overflow-hidden relative">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(circle_at_top,_#ffffff_0%,_transparent_45%)]"></div>
    <div class="relative max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-3xl sm:text-4xl font-bold {{ $textClass }}">
            {{ $data['title'] }}
        </h2>

        @if(isset($data['subtitle']) && $data['subtitle'])
        <p class="text-lg sm:text-xl {{ $isDark ? 'text-white/80' : 'text-neutral-600' }}">
            {{ $data['subtitle'] }}
        </p>
        @endif

        @if(isset($data['button_text']) && isset($data['button_url']))
        <a href="{{ $data['button_url'] }}"
           class="inline-flex items-center gap-2 px-6 py-3 border border-transparent text-base font-semibold rounded-full shadow-sm {{ $buttonClass }}">
            {{ $data['button_text'] }}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>
</div>
@endif
