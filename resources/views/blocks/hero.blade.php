<div class="relative bg-grad-primary text-white">
    <div class="absolute inset-0 bg-neutral-900 bg-opacity-30"></div>
    <div class="relative max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                {{ $data['title'] ?? 'Welcome' }}
            </h1>
            @if(isset($data['subtitle']))
            <p class="mt-6 text-xl max-w-3xl mx-auto">
                {{ $data['subtitle'] }}
            </p>
            @endif
        </div>
    </div>
</div>
