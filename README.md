# 🚀 Laravel API Profiler
![Laravel](https://img.shields.io/badge/Laravel-12-red)![PHP](https://img.shields.io/badge/PHP-8.2+-blue)![License](https://img.shields.io/badge/license-MIT-green)![Status](https://img.shields.io/badge/status-production--ready-brightgreen)

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

Add the middleware to your API routes:

```php
use ZeeshanTariq\LaravelApiProfiler\Middleware\ApiProfilerMiddleware;

Route::middleware(['api', ApiProfilerMiddleware::class])->group(function () {
    // Your API routes
});
```

Visit the dashboard:

```
/api-profiler
```

---

## 🧪 Example (QtTagsPro)

Calling:

```
GET /api/v1/clients/15/assets
```

Shows:

- 980ms total time
- 760ms database time
- 32 SQL queries
- N+1 detected
- Bottleneck: Database

---

## 📸 Screenshots

*(Add screenshots here)*

---

## 🧭 Roadmap

- Real‑time WebSocket dashboard
- Slack & Email alerts
- Per‑client baselines
- Anomaly detection
- Team dashboards

---

## 👤 Author

**Zeeshan Tariq**  
Laravel Architect & AI Engineer  

---

## 📄 License

MIT
