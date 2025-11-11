@php
    $backgroundClass = $data['background_style'] ?? $data['background_class'] ?? 'bg-white';
    $maxWidth = $data['max_width'] ?? 'max-w-3xl';
    if ($maxWidth === 'full') {
        $maxWidth = 'max-w-none';
    }
    $paddingClass = $data['padding_class'] ?? 'px-4 py-12 sm:px-6 lg:px-8';
@endphp
<div class="{{ $backgroundClass }} w-full flex items-center justify-center">
    <div class="{{ $maxWidth }} mx-auto {{ $paddingClass }} text-center">
        @if(!empty($data['eyebrow']))
            <div class="inline-flex items-center justify-center rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-secondary shadow-sm backdrop-blur mb-6">
                {{ $data['eyebrow'] }}
            </div>
        @endif
        <div class="prose prose-lg mx-auto text-center prose-headings:text-neutral-900 prose-headings:text-center prose-p:text-neutral-700 prose-p:text-center prose-strong:text-neutral-900 prose-ul:list-none prose-ul:pl-0 prose-li:text-center">
            {!! $data['content'] ?? '' !!}
        </div>
    </div>
</div>
