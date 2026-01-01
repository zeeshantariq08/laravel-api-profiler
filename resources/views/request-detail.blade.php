@extends('laravel-api-profiler::layout')

@section('content')
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ url('/api-profiler/requests') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
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
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-md text-sm font-bold {{ $colorClass }}">
                        {{ $request->method }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-mono tracking-tight">{{ $request->url }}</h1>
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
    </div>

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</p>
            <p class="text-2xl font-bold {{ $request->duration_ms > 500 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                {{ $request->duration_ms }} <span class="text-sm font-normal text-gray-500">ms</span>
            </p>
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
                {{ count($request->queries) }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">HTTP Calls</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ count($request->http_calls) }}
            </p>
        </div>
    </div>

    {{-- Tabs & Content --}}
    <div x-data="{ tab: 'timeline' }" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-800">
            <nav class="flex -mb-px">
                <button @click="tab = 'timeline'" 
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'timeline', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'timeline' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">timeline</span>
                    Timeline
                </button>
                <button @click="tab = 'queries'" 
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'queries', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'queries' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">storage</span>
                    Queries ({{ count($request->queries) }})
                </button>
                <button @click="tab = 'http'" 
                        :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': tab === 'http', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': tab !== 'http' }"
                        class="group inline-flex items-center py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <span class="material-icons-round mr-2 text-lg">cloud</span>
                    HTTP Calls ({{ count($request->http_calls) }})
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            {{-- Timeline Tab --}}
            <div x-show="tab === 'timeline'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative h-80 w-full">
                    <canvas id="detailTimelineChart"></canvas>
                </div>
            </div>

            {{-- Queries Tab --}}
            <div x-show="tab === 'queries'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                @if(count($request->queries) > 0)
                    <div class="space-y-4">
                        @foreach($request->queries as $index => $query)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">#{{ $index + 1 }}</span>
                                    <span class="text-xs font-medium px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ round($query['time'], 2) }} ms
                                    </span>
                                </div>
                                <div class="p-4 bg-[#282c34]">
                                    <pre><code class="language-sql">{{ $query['sql'] }}</code></pre>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <span class="material-icons-round text-4xl mb-2">storage</span>
                        <p>No database queries executed.</p>
                    </div>
                @endif
            </div>

            {{-- HTTP Calls Tab --}}
            <div x-show="tab === 'http'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                @if(count($request->http_calls) > 0)
                    <div class="space-y-4">
                        @foreach($request->http_calls as $http)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">GET</span>
                                        <span class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $http['url'] }}</span>
                                    </div>
                                    <span class="text-xs font-medium px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ round($http['duration'], 2) }} ms
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
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });

        const ctx = document.getElementById('detailTimelineChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($timelineLabels),
                datasets: [
                    {
                        label: 'DB',
                        data: @json($timelineDb),
                        backgroundColor: '#f43f5e',
                        borderRadius: 4,
                        barPercentage: 0.5,
                    },
                    {
                        label: 'HTTP',
                        data: @json($timelineHttp),
                        backgroundColor: '#3b82f6',
                        borderRadius: 4,
                        barPercentage: 0.5,
                    },
                    {
                        label: 'Middleware',
                        data: @json($timelineMiddleware),
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                        barPercentage: 0.5,
                    },
                    {
                        label: 'Controller',
                        data: @json($timelineController),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        barPercentage: 0.5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true, grid: { borderDash: [4, 4] } }
                }
            }
        });
    </script>
@endsection
