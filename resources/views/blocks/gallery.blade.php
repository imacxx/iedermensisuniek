@if(isset($data['images']) && is_array($data['images']) && count($data['images']) > 0)
<div class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 {{ 'grid-cols-' . ($data['columns'] ?? 3) }} md:grid-cols-{{ $data['columns'] ?? 3 }}">
            @foreach($data['images'] as $image)
            @if(isset($image['url']) && $image['url'])
            <div class="aspect-square">
                <img src="{{ $image['url'] }}"
                     alt="{{ $image['alt'] ?? '' }}"
                     class="w-full h-full object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow" />
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif
