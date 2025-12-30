@extends('laravel-api-profiler::layout')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Alerts</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 shadow rounded">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">Request ID</th>
                <th class="px-4 py-2">Method</th>
                <th class="px-4 py-2">URL</th>
                <th class="px-4 py-2">Duration (ms)</th>
                <th class="px-4 py-2">Memory (MB)</th>
                <th class="px-4 py-2">Alert Type</th>
            </tr>
            </thead>
            <tbody>
            @foreach($alerts as $alert)
                <tr class="hover:bg-red-50 dark:hover:bg-red-700 cursor-pointer">
                    <td class="border px-4 py-2 font-mono">
                        <a href="/api-profiler/requests/{{ $alert->request_id }}" class="text-blue-500">{{ $alert->request_id }}</a>
                    </td>
                    <td class="border px-4 py-2">{{ $alert->method }}</td>
                    <td class="border px-4 py-2">{{ $alert->url }}</td>
                    <td class="border px-4 py-2">{{ $alert->duration_ms }}</td>
                    <td class="border px-4 py-2">{{ round($alert->memory_peak/1024/1024,2) }}</td>
                    <td class="border px-4 py-2">
                        @foreach($alert->alerts as $type)
                            <span class="px-2 py-1 rounded text-white {{ $type=='Slow' ? 'bg-red-500' : 'bg-yellow-500' }}">{{ $type }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
