@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
    <!-- Admin Edit Button -->
    @auth
        @can('update', $page)
        <div class="fixed bottom-4 right-4 z-50">
            <a href="#"
               class="bg-primary hover:bg-secondary text-white font-medium py-2 px-4 rounded-lg shadow-lg transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Page</span>
            </a>
        </div>
        @endcan
    @endauth

    <!-- Page Content Blocks -->
    @if($page->blocks)
        @foreach($page->blocks as $block)
            @php
                $blockType = $block['type'] ?? 'text';
                $blockData = $block['data'] ?? [];
                $template = \App\Services\BlockSchema::getBlockTemplate($blockType);
            @endphp

            @if($template && \View::exists($template))
                @include($template, ['data' => $blockData])
            @else
                <!-- Fallback for unknown block types -->
                <div class="py-4 px-4 bg-neutral-100 border-l-4 border-primary">
                    <p class="text-neutral-900">Unknown block type: {{ $blockType }}</p>
                </div>
            @endif
        @endforeach
    @else
        <!-- Default content when no blocks -->
        <div class="py-16 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-bold text-neutral-900 mb-4">{{ $page->title }}</h1>
                <p class="text-xl text-neutral-600">This page has no content blocks yet.</p>
            </div>
        </div>
    @endif
@endsection
