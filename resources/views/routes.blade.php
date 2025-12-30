@extends('laravel-api-profiler::layout')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Route Health</h1>

    <table class="min-w-full bg-white shadow rounded">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2">Method</th>
            <th class="px-4 py-2">Route</th>
            <th class="px-4 py-2">Total Requests</th>
            <th class="px-4 py-2">Avg Duration (ms)</th>
            <th class="px-4 py-2">Slow Requests</th>
            <th class="px-4 py-2">Errors</th>
        </tr>
        </thead>
        <tbody>
        @foreach($routes as $route)
            <tr class="hover:bg-gray-50 {{ $route->avg_duration > 500 ? 'bg-red-50' : '' }}">
                <td class="border px-4 py-2">{{ $route->method }}</td>
                <td class="border px-4 py-2">{{ $route->url }}</td>
                <td class="border px-4 py-2">{{ $route->total_requests }}</td>
                <td class="border px-4 py-2">{{ round($route->avg_duration,2) }}</td>
                <td class="border px-4 py-2">{{ $route->slow_requests }}</td>
                <td class="border px-4 py-2">{{ $route->errors }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
