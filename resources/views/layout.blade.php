<!DOCTYPE html>
<html lang="en" x-data="{ dark: false }" :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <title>Laravel API Profiler</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen font-sans transition-colors duration-300 dark:bg-gray-900 dark:text-white">

{{-- Sidebar --}}
<aside class="w-64 bg-white dark:bg-gray-800 shadow flex flex-col">
    <div class="p-4 flex items-center space-x-2 border-b border-gray-200 dark:border-gray-700">
        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-8">
        <span class="font-bold text-lg">API Profiler</span>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ url('/api-profiler/dashboard') }}" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Dashboard</a>
        <a href="{{ url('/api-profiler/requests') }}" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Requests</a>
        <a href="{{ url('/api-profiler/routes') }}" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Routes</a>
        <a href="{{ url('/api-profiler/alerts') }}" class="block p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Alerts</a>
    </nav>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <button @click="dark = !dark" class="w-full py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">Toggle Dark Mode</button>
    </div>
</aside>

{{-- Main content --}}
<main class="flex-1 p-6 overflow-auto">
    @yield('content')
</main>
</body>
</html>
