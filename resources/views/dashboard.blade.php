@extends('laravel-api-profiler::layout')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your API performance and health.</p>
        </div>
        <div class="flex gap-2">
            <button class="px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Last 24 Hours
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Requests --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-icons-round text-6xl text-blue-500">receipt_long</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg">
                        <span class="material-icons-round text-blue-600 dark:text-blue-400 text-xl">receipt_long</span>
                    </div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Requests</h2>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalRequests) }}</p>
                <div class="mt-2 flex items-center text-xs text-green-600 dark:text-green-400 font-medium">
                    <span class="material-icons-round text-sm mr-1">trending_up</span>
                    <span>+12% from yesterday</span>
                </div>
            </div>
        </div>

        {{-- Slow Requests --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-icons-round text-6xl text-red-500">timer_off</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-red-50 dark:bg-red-500/10 rounded-lg">
                        <span class="material-icons-round text-red-600 dark:text-red-400 text-xl">timer_off</span>
                    </div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Slow Requests (>500ms)</h2>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($slowRequests) }}</p>
                <div class="mt-2 flex items-center text-xs text-red-600 dark:text-red-400 font-medium">
                    <span class="material-icons-round text-sm mr-1">warning</span>
                    <span>Requires attention</span>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-icons-round text-6xl text-amber-500">notifications_active</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg">
                        <span class="material-icons-round text-amber-600 dark:text-amber-400 text-xl">notifications_active</span>
                    </div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Alerts</h2>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($alertsCount) }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <span>System health check</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Timeline Chart --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Breakdown</h2>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">Avg Duration</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>

        {{-- Route Performance Chart --}}
        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Route Performance</h2>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">Top Routes</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="routeChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Common Chart Options
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6b7280';
        Chart.defaults.scale.grid.color = 'rgba(107, 114, 128, 0.1)';
        
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20, boxWidth: 8 }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                x: { grid: { display: false } }
            }
        };

        // Timeline Chart
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        new Chart(timelineCtx, {
            type: 'bar',
            data: {
                labels: @json($timelineLabels),
                datasets: [
                    {
                        label: 'DB',
                        data: @json($timelineDb),
                        backgroundColor: '#f43f5e',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'HTTP',
                        data: @json($timelineHttp),
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Middleware',
                        data: @json($timelineMiddleware),
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Controller',
                        data: @json($timelineController),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, grid: { borderDash: [4, 4] } }
                }
            }
        });

        // Route Performance Chart
        const routeCtx = document.getElementById('routeChart').getContext('2d');
        
        // Create gradient
        let gradient = routeCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(routeCtx, {
            type: 'line',
            data: {
                labels: @json($routeLabels),
                datasets: [{
                    label: 'Avg Duration (ms)',
                    data: @json($routeDurations),
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: commonOptions
        });
    </script>
@endsection
