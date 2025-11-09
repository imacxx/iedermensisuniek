@if(isset($data['left_content']) && isset($data['right_content']))
@php
    $layout = $data['layout'] ?? '50-50';
    $layoutClasses = match($layout) {
        '33-67' => 'lg:grid-cols-1 lg:grid-cols-3',
        '67-33' => 'lg:grid-cols-2 lg:grid-cols-3',
        default => 'lg:grid-cols-2'
    };
@endphp
<div class="bg-white h-full flex items-center">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 {{ $layoutClasses }} gap-8 lg:gap-12">
            <div class="prose prose-lg max-w-none">
                {!! $data['left_content'] !!}
            </div>
            <div class="prose prose-lg max-w-none">
                {!! $data['right_content'] !!}
            </div>
        </div>
    </div>
</div>
@endif
