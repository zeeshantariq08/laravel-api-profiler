<?php

namespace ZeeshanTariq\LaravelApiProfiler\Controllers;

use Illuminate\Routing\Controller;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerAlert;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;
use ZeeshanTariq\LaravelApiProfiler\Services\AlertEngine;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_requests' => ApiProfilerLog::count(),
            'slow_requests' => ApiProfilerLog::where('duration_ms', '>', 500)->count(),
            'errors' => ApiProfilerLog::where('status_code', '>=', 500)->count(),
        ];

        return view('laravel-api-profiler::dashboard', compact('stats'));
    }

    public function requests()
    {
        $requests = ApiProfilerLog::latest()
            ->limit(50)
            ->get();


        return view('laravel-api-profiler::requests', compact('requests'));
    }

    public function show(string $requestId)
    {
        $request = ApiProfilerLog::where('request_id', $requestId)->firstOrFail();

        $timelineEntries = is_array($request->timeline) ? $request->timeline : (json_decode($request->timeline, true) ?? []);

        $queries = [];
        if (!empty($request->queries_list)) {
            foreach ($request->queries_list as $q) {
                $queries[] = [
                    'type' => 'DB Query',
                    'name' => $q['sql'] ?? $q,   // depending on how you store it
                    'duration' => $q['time_ms'] ?? 0
                ];
            }
        }

        $httpCalls = [];
        $httpCallsData = $request->http_calls_list ?? $request->http_calls ?? [];
        if (!empty($httpCallsData)) {
            foreach ($httpCallsData as $h) {
                $httpCalls[] = [
                    'type' => 'HTTP Call',
                    'name' => $h['url'] ?? $h,
                    'duration' => $h['duration_ms'] ?? 0
                ];
            }
        }

        $timeline = array_merge(
            $queries,
            $httpCalls,
            $timelineEntries,
            [
                [
                    'type' => 'Middleware/Controller',
                    'name' => 'Application Code',
                    'duration' => $request->duration_ms - ($request->db_ms ?? 0) - ($request->http_ms ?? 0)
                ]
            ]
        );

        return view('laravel-api-profiler::request-detail', [
            'request' => $request,
            'timeline' => $timeline,
            'queriesList' => $request->queries_list ?? [],
            'httpCalls' => $request->http_calls ?? [],
        ]);
    }

    public function routes()
    {
        $routes = \ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog::selectRaw("
        method,
        url,
        COUNT(*) as total_requests,
        AVG(duration_ms) as avg_duration,
        SUM(CASE WHEN duration_ms > 500 THEN 1 ELSE 0 END) as slow_requests,
        SUM(CASE WHEN status_code >= 500 THEN 1 ELSE 0 END) as errors
    ")
            ->groupBy('method', 'url')
            ->get();

        return view('laravel-api-profiler::routes', compact('routes'));
    }
    public function alerts()
    {
        $alertRequestIds = ApiProfilerAlert::distinct()
            ->pluck('request_id')
            ->toArray();

        $logsWithAlerts = ApiProfilerLog::whereIn('request_id', $alertRequestIds)
            ->latest()
            ->get()
            ->map(function ($log) {
                $alertRecords = ApiProfilerAlert::where('request_id', $log->request_id)->get();

                $alertTypes = $alertRecords->map(function ($alert) {
                    return match ($alert->type) {
                        'slow_request' => 'Slow Request',
                        'high_memory' => 'High Memory',
                        'n_plus_one' => 'N+1 Detected',
                        default => ucfirst(str_replace('_', ' ', $alert->type))
                    };
                })->unique()->values()->toArray();

                $log->alerts = $alertTypes;
                $log->alert_count = count($alertTypes);

                return $log;
            });

        $logsWithIssues = ApiProfilerLog::where(function ($query) {
            $query->where('duration_ms', '>', config('api-profiler.slow_request_threshold_ms', 500))
                ->orWhere('memory_peak', '>', config('api-profiler.high_memory_bytes', 128 * 1024 * 1024))
                ->orWhereNotNull('n_plus_one');
        })
            ->whereNotIn('request_id', $alertRequestIds)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($log) {
                $list = [];
                if ($log->duration_ms > config('api-profiler.slow_request_threshold_ms', 500)) {
                    $list[] = 'Slow Request';
                }
                if ($log->memory_peak > config('api-profiler.high_memory_bytes', 128 * 1024 * 1024)) {
                    $list[] = 'High Memory';
                }
                if (!empty($log->n_plus_one)) {
                    $list[] = 'N+1 Detected';
                }
                $log->alerts = $list;
                $log->alert_count = count($list);
                return $log;
            })
            ->filter(fn($log) => count($log->alerts) > 0);

        $alerts = $logsWithAlerts->concat($logsWithIssues)
            ->sortByDesc('created_at')
            ->values();

        return view('laravel-api-profiler::alerts', compact('alerts'));
    }
    public function dashboard() {
        $timelineLabels = $timelineDb = $timelineHttp = $timelineMiddleware = $timelineController = [];

        $requests = ApiProfilerLog::latest()->limit(10)->get();
        foreach($requests as $req){
            $timelineLabels[] = substr($req->url, 0, 20) . (strlen($req->url) > 20 ? '...' : '');
            $timelineDb[] = $req->db_time_ms ?? 0;
            $timelineHttp[] = $req->http_time_ms ?? 0;
            $timelineMiddleware[] = $req->middleware_time_ms ?? 0;
            $timelineController[] = $req->controller_time_ms ?? 0;
        }

        $routeLabels = $routeDurations = [];
        $routes = ApiProfilerLog::select('url')->distinct()->limit(10)->get();
        foreach($routes as $route){
            $routeLabels[] = substr($route->url, 0, 30) . (strlen($route->url) > 30 ? '...' : '');
            $avgDuration = ApiProfilerLog::where('url', $route->url)->avg('duration_ms');
            $routeDurations[] = round($avgDuration ?? 0, 2);
        }

        return view('laravel-api-profiler::dashboard', [
            'totalRequests' => ApiProfilerLog::count(),
            'slowRequests' => ApiProfilerLog::where('duration_ms','>',500)->count(),
            'alertsCount' => ApiProfilerAlert::count(),
            'timelineLabels'=>$timelineLabels,
            'timelineDb'=>$timelineDb,
            'timelineHttp'=>$timelineHttp,
            'timelineMiddleware'=>$timelineMiddleware,
            'timelineController'=>$timelineController,
            'routeLabels'=>$routeLabels,
            'routeDurations'=>$routeDurations
        ]);
    }

}
