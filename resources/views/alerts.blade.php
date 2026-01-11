@extends('laravel-api-profiler::layout')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Alerts</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Performance issues and anomalies detected in your
            API.</p>
    </div>

    @if($alerts->count() > 0)
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Request ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Method
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            URL
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Duration
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Memory
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Alert Types
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($alerts as $alert)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group"
                            @click="window.location='{{ url('/api-profiler/requests/'.$alert->request_id) }}'">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                {{ substr($alert->request_id, 0, 8) }}...
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $methodColors = [
                                        'GET' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'POST' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'PUT' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'DELETE' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        'PATCH' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                    ];
                                    $colorClass = $methodColors[$alert->method] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ $alert->method }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                {{ Str::limit($alert->url, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400 font-medium">
                                {{ round($alert->duration_ms, 2) }} ms
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ round($alert->memory_peak/1024/1024, 2) }} MB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($alert->alerts as $type)
                                        @php
                                            $alertColors = [
                                                'Slow Request' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                'High Memory' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                                'N+1 Detected' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                            ];
                                            $alertClass = $alertColors[$type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $alertClass }}">
                                                {{ $type }}
                                            </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <span
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                        View &rarr;
                                    </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-12 text-center">
            <span
                class="material-icons-round text-gray-300 dark:text-gray-600 text-6xl mb-4 block">notifications_off</span>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Alerts</h3>
            <p class="text-gray-500 dark:text-gray-400">Your API is performing well! No performance issues detected.</p>
        </div>
    @endif
@endsection
