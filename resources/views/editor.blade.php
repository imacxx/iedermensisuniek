<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Editor - {{ $slug }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/editor.js'])
</head>
<body class="antialiased bg-gray-50">
    <div id="app">
        <div class="min-h-screen bg-gray-50">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Page Editor</h1>
                            <p class="text-sm text-gray-600">Editing: {{ $slug }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <button id="loadBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Load Page
                            </button>
                            <button id="saveBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Save Page
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Editor Area -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Blocks Editor -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Page Blocks</h2>
                        <div id="blocks-editor" class="space-y-4">
                            <!-- Blocks will be loaded here -->
                        </div>
                        <button id="addBlockBtn" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Add Block
                        </button>
                    </div>

                    <!-- Preview -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Preview</h2>
                        <div id="preview" class="border rounded-md p-4 min-h-96 bg-gray-50">
                            <!-- Preview will be rendered here -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Block Templates -->
    <script type="text/template" id="hero-block-template">
        <div class="block-item border rounded-md p-4 bg-blue-50" data-type="hero">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold">Hero Block</h3>
                <button class="remove-block text-red-600 hover:text-red-800">×</button>
            </div>
            <div class="space-y-3">
                <input type="text" class="block-title w-full p-2 border rounded" placeholder="Hero Title" />
                <textarea class="block-subtitle w-full p-2 border rounded" placeholder="Hero Subtitle" rows="2"></textarea>
            </div>
        </div>
    </script>

    <script type="text/template" id="text-block-template">
        <div class="block-item border rounded-md p-4 bg-green-50" data-type="text">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold">Text Block</h3>
                <button class="remove-block text-red-600 hover:text-red-800">×</button>
            </div>
            <textarea class="block-content w-full p-2 border rounded" placeholder="Enter your content here..." rows="6"></textarea>
        </div>
    </script>

    <script type="text/template" id="features-block-template">
        <div class="block-item border rounded-md p-4 bg-purple-50" data-type="features">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-semibold">Features Block</h3>
                <button class="remove-block text-red-600 hover:text-red-800">×</button>
            </div>
            <input type="text" class="block-title w-full p-2 border rounded mb-3" placeholder="Features Title" />
            <div class="features-list space-y-2">
                <!-- Feature items will be added here -->
            </div>
            <button class="add-feature mt-3 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                Add Feature
            </button>
        </div>
    </script>

    <script type="text/template" id="feature-item-template">
        <div class="feature-item flex space-x-2">
            <input type="text" class="feature-title flex-1 p-2 border rounded" placeholder="Feature Title" />
            <input type="text" class="feature-description flex-1 p-2 border rounded" placeholder="Feature Description" />
            <button class="remove-feature text-red-600 hover:text-red-800 px-2">×</button>
        </div>
    </script>

    <script>
        window.pageSlug = '{{ $slug }}';
        window.apiBaseUrl = '{{ url("/api") }}';
        window.csrfCookieUrl = '{{ url("/sanctum/csrf-cookie") }}';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
</body>
</html>
