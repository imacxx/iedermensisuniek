<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', \App\Models\Setting::getSiteTitle()) - {{ \App\Models\Setting::getSiteTitle() }}</title>

    @if(View::hasSection('meta_description'))
    <meta name="description" content="@yield('meta_description')">
    @endif

    @if(View::hasSection('meta_keywords'))
    <meta name="keywords" content="@yield('meta_keywords')">
    @endif

    @if(\App\Models\Setting::getFavicon())
    <link rel="icon" href="{{ \App\Models\Setting::getFavicon() }}" type="image/x-icon">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-neutral-100 text-neutral-900">
    <!-- Header -->
    <header class="bg-primary shadow-sm border-b border-secondary/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4 md:py-5">
                <!-- Logo -->
                <div class="flex items-center">
                    @php($logo = \App\Models\Setting::getLogoImage())
                    @php($logoUrl = $logo ? (filter_var($logo, FILTER_VALIDATE_URL) ? $logo : asset(ltrim($logo, '/'))) : null)
                    <a href="/" class="flex items-center">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}"
                                 alt="{{ \App\Models\Setting::getSiteTitle() }}"
                                 class="max-h-16 w-auto">
                            <span class="sr-only">{{ \App\Models\Setting::getSiteTitle() }}</span>
                        @else
                            <span class="text-2xl font-semibold text-secondary hover:text-neutral-900 transition-colors">
                                {{ \App\Models\Setting::getSiteTitle() }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    @foreach(\App\Models\Setting::getPrimaryNavigation() as $item)
                        <a href="{{ $item['url'] }}"
                           class="text-secondary hover:text-neutral-900 transition-colors font-semibold tracking-wide uppercase text-sm">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-secondary hover:text-neutral-900 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="flex flex-col space-y-2 pt-4 border-t border-secondary/40">
                    @foreach(\App\Models\Setting::getPrimaryNavigation() as $item)
                        <a href="{{ $item['url'] }}"
                           class="text-secondary hover:text-neutral-900 transition-colors font-semibold uppercase tracking-wide py-2">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 gap-8">
                <!-- Footer Text -->
                <div>
                    <p class="text-neutral-300">
                        {{ \App\Models\Setting::getFooterText() }}
                    </p>
                </div>
            </div>

            <!-- Secondary Navigation -->
            @if(\App\Models\Setting::getSecondaryNavigation())
            <div class="border-t border-neutral-800 mt-8 pt-8">
                <div class="flex flex-wrap justify-center space-x-6">
                    @foreach(\App\Models\Setting::getSecondaryNavigation() as $item)
                        <a href="{{ $item['url'] }}"
                           class="text-neutral-400 hover:text-primary transition-colors text-sm">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>