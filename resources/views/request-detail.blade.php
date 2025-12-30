@extends('laravel-api-profiler::layout')

@section('content')
    <h1 class="text-3xl font-bold mb-4">{{ $request->method }} {{ $request->url }}</h1>

    {{-- Timeline / Waterfall --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-2">Timeline</h2>
        <canvas id="timelineChart" height="100"></canvas>
    </div>

    {{-- Queries --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-2">Database Queries</h2>
        <ul class="list-disc pl-6">
            @foreach($request->queries as $query)
                <li class="font-mono">{{ $query['sql'] }} — {{ round($query['time'],2) }} ms</li>
            @endforeach
        </ul>
    </div>

    {{-- HTTP Calls --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow mb-6">
        <h2 class="text-xl font-bold mb-2">HTTP Calls</h2>
        <ul class="list-disc pl-6">
            @foreach($request->http_calls as $http)
                <li class="font-mono">{{ $http['url'] }} — {{ round($http['duration'],2) }} ms</li>
            @endforeach
        </ul>
    </div>

    <script>
        const ctx = document.getElementById('timelineChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($timelineLabels),
                datasets: [
                    {label:'DB', data:@json($timelineDb), backgroundColor:'rgba(255,99,132,0.7)'},
                    {label:'HTTP', data:@json($timelineHttp), backgroundColor:'rgba(54,162,235,0.7)'},
                    {label:'Middleware', data:@json($timelineMiddleware), backgroundColor:'rgba(255,206,86,0.7)'},
                    {label:'Controller', data:@json($timelineController), backgroundColor:'rgba(75,192,192,0.7)'}
                ]
            },
            options: {responsive:true, scales:{y:{beginAtZero:true}}}
        });
    </script>
@endsection
