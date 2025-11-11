@if(isset($data['left_content']) && isset($data['right_content']))
@php
    $layout = $data['layout'] ?? '50-50';
    $layoutClasses = 'lg:grid-cols-12';
    $leftSpan = match($layout) {
        '33-67' => 'lg:col-span-4',
        '67-33' => 'lg:col-span-8',
        default => 'lg:col-span-6',
    };
    $rightSpan = match($layout) {
        '33-67' => 'lg:col-span-8',
        '67-33' => 'lg:col-span-4',
        default => 'lg:col-span-6',
    };
@endphp
@php($eyebrow = $data['eyebrow'] ?? null)
<div class="bg-white h-full flex items-center">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-10 w-full text-center">
        @if($eyebrow)
            <div class="inline-flex items-center justify-center rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-secondary shadow-sm backdrop-blur mb-8">
                {{ $eyebrow }}
            </div>
        @endif
        <div class="grid grid-cols-1 {{ $layoutClasses }} gap-10 lg:gap-14 items-center">
            <div class="flex justify-center {{ $leftSpan }}">
                <div class="prose prose-lg max-w-2xl text-center prose-headings:text-neutral-900 prose-headings:text-center prose-p:text-neutral-700 prose-p:text-center prose-ul:list-none prose-ul:pl-0 prose-li:text-center">
                    {!! $data['left_content'] !!}
                </div>
            </div>
            <div class="flex justify-center {{ $rightSpan }}">
                <div class="w-full max-w-xl text-center space-y-6">
                    {!! $data['right_content'] !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
