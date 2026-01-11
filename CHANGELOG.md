# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-12


### Added

- Initial release of Laravel API Profiler
- Request performance tracking with duration, memory, and query metrics
- Database query tracking with SQL query list and timing
- External HTTP call tracking with URL and duration
- Automatic bottleneck detection (Database, HTTP, Memory, Application)
- N+1 query detection with suggestions
- Interactive dashboard with charts and visualizations
- Route-based analytics and performance metrics
- Alert system for slow requests, high memory, and N+1 queries
- Modern, responsive UI with dark mode support
- Request detail page with timeline, queries, and HTTP calls breakdown
- Configurable thresholds for slow requests, memory, and N+1 detection
- Baseline calculation for route performance
- Middleware-based integration (easy to add to existing routes)

### Features

- Production-safe profiling (unlike Telescope)
- Real-time performance monitoring
- Comprehensive request analysis
- Beautiful, modern dashboard interface
- Full Laravel 11 compatibility
- Full Laravel 12 compatibility


