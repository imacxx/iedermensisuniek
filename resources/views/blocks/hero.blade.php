<div class="relative bg-grad-primary text-white h-full flex items-center justify-center overflow-hidden">
    @if(!empty($data['background_image']))
        <!-- Custom background image overlay -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $data['background_image'] }}');"></div>
        <div class="absolute inset-0 bg-neutral-900 bg-opacity-50"></div>
    @endif
    
    <div class="relative max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 w-full z-10">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl drop-shadow-lg">
                {{ $data['title'] ?? 'Welcome' }}
            </h1>
            @if(isset($data['subtitle']))
            <p class="mt-6 text-xl max-w-3xl mx-auto drop-shadow-md">
                {{ $data['subtitle'] }}
            </p>
            @endif
        </div>
    </div>
</div>
