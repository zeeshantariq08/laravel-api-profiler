<!DOCTYPE html>
<html lang="en" x-data="{ dark: localStorage.getItem('dark') === 'true' }" :class="{ 'dark': dark }" x-init="$watch('dark', val => localStorage.setItem('dark', val))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel API Profiler</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                            950: '#030712',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="flex h-screen font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">

{{-- Sidebar --}}
<aside class="w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col shadow-sm z-10 transition-colors duration-300">
    {{-- Header --}}
    <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-600 p-1.5 rounded-lg shadow-lg shadow-indigo-500/30">
                <span class="material-icons-round text-white text-xl">bolt</span>
            </div>
            <span class="font-bold text-lg tracking-tight">API Profiler</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Monitoring</p>
        
        <a href="{{ url('/api-profiler/dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
           {{ request()->is('api-profiler/dashboard*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/50 dark:hover:text-gray-200' }}">
            <span class="material-icons-round text-[20px] {{ request()->is('api-profiler/dashboard*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300' }}">dashboard</span>
            Dashboard
        </a>

        <a href="{{ url('/api-profiler/requests') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
           {{ request()->is('api-profiler/requests*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/50 dark:hover:text-gray-200' }}">
            <span class="material-icons-round text-[20px] {{ request()->is('api-profiler/requests*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300' }}">list</span>
            Requests
        </a>

        <a href="{{ url('/api-profiler/routes') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
           {{ request()->is('api-profiler/routes*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/50 dark:hover:text-gray-200' }}">
            <span class="material-icons-round text-[20px] {{ request()->is('api-profiler/routes*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300' }}">alt_route</span>
            Routes
        </a>

        <a href="{{ url('/api-profiler/alerts') }}" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
           {{ request()->is('api-profiler/alerts*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/50 dark:hover:text-gray-200' }}">
            <span class="material-icons-round text-[20px] {{ request()->is('api-profiler/alerts*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-gray-300' }}">notifications</span>
            Alerts
        </a>
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        <button @click="dark = !dark" 
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <span class="material-icons-round text-lg" x-text="dark ? 'light_mode' : 'dark_mode'"></span>
            <span x-text="dark ? 'Light Mode' : 'Dark Mode'"></span>
        </button>
    </div>
</aside>

{{-- Main content --}}
<main class="flex-1 h-full overflow-y-auto bg-gray-50 dark:bg-gray-950 scroll-smooth">
    <div class="max-w-7xl mx-auto p-8">
        @yield('content')
    </div>
</main>

</body>
</html>
