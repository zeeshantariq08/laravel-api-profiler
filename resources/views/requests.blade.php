@extends('laravel-api-profiler::layout')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Requests</h1>

    <div x-data="{search:''}" class="space-y-4">
        {{-- Search --}}
        <input type="text" x-model="search" placeholder="Search by URL..."
               class="w-full p-2 border rounded dark:bg-gray-800 dark:text-white">

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 shadow rounded">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2">Request ID</th>
                    <th class="px-4 py-2">Method</th>
                    <th class="px-4 py-2">URL</th>
                    <th class="px-4 py-2">Duration (ms)</th>
                    <th class="px-4 py-2">Memory (MB)</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($requests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                        x-show=" '{{ $req->url }}'.toLowerCase().includes(search.toLowerCase()) "
                        @click="window.location='{{ url('/api-profiler/requests/'.$req->request_id) }}'">
                        <td class="border px-4 py-2 font-mono">{{ $req->request_id }}</td>
                        <td class="border px-4 py-2">{{ $req->method }}</td>
                        <td class="border px-4 py-2">{{ $req->url }}</td>
                        <td class="border px-4 py-2">{{ $req->duration_ms }}</td>
                        <td class="border px-4 py-2">{{ round($req->memory_peak/1024/1024,2) }}</td>
                        <td class="border px-4 py-2">
                        <span class="px-2 py-1 rounded text-white
                            {{ $req->duration_ms > 500 ? 'bg-red-500' : 'bg-green-500' }}">
                            {{ $req->duration_ms > 500 ? 'Slow' : 'OK' }}
                        </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
