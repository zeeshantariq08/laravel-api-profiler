# Release Notes - v1.0.0

## 🎉 Initial Release

We're excited to announce the first release of **Laravel API Profiler** - a production-grade performance observability tool for Laravel APIs.

## What's New

### Core Features

✅ **Request Performance Tracking**
- Track every API request with detailed metrics
- Duration, memory usage, query count, and HTTP calls
- Automatic slow request detection

✅ **Database Query Analysis**
- Complete SQL query list with execution times
- Automatic N+1 query detection
- Query performance insights

✅ **External API Monitoring**
- Track all external HTTP calls
- URL and duration tracking
- Identify slow external dependencies

✅ **Bottleneck Detection**
- Automatic identification of performance bottlenecks
- Categories: Database, HTTP, Memory, or Application
- Helps you focus on the right optimization

✅ **Interactive Dashboard**
- Beautiful, modern UI with dark mode
- Real-time charts and visualizations
- Route-based analytics
- Request timeline breakdown

✅ **Alert System**
- Slow request alerts
- High memory usage warnings
- N+1 query detection alerts
- Configurable thresholds

## Installation

```bash
composer require zeeshantariq/laravel-api-profiler
php artisan migrate
```

## Quick Start

Add the middleware to your API routes:

```php
use ZeeshanTariq\LaravelApiProfiler\Middleware\ApiProfilerMiddleware;

Route::middleware([ApiProfilerMiddleware::class])->group(function () {
    // Your API routes
});
```

Visit `/api-profiler/dashboard` to see your API performance!

## Requirements

- PHP >= 8.2
- Laravel >= 11.0
- MySQL/PostgreSQL/SQLite

## What Makes It Different?

Unlike other profiling tools:
- ✅ **Production Safe** - Can be used in production environments
- ✅ **API Focused** - Built specifically for API monitoring
- ✅ **N+1 Detection** - Automatic detection with suggestions
- ✅ **Bottleneck Analysis** - Identifies the root cause of slowness
- ✅ **Beautiful UI** - Modern dashboard with charts

## Documentation

Full documentation available in the [README.md](README.md)

## Support

- GitHub Issues: [Report bugs or request features](https://github.com/zeeshantariq/laravel-api-profiler/issues)
- Documentation: See README.md

## Credits

Created by **Zeeshan Tariq**

## License

MIT License - see [LICENSE](LICENSE) file for details

---

**Thank you for using Laravel API Profiler!** 🚀

