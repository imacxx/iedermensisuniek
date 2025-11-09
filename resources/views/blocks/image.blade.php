@if(isset($data['image_url']) && $data['image_url'])
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <img src="{{ $data['image_url'] }}"
             alt="{{ $data['alt_text'] ?? '' }}"
             class="w-full rounded-lg shadow-lg" />
        @if(isset($data['caption']) && $data['caption'])
        <p class="text-center mt-4 text-gray-600 italic">
            {{ $data['caption'] }}
        </p>
        @endif
    </div>
</div>
@endif
