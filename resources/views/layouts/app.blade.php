<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Inventory') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        sidebar: '#020617', // Extremely dark for premium feel
                    },
                    fontFamily: {
                        sans: ['"Inter var"', 'Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        [x-cloak] { display: none !important; }
        .sidebar-item-active { background: linear-gradient(to right, rgba(14, 165, 233, 0.15), transparent); color: #38bdf8; border-right: 2px solid #0ea5e9; }
        .glass-header { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); }
        .sidebar-scroll::-webkit-scrollbar { width: 0px; }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full text-slate-900 antialiased overflow-hidden" x-data="{ mobileMenuOpen: false, notificationsOpen: false }">
    <div class="h-screen flex overflow-hidden">
        @auth
            @include('layouts.partials.sidebar')
            <!-- Mobile Overlay -->
            <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" x-cloak class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden"></div>
        @endauth

        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
            @auth
                @include('layouts.partials.header')
            @endauth

            <!-- Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto p-8 lg:p-12 relative">
                <!-- Toast Notifications -->
                <x-ui.toast />
                <div class="max-w-[1400px] mx-auto pb-12">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
