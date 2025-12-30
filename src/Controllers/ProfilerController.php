<?php

namespace ZeeshanTariq\LaravelApiProfiler\Controllers;

use Illuminate\Routing\Controller;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;

class ProfilerController extends Controller
{
    /**
     * Return the latest 50 API profiler logs.
     */
//    public function index()
//    {
//        return ApiProfilerLog::latest('created_at')
//            ->limit(50)
//            ->get([
//                'request_id',
//                'method',
//                'url',
//                'status_code',
//                'duration_ms',
//                'db_ms',
//                'http_ms',
//                'queries',
//                'memory_peak',
//                'slow',
//                'bottleneck',
//                'created_at',
//            ]);
//    }
//
//    /**
//     * Return details for a single request, including timings and N+1 info.
//     */
//    public function show(string $requestId)
//    {
//        $log = ApiProfilerLog::where('request_id', $requestId)
//            ->firstOrFail();
//
//        return [
//            'request_id' => $log->request_id,
//            'method' => $log->method,
//            'url' => $log->url,
//            'status_code' => $log->status_code,
//            'duration_ms' => $log->duration_ms,
//            'db_ms' => $log->db_ms,
//            'http_ms' => $log->http_ms,
//            'queries' => $log->queries,
//            'memory_peak' => $log->memory_peak,
//            'slow' => $log->slow,
//            'bottleneck' => $log->bottleneck,
//            'timings' => $log->timings, // middleware/controller/db/http/response
//            'nPlusOne' => $log->n_plus_one, // JSON array of detected N+1 queries
//            'created_at' => $log->created_at,
//        ];
//    }
//
//    public function dashboard()
//    {
//        $logs = ApiProfilerLog::latest()->limit(50)->get();
//        return view('api-profiler::dashboard', compact('logs'));
//    }
//
//    public function view($id)
//    {
//        $log = ApiProfilerLog::findOrFail($id);
//        return view('api-profiler::request', compact('log'));
//    }
//
//    public function routes()
//    {
//        return ApiProfilerLog::routeStats();
//    }

    public function index()
    {
        return ApiProfilerLog::query()
            ->orderByDesc('duration_ms')
            ->limit(100)
            ->get([
                'request_id',
                'method',
                'url',
                'status_code',
                'duration_ms',
                'db_ms',
                'http_ms',
                'queries',
                'created_at'
            ]);
    }

    public function show(string $requestId)
    {
        $log = ApiProfilerLog::where('request_id', $requestId)->firstOrFail();

        return [
            'meta' => $log->only([
                'request_id','method','url','status_code',
                'duration_ms','db_time_ms','http_time_ms'
            ]),
            'timeline' => $log->timeline
        ];
    }

    public function routes()
    {
        $rows = \DB::table('api_profiler_logs')
            ->selectRaw("
            url,
            COUNT(*) as hits,
            AVG(duration_ms) as avg_ms,
            MAX(duration_ms) as max_ms,
            AVG(db_ms) as avg_db,
            AVG(http_ms) as avg_http,
            SUM(CASE WHEN status_code >= 500 THEN 1 ELSE 0 END) as errors
        ")
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('url')
            ->get();

        return $rows->map(function ($r) {
            $health = 'healthy';

            if ($r->avg_ms > 1000 || $r->errors > 0) {
                $health = 'danger';
            } elseif ($r->avg_ms > 500) {
                $health = 'warning';
            }

            return [
                'url' => $r->url,
                'hits' => $r->hits,
                'avg_ms' => round($r->avg_ms),
                'max_ms' => round($r->max_ms),
                'avg_db' => round($r->avg_db),
                'avg_http' => round($r->avg_http),
                'errors' => $r->errors,
                'health' => $health
            ];
        });
    }

    public function alerts()
    {
        return \DB::table('api_profiler_alerts')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }
    public function trend(string $url)
    {
        return \DB::table('api_profiler_logs')
            ->selectRaw("
            DATE_FORMAT(created_at, '%H:%i') as minute,
            AVG(duration_ms) as avg_ms
        ")
            ->where('url', urldecode($url))
            ->where('created_at', '>=', now()->subHours(2))
            ->groupBy('minute')
            ->orderBy('minute')
            ->get();
    }


}
