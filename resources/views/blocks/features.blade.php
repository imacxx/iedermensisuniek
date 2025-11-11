@php($eyebrow = $data['eyebrow'] ?? 'Aanbod')
<div class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-neutral-100 via-white to-primary/10"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.6)_0%,_transparent_55%)]"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 w-full space-y-10">
        @if(isset($data['title']))
        <div class="text-center space-y-3">
            <span class="inline-flex items-center rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-secondary shadow-sm backdrop-blur">
                {{ $eyebrow }}
            </span>
            <h2 class="text-3xl font-bold tracking-tight text-neutral-900 sm:text-4xl">
                {{ $data['title'] }}
            </h2>
        </div>
        @endif

        @if(isset($data['items']) && is_array($data['items']))
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 text-center">
            @foreach($data['items'] as $item)
            <div class="group bg-white/90 backdrop-blur rounded-2xl shadow-sm border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-200 p-6 flex flex-col items-center space-y-3 text-center">
                @if(isset($item['title']))
                <h3 class="text-lg font-semibold text-neutral-900">
                    {{ $item['title'] }}
                </h3>
                @endif

                @if(isset($item['description']))
                <div class="text-neutral-600 leading-relaxed [&_a]:text-secondary [&_a]:font-semibold [&_a]:inline-flex [&_a]:items-center [&_a]:gap-1 [&_a]:transition-colors group-hover:[&_a]:text-neutral-900">
                    {!! $item['description'] !!}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
