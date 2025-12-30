@extends('laravel-api-profiler::layout')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Total Requests</h2>
                <p class="text-2xl font-bold">{{ $totalRequests }}</p>
            </div>
            <span class="material-icons text-4xl text-blue-500">receipt</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Slow Requests</h2>
                <p class="text-2xl font-bold">{{ $slowRequests }}</p>
            </div>
            <span class="material-icons text-4xl text-red-500">timer</span>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Alerts</h2>
                <p class="text-2xl font-bold">{{ $alertsCount }}</p>
            </div>
            <span class="material-icons text-4xl text-yellow-500">warning</span>
        </div>
    </div>

    {{-- Timeline / Waterfall Chart --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-4">Request Timeline</h2>
        <canvas id="timelineChart" height="100"></canvas>
    </div>

    {{-- Route Analytics Chart --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Route Performance</h2>
        <canvas id="routeChart" height="100"></canvas>
    </div>

    <script>
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        const timelineChart = new Chart(timelineCtx, {
            type: 'bar',
            data: {
                labels: @json($timelineLabels),
                datasets: [
                    {
                        label: 'DB (ms)',
                        data: @json($timelineDb),
                        backgroundColor: 'rgba(255,99,132,0.7)'
                    },
                    {
                        label: 'HTTP (ms)',
                        data: @json($timelineHttp),
                        backgroundColor: 'rgba(54,162,235,0.7)'
                    },
                    {
                        label: 'Middleware (ms)',
                        data: @json($timelineMiddleware),
                        backgroundColor: 'rgba(255,206,86,0.7)'
                    },
                    {
                        label: 'Controller (ms)',
                        data: @json($timelineController),
                        backgroundColor: 'rgba(75,192,192,0.7)'
                    }
                ]
            },
            options: { responsive:true, scales:{y:{beginAtZero:true}} }
        });

        const routeCtx = document.getElementById('routeChart').getContext('2d');
        const routeChart = new Chart(routeCtx, {
            type: 'line',
            data: {
                labels: @json($routeLabels),
                datasets: [{
                    label: 'Avg Duration (ms)',
                    data: @json($routeDurations),
                    borderColor: 'rgba(75,192,192,1)',
                    backgroundColor: 'rgba(75,192,192,0.2)',
                    fill:true,
                    tension:0.3
                }]
            },
            options: { responsive:true, scales:{y:{beginAtZero:true}} }
        });
    </script>
@endsection
