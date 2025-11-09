@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
    @php
        $canEdit = auth()->check() && auth()->user()->can('update', $page);
    @endphp

    <!-- Floating Editor Toolbar for Admins -->
    @if($canEdit)
        <div id="editor-toolbar" class="fixed bottom-6 right-6 z-50 bg-white rounded-lg shadow-2xl border border-neutral-200">
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-neutral-900">Page Editor</h3>
                    <button id="close-editor" class="text-neutral-400 hover:text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-2">
                    <button id="add-block-btn" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Block</span>
                    </button>
                    <button id="save-page-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <span>Save Changes</span>
                    </button>
                    <button id="cancel-editing-btn" class="w-full bg-neutral-600 hover:bg-neutral-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Cancel
                    </button>
                </div>
                <div id="editor-status" class="mt-3 text-xs text-neutral-500 text-center"></div>
            </div>
        </div>

        <!-- Inline Editor Assets -->
        @vite(['resources/js/inline-editor.js'])
    @endif

    <!-- Page Content Blocks (Editable Area) -->
    <div id="editable-content" class="min-h-screen" data-page-slug="{{ $page->slug }}">
        @if($page->blocks)
            @foreach($page->blocks as $index => $block)
                @php
                    $blockType = $block['type'] ?? 'text';
                    $blockData = $block['data'] ?? [];
                    $template = \App\Services\BlockSchema::getBlockTemplate($blockType);
                @endphp

                <div class="block-wrapper relative group" data-block-index="{{ $index }}" data-block-type="{{ $blockType }}">
                    @if($canEdit)
                        <!-- Edit Overlay (visible on hover) -->
                        <div class="block-controls absolute inset-0 bg-blue-500 bg-opacity-0 group-hover:bg-opacity-10 transition-all pointer-events-none">
                            <div class="absolute top-2 right-2 flex space-x-2 pointer-events-auto opacity-0 group-hover:opacity-100 transition-opacity">
                                <button class="edit-block-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium shadow-lg">
                                    Edit
                                </button>
                                <button class="move-up-btn bg-neutral-600 hover:bg-neutral-700 text-white px-3 py-1 rounded text-xs font-medium shadow-lg">
                                    ↑
                                </button>
                                <button class="move-down-btn bg-neutral-600 hover:bg-neutral-700 text-white px-3 py-1 rounded text-xs font-medium shadow-lg">
                                    ↓
                                </button>
                                <button class="delete-block-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium shadow-lg">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($template && \View::exists($template))
                        @include($template, ['data' => $blockData])
                    @else
                        <div class="py-4 px-4 bg-neutral-100 border-l-4 border-primary">
                            <p class="text-neutral-900">Unknown block type: {{ $blockType }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="py-16 bg-white">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl font-bold text-neutral-900 mb-4">{{ $page->title }}</h1>
                    <p class="text-xl text-neutral-600">This page has no content blocks yet.</p>
                    @if($canEdit)
                        <button class="mt-6 bg-primary hover:bg-secondary text-white px-6 py-3 rounded-lg font-medium" onclick="document.getElementById('add-block-btn').click()">
                            Add Your First Block
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Block Edit Modal -->
    @if($canEdit)
        <div id="block-edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="p-6 border-b border-neutral-200 flex items-center justify-between">
                    <h2 id="modal-title" class="text-2xl font-bold text-neutral-900">Edit Block</h2>
                    <button id="close-modal-btn" class="text-neutral-400 hover:text-neutral-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modal-content" class="p-6 overflow-y-auto flex-1">
                    <!-- Dynamic content will be injected here -->
                </div>
                <div class="p-6 border-t border-neutral-200 flex justify-end space-x-3">
                    <button id="modal-cancel-btn" class="px-4 py-2 border border-neutral-300 rounded-md text-neutral-700 hover:bg-neutral-50 font-medium">
                        Cancel
                    </button>
                    <button id="modal-save-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium">
                        Save Block
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        @if($canEdit)
            window.pageData = {
                slug: '{{ $page->slug }}',
                canEdit: true,
                csrfToken: '{{ csrf_token() }}',
                apiUrl: '{{ url("/api/pages/{$page->slug}") }}',
                csrfCookieUrl: '{{ url("/sanctum/csrf-cookie") }}',
                blocks: @json($page->blocks ?? [])
            };
        @endif
    </script>
@endsection
