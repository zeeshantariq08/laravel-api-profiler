@extends('laravel-api-profiler::layout')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Requests</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitor and analyze individual API requests.</p>
    </div>

    <div x-data="{ search: '' }" class="space-y-6">
        {{-- Search Bar --}}
        <div class="relative max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-icons-round text-gray-400 text-xl">search</span>
            </div>
            <input type="text" 
                   x-model="search" 
                   placeholder="Search by URL, Method, or ID..." 
                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg leading-5 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Request ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">URL</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Memory</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">View</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($requests as $req)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer group"
                                x-show="!search || '{{ $req->url }}'.toLowerCase().includes(search.toLowerCase()) || '{{ $req->method }}'.toLowerCase().includes(search.toLowerCase()) || '{{ $req->request_id }}'.toLowerCase().includes(search.toLowerCase())"
                                @click="window.location='{{ url('/api-profiler/requests/'.$req->request_id) }}'">
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                    {{ substr($req->request_id, 0, 8) }}...
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
                                        $colorClass = $methodColors[$req->method] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ $req->method }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                    {{ Str::limit($req->url, 50) }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $req->duration_ms }} ms
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ round($req->memory_peak/1024/1024, 2) }} MB
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($req->duration_ms > 500)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Slow
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Healthy
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <span class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                        View Details &rarr;
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(count($requests) === 0)
                <div class="p-12 text-center">
                    <span class="material-icons-round text-gray-300 dark:text-gray-600 text-6xl mb-4">inbox</span>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No requests found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Make some API calls to see them listed here.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
