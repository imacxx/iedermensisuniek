<div class="bg-neutral-100 h-full flex items-center">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 w-full">
        @if(isset($data['title']))
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-neutral-900 sm:text-4xl">
                {{ $data['title'] }}
            </h2>
        </div>
        @endif

        @if(isset($data['items']) && is_array($data['items']))
        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($data['items'] as $item)
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-100 hover:shadow-md transition-shadow">
                @if(isset($item['title']))
                <h3 class="text-lg font-semibold text-neutral-900 mb-2">
                    {{ $item['title'] }}
                </h3>
                @endif

                @if(isset($item['description']))
                <p class="text-neutral-500">
                    {{ $item['description'] }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
