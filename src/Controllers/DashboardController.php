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
        $request = \ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog::where('request_id', $requestId)
            ->firstOrFail();

        // Prepare timeline
        $timeline = array_merge(
            $request->queries->map(fn($q) => [
                'type' => 'DB Query',
                'name' => $q->sql,
                'duration' => $q->time_ms
            ])->toArray(),
            $request->http_calls->map(fn($h) => [
                'type' => 'HTTP Call',
                'name' => $h->url,
                'duration' => $h->duration_ms
            ])->toArray(),
            [
                [
                    'type' => 'Middleware/Controller',
                    'name' => 'Application Code',
                    'duration' => $request->duration_ms - $request->db_time_ms - $request->http_time_ms
                ]
            ]
        );

        return view('laravel-api-profiler::request-detail', [
            'request' => $request,
            'timeline' => $timeline,
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
        $alerts = \ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog::all()->map(function($log) {
            $list = [];

            if ($log->duration_ms > 500) $list[] = 'Slow Request';
            if ($log->memory_peak > 128*1024*1024) $list[] = 'High Memory';
            if (!empty($log->n_plus_one)) $list[] = 'N+1 Detected';

            $log->alerts = $list;
            return $log;
        })->filter(fn($log) => count($log->alerts) > 0);

        return view('laravel-api-profiler::alerts', compact('alerts'));
    }
    public function dashboard() {
        $timelineLabels = $timelineDb = $timelineHttp = $timelineMiddleware = $timelineController = [];

        // Prepare last 10 requests timeline data
        $requests = ApiProfilerLog::latest()->limit(10)->get();
        foreach($requests as $req){
            $timelineLabels[] = substr($req->url,0,20);
            $timelineDb[] = $req->db_time_ms;
            $timelineHttp[] = $req->http_time_ms;
            $timelineMiddleware[] = $req->middleware_time_ms;
            $timelineController[] = $req->controller_time_ms;
        }

        $routeLabels = $routeDurations = [];
        $routes = ApiProfilerLog::select('url')->distinct()->get();
        foreach($routes as $route){
            $routeLabels[] = $route->url;
            $routeDurations[] = ApiProfilerLog::where('url',$route->url)->avg('duration_ms');
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
