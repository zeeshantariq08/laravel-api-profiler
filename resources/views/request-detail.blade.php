@extends('laravel-api-profiler::layout')

@section('content')
    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ url('/api-profiler/requests') }}"
           class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <span class="material-icons-round">arrow_back</span>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                @php
                    $methodColors = [
                        'GET' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        'POST' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'PUT' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'DELETE' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        'PATCH' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                    ];
                    $colorClass = $methodColors[$request->method] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';

                    // Get queries and HTTP calls from the controller
                    $queriesList = $request->queries_list ?? [];
                    $httpCalls = $request->http_calls ?? [];
                    $nPlusOne = $request->n_plus_one ?? [];
                    $bottleneck = $request->bottleneck ?? null;
                @endphp

                <span class="px-2.5 py-0.5 rounded-md text-sm font-bold {{ $colorClass }}">
                    {{ $request->method }}
                </span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-mono tracking-tight">
                    {{ $request->url }}
                </h1>
            </div>
            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-base">schedule</span>
                    {{ $request->created_at ?? now() }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-icons-round text-base">fingerprint</span>
                    {{ $request->request_id }}
                </span>
            </div>
        </div>
    </div>

    {{-- Alerts Banner --}}
    @if(!empty($nPlusOne) || $request->slow || $bottleneck)
        <div
            class="mb-6 p-4 rounded-xl border {{ $request->slow ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' }}">
            <div class="flex items-start gap-3">
                <span
                    class="material-icons-round text-2xl {{ $request->slow ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">warning</span>
                <div class="flex-1">
                    <h3 class="font-semibold {{ $request->slow ? 'text-red-900 dark:text-red-300' : 'text-amber-900 dark:text-amber-300' }} mb-2">
                        Performance Issues Detected</h3>
                    <div
                        class="space-y-1 text-sm {{ $request->slow ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }}">
                        @if($request->slow)
                            <p>⚠️ Slow Request: {{ $request->duration_ms }}ms (threshold: 500ms)</p>
                        @endif
                        @if($bottleneck)
                            <p>🔍 Bottleneck: <strong>{{ ucfirst($bottleneck) }}</strong></p>
                        @endif
                        @if(!empty($nPlusOne))
                            <p>🚨 N+1 Query Detected: {{ count($nPlusOne) }} potential issue(s)</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</p>
            <p class="text-2xl font-bold {{ $request->duration_ms > 500 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                {{ round($request->duration_ms, 2) }} <span class="text-sm font-normal text-gray-500">ms</span>
            </p>
            @if($request->db_time_ms > 0 || $request->http_time_ms > 0)
                <p class="text-xs text-gray-500 mt-1">
                    DB: {{ round($request->db_time_ms, 2) }}ms |
                    HTTP: {{ round($request->http_time_ms, 2) }}ms
                </p>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Memory Peak</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ round($request->memory_peak/1024/1024, 2) }} <span class="text-sm font-normal text-gray-500">MB</span>
            </p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">DB Queries</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $request->queries ?? 0 }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total DB Time: {{ round($request->db_time_ms, 2) }}ms</p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">HTTP Calls</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ count($httpCalls) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total HTTP Time: {{ round($request->http_time_ms, 2) }}ms</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'timeline' }" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-800">
            <nav class="flex -mb-px">
                <button @click="tab = 'timeline'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'timeline', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'timeline' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">timeline</span> Timeline
                </button>
                <button @click="tab = 'queries'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'queries', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'queries' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">storage</span> Queries ({{ $request->queries }})
                </button>
                <button @click="tab = 'http'"
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'http', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'http' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">cloud</span> HTTP Calls ({{ count($httpCalls) }})
                </button>
            </nav>
        </div>

        <div class="p-6">
            {{-- Timeline --}}
            <div x-show="tab === 'timeline'">
                <div class="relative h-80 w-full">
                    <canvas id="detailTimelineChart"></canvas>
                </div>
            </div>

            {{-- Queries --}}
            <div x-show="tab === 'queries'">
                @if(!empty($queriesList))
                    <div class="space-y-4">
                        @foreach($queriesList as $index => $query)
                            @php
                                $querySql = is_array($query) ? ($query['sql'] ?? $query) : $query;
                                $queryTime = is_array($query) ? ($query['time_ms'] ?? 0) : 0;
                            @endphp
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                                    <span
                                        class="text-xs font-mono text-gray-500 dark:text-gray-400">Query #{{ $index + 1 }}</span>
                                    <span class="text-xs font-medium px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ round($queryTime, 2) }} ms
                                    </span>
                                </div>
                                <div class="p-4 bg-[#282c34]">
                                    <pre><code class="language-sql">{{ $querySql }}</code></pre>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!empty($nPlusOne))
                        <div
                            class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <h4 class="font-semibold text-red-900 dark:text-red-300 mb-2">⚠️ N+1 Query Detection</h4>
                            @foreach($nPlusOne as $n1)
                                <div class="text-sm text-red-700 dark:text-red-400 mb-2">
                                    <p><strong>Query executed {{ $n1['count'] }} times:</strong></p>
                                    <code
                                        class="text-xs bg-red-100 dark:bg-red-900/30 p-2 rounded block mt-1">{{ $n1['query'] }}</code>
                                    <p class="mt-1 text-xs italic">{{ $n1['suggestion'] ?? 'Consider eager loading' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <span class="material-icons-round text-4xl mb-2">storage</span>
                        <p>No database queries executed.</p>
                    </div>
                @endif
            </div>

            {{-- HTTP --}}
            <div x-show="tab === 'http'">
                @if(!empty($httpCalls))
                    <div class="space-y-4">
                        @foreach($httpCalls as $index => $http)
                            @php
                                $httpUrl = is_array($http) ? ($http['url'] ?? $http) : $http;
                                $httpDuration = is_array($http) ? ($http['duration_ms'] ?? 0) : 0;
                            @endphp
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">HTTP</span>
                                        <span
                                            class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $httpUrl }}</span>
                                    </div>
                                    <span class="text-xs font-medium px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ round($httpDuration, 2) }} ms
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <span class="material-icons-round text-4xl mb-2">cloud_off</span>
                        <p>No external HTTP calls made.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('pre code').forEach(el => hljs.highlightElement(el));

            const timeline = @json($queriesList);
            const labels = timeline.map(t => t.name.slice(0, 20));
            const dbData = timeline.filter(t => t.type === 'db').map(t => t.duration);
            const httpData = timeline.filter(t => t.type === 'http').map(t => t.duration);
            const middlewareData = timeline.filter(t => t.type === 'middleware').map(t => t.duration);
            const controllerData = timeline.filter(t => t.type === 'controller').map(t => t.duration);

            const ctx = document.getElementById('detailTimelineChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {label: 'DB', data: dbData, backgroundColor: '#f43f5e', borderRadius: 4, barPercentage: 0.5},
                        {
                            label: 'HTTP',
                            data: httpData,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            barPercentage: 0.5
                        },
                        {
                            label: 'Middleware',
                            data: middlewareData,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                            barPercentage: 0.5
                        },
                        {
                            label: 'Controller',
                            data: controllerData,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                            barPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'bottom', labels: {usePointStyle: true, padding: 20}},
                        tooltip: {backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8}
                    },
                    scales: {
                        x: {stacked: true, grid: {display: false}},
                        y: {stacked: true, beginAtZero: true, grid: {borderDash: [4, 4]}}
                    }
                }
            });
        });
    </script>
@endsection
