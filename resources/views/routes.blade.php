@extends('laravel-api-profiler::layout')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Route Analytics</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Performance metrics grouped by route.</p>
    </div>

    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Method
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Route
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Total Requests
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Avg Duration
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Slow Requests
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Errors
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($routes as $route)
                    @php
                        $methodColors = [
                            'GET' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'POST' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'PUT' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'DELETE' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            'PATCH' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                        ];
                        $colorClass = $methodColors[$route->method] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                        $isSlow = $route->avg_duration > 500;
                        $hasErrors = $route->errors > 0;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ $isSlow || $hasErrors ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                    {{ $route->method }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                            {{ $route->url }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($route->total_requests) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $isSlow ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ round($route->avg_duration, 2) }} ms
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $route->slow_requests }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $hasErrors ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $route->errors }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isSlow || $hasErrors)
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Needs Attention
                                    </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Healthy
                                    </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <span class="material-icons-round text-4xl mb-2 block">route</span>
                            <p>No routes found. Make some API calls to see route analytics.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
