# 🚀 Laravel API Profiler

[![Latest Version](https://img.shields.io/packagist/v/zeeshantariq/laravel-api-profiler.svg?style=flat-square)](https://packagist.org/packages/zeeshantariq/laravel-api-profiler)
[![Total Downloads](https://img.shields.io/packagist/dt/zeeshantariq/laravel-api-profiler.svg?style=flat-square)](https://packagist.org/packages/zeeshantariq/laravel-api-profiler)
![Laravel](https://img.shields.io/badge/Laravel-11-red)![PHP](https://img.shields.io/badge/PHP-8.2+-blue)![License](https://img.shields.io/badge/license-MIT-green)![Status](https://img.shields.io/badge/status-production--ready-brightgreen)

**Production‑grade performance observability for Laravel APIs**

Laravel API Profiler helps you understand *why* your APIs are slow by tracking database queries, external API calls, memory usage, and execution time — all in one powerful dashboard.

---

## ✨ Features

- ⏱️ Request & route performance
- 🗄️ SQL query timing + N+1 detection
- 🌐 External HTTP call tracking
- 🧠 Bottleneck detection (DB, HTTP, Memory, App)
- 📊 Interactive dashboard with charts
- 🚨 Slow request & anomaly alerts
- 🧭 Route based analytics

---

## 🧠 Why not Telescope?

| Feature | Telescope | Laravel API Profiler |
|--------|-----------|---------------------|
| Production safe | ❌ | ✅ |
| N+1 detection | ❌ | ✅ |
| Bottleneck detection | ❌ | ✅ |
| API focused | ❌ | ✅ |
| Performance charts | ❌ | ✅ |

---

## 🛠 Installation

```bash
composer require zeeshantariq/laravel-api-profiler
php artisan migrate
```

---

## 🔧 Usage

### Basic Setup

Add the middleware to your API routes:

```php
use ZeeshanTariq\LaravelApiProfiler\Middleware\ApiProfilerMiddleware;

Route::middleware(['api', ApiProfilerMiddleware::class])->group(function () {
    // Your API routes
});
```

Or use the middleware alias:

```php
Route::middleware(['api', 'api-profiler'])->group(function () {
    // Your API routes
});
```

### Access the Dashboard

Visit the dashboard at:

```
http://your-app.test/api-profiler/dashboard
```

### Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=config --provider="ZeeshanTariq\LaravelApiProfiler\LaravelApiProfilerServiceProvider"
```

Available configuration options in `config/api-profiler.php`:

```php
return [
    // Enable/disable the profiler
    'enabled' => env('API_PROFILER_ENABLED', true),
    
    // Slow request threshold in milliseconds
    'slow_request_threshold_ms' => 500,
    
    // High memory threshold in bytes (default: 128MB)
    'high_memory_bytes' => 128 * 1024 * 1024,
    
    // N+1 query detection threshold
    'n_plus_one_threshold' => 5,
    
    // Number of samples for baseline calculation
    'baseline_sample_size' => 50,
    
    // Middleware for dashboard routes
    'middleware' => ['web', 'auth'],
];
```

### Environment Variables

Add to your `.env` file:

```env
API_PROFILER_ENABLED=true
```

---

## 📊 Dashboard Features

### Main Dashboard (`/api-profiler/dashboard`)

- **Overview Stats**: Total requests, slow requests, active alerts
- **Request Breakdown Chart**: Visual timeline showing DB, HTTP, Middleware, and Controller time
- **Route Performance Chart**: Average duration per route over time

### Requests Page (`/api-profiler/requests`)

- List of all profiled requests
- Search and filter functionality
- Click any request to see detailed analysis

### Request Detail Page

For each request, you can see:

- **Performance Metrics**: Duration, memory usage, query count, HTTP calls
- **Bottleneck Detection**: Identifies if the issue is Database, HTTP, Memory, or Application
- **N+1 Detection**: Highlights repeated queries with suggestions
- **Timeline Tab**: Visual breakdown of request execution
- **Queries Tab**: All SQL queries with execution times
- **HTTP Calls Tab**: External API calls with durations

### Routes Page (`/api-profiler/routes`)

- Performance metrics grouped by route
- Average duration, slow request count, error count
- Health status indicators

### Alerts Page (`/api-profiler/alerts`)

- Slow requests (>500ms by default)
- High memory usage alerts
- N+1 query detection alerts
- Click to view detailed request analysis

## 🧪 Example

Calling:

```
GET /api/v1/clients/15/assets
```

The profiler will show:

- **Total Duration**: 980ms
- **Database Time**: 760ms (77% of total)
- **SQL Queries**: 32 queries executed
- **N+1 Detected**: Query executed 15+ times
- **Bottleneck**: Database
- **Alert**: Slow Request (>500ms)

This helps you identify that the issue is database-related and likely due to N+1 queries.

---

## 🔍 How It Works

### Automatic Tracking

The profiler automatically tracks:

1. **Database Queries**: Every SQL query with execution time
2. **HTTP Calls**: External API requests via Laravel's HTTP client
3. **Memory Usage**: Peak memory consumption per request
4. **Execution Time**: Total request duration broken down by component

### Bottleneck Detection

The profiler identifies the main performance bottleneck:

- **Database**: If DB time > 200ms
- **External API**: If HTTP time > 300ms
- **Memory**: If memory usage > 128MB
- **Application**: Otherwise

### N+1 Query Detection

Automatically detects when the same query is executed multiple times (threshold: 5+ times), suggesting potential N+1
problems that can be solved with eager loading.

### Performance impact

The profiler is designed to be lightweight:

- Data is stored asynchronously
- Minimal overhead on requests
- Can be disabled in production if needed via config

## 📸 Screenshots

*(Add screenshots of your dashboard here)*

---

## 📝 Requirements

- PHP >= 8.2
- Laravel >= 11.0
- MySQL/PostgreSQL/SQLite

## 🧭 Roadmap

- Real‑time WebSocket dashboard
- Slack & Email alerts
- Per‑route baselines
- Anomaly detection
- Team dashboards
- Export reports
- API endpoints for programmatic access

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 👤 Author

**Zeeshan Tariq**  
Laravel Architect & AI Engineer  

---

## 📄 License

MIT
