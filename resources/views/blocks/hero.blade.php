@php($backgroundImage = $data['background_image'] ?? null)
<section class="relative overflow-hidden text-neutral-900">
    <div class="absolute inset-0">
        @if($backgroundImage)
            <img src="{{ $backgroundImage }}"
                 alt=""
                 class="h-full w-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-b from-white/95 via-white/85 to-primary/30"></div>
        @else
            <div class="h-full w-full bg-gradient-to-b from-white via-white to-primary/25"></div>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-primary/40 via-primary/20 to-transparent pointer-events-none"></div>
    </div>

    <div class="relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24">
            <div class="max-w-3xl mx-auto space-y-6 text-center">
                <div class="inline-flex items-center justify-center rounded-full bg-white/70 px-4 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-secondary shadow-sm backdrop-blur mx-auto">
                    {{ $data['eyebrow'] ?? 'Ieder Mens Is Uniek' }}
                </div>

                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl text-secondary drop-shadow-sm">
                    {{ $data['title'] ?? 'Welkom' }}
                </h1>

                @if(isset($data['subtitle']))
                <p class="text-lg sm:text-xl text-neutral-700 leading-relaxed">
                    {{ $data['subtitle'] }}
                </p>
                @endif

                @if(isset($data['button_text']) && isset($data['button_url']))
                <div class="flex justify-center">
                    <a href="{{ $data['button_url'] }}"
                       class="inline-flex items-center gap-2 rounded-full bg-secondary px-5 py-2 text-white font-medium shadow-md hover:bg-secondary/90 transition-colors">
                        {{ $data['button_text'] }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
