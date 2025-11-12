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
    $rightVariant = $data['right_variant'] ?? 'plain';
    $rightTitle = $data['right_title'] ?? null;
    $rightSubtitle = $data['right_subtitle'] ?? null;
    $rightButtonText = $data['right_button_text'] ?? null;
    $rightButtonLink = $data['right_button_link'] ?? null;
    $rightImage = $data['right_image'] ?? null;
    $rightImageAlt = $data['right_image_alt'] ?? null;
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
                @switch($rightVariant)
                    @case('card')
                        <div class="w-full max-w-xl">
                            <div class="rounded-3xl border border-neutral-200 bg-neutral-50/80 px-8 py-10 shadow-sm text-center space-y-6">
                                @if($rightTitle)
                                    <h3 class="text-2xl font-semibold text-neutral-900">{{ $rightTitle }}</h3>
                                @endif
                                @if($rightSubtitle)
                                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-secondary">{{ $rightSubtitle }}</p>
                                @endif
                                @if(!empty($data['right_content']))
                                    <div class="prose prose-lg mx-auto text-center prose-p:text-neutral-700 prose-a:text-secondary">
                                        {!! $data['right_content'] !!}
                                    </div>
                                @endif
                                @if($rightButtonText && $rightButtonLink)
                                    <div>
                                        <a href="{{ $rightButtonLink }}"
                                           class="inline-flex items-center justify-center rounded-full bg-secondary px-6 py-3 text-white font-semibold shadow-sm hover:bg-secondary/90 transition-colors">
                                            {{ $rightButtonText }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @break

                    @case('profile')
                        <div class="w-full max-w-md flex flex-col items-center text-center space-y-6">
                            @if($rightImage)
                                <div class="relative h-48 w-48 rounded-full border-[12px] border-primary/50 shadow-lg overflow-hidden">
                                    <img src="{{ $rightImage }}" alt="{{ $rightImageAlt ?? $rightTitle ?? '' }}" class="h-full w-full object-cover">
                                </div>
                            @endif
                            <div class="space-y-2">
                                @if($rightTitle)
                                    <h3 class="text-xl font-semibold text-neutral-900">{{ $rightTitle }}</h3>
                                @endif
                                @if($rightSubtitle)
                                    <p class="text-neutral-600">{{ $rightSubtitle }}</p>
                                @endif
                                @if(!empty($data['right_content']))
                                    <div class="prose prose-base mx-auto text-center prose-p:text-neutral-600">
                                        {!! $data['right_content'] !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @break

                    @default
                        <div class="prose prose-lg max-w-2xl mx-auto text-center prose-headings:text-neutral-900 prose-headings:text-center prose-p:text-neutral-700 prose-p:text-center prose-ul:list-none prose-ul:pl-0 prose-li:text-center">
                            {!! $data['right_content'] !!}
                        </div>
                        @break
                @endswitch
            </div>
        </div>
    </div>
</div>
@endif
