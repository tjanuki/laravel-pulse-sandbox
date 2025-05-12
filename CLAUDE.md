# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This project is a Laravel Pulse sandbox that implements a multi-server metrics monitoring system. It extends Laravel Pulse with the ability to collect and display metrics from multiple servers in a centralized dashboard.

The main components are:
- StatusMetric model for storing various application and server metrics
- ServerRegistration system for tracking servers with API key authentication
- MetricsService for sending metrics to API endpoints
- Custom Pulse components for displaying multi-server metrics
- Command structure for collecting and reporting metrics

## Development Commands

### Installation and Setup

```bash
# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Create SQLite database (used by default)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Generate test data
php artisan blogs:generate
```

### Running the Application

```bash
# Start development server with queue worker and vite
composer run dev

# Start just the server
php artisan serve

# Process the queue
php artisan queue:work

# Run scheduler (in a separate terminal)
php artisan schedule:work
```

### Testing

```bash
# Run all tests
composer run test

# Run a specific test
php artisan test --filter=ExampleTest

# Test the metrics API
php artisan test:status-metrics-api

# Test direct status updates
php artisan test:direct-status-update
```

### Working with Metrics

```bash
# Send blog metrics to the status API
php artisan send-metrics:blogs

# Send external service metrics
php artisan send-metrics:external-service

# Update all status metrics
php artisan status:update-all

# Purge old status metrics
php artisan status:purge-old
```

## Architecture

### Metrics Flow

1. **Data Collection**: Metric data is gathered via various commands that extend `AbstractSendMetricsCommand`
2. **Processing**: The `MetricsService` processes metric values and determines their status (ok, warning, critical)
3. **Storage/Transmission**: Metrics can be stored locally in the database or sent to an external API endpoint
4. **Display**: Laravel Pulse cards and components visualize the metrics

### Multi-Server System

- Each server registers with the central Pulse server and receives an API key
- Servers send metrics using their API key for authentication
- The central server collects metrics from all registered servers
- Metrics are displayed with server identification and can be filtered by server, environment, or region

### Key Files

- `app/Console/Commands/AbstractSendMetricsCommand.php`: Base class for metric reporting commands
- `app/Services/MetricsService.php`: Core service for processing and sending metrics
- `app/Models/StatusMetric.php`: Model for storing metric data
- `app/Models/ServerRegistration.php`: Model for tracking registered servers
- `config/metrics.php`: Configuration for metrics thresholds and API endpoints

## Common Tasks

### Creating a New Metric Command

1. Create a new command that extends `AbstractSendMetricsCommand`
2. Implement the required abstract methods:
   - `getMetricsSource()`: Return a unique identifier for the metrics source
   - `getMetricsData()`: Return an array of metrics to send
3. Register the command in `app/Console/Kernel.php` for scheduling if needed

### Adding New Metrics Cards

1. Create a new Livewire component in `app/Livewire/Pulse/`
2. Create a corresponding blade template in `resources/views/livewire/pulse/`
3. Add the component to the Pulse dashboard in `resources/views/vendor/pulse/dashboard.blade.php`

### Adding New Server Registration

```bash
# Register a new server via API
curl -X POST http://your-app.test/api/servers/register \
  -H "Content-Type: application/json" \
  -d '{"server_name": "server-name", "environment": "environment", "region": "region"}'
```