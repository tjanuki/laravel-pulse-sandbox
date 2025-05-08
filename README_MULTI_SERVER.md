# Multi-Server Metrics Monitoring Extension for Laravel Pulse

This extension enhances Laravel Pulse with the ability to collect and display metrics from multiple servers in a centralized dashboard.

## Features

- **Server Registration**: Register servers with unique API keys for secure metrics reporting
- **Centralized Monitoring**: View metrics from all your servers in a single dashboard
- **Flexible Filtering**: Filter metrics by server, environment, region, or type
- **Alerting**: Visual indicators for servers with warning or critical metrics
- **Real-time Updates**: Live updates with configurable polling intervals

## Installation

1. Run the migrations to add server-related fields to the metrics table:

```bash
php artisan migrate
```

This will create:
- New fields in the `status_metrics` table for server identification
- A new `server_registrations` table for tracking servers

## Usage

### Registering a Server

Before a server can submit metrics, it needs to be registered:

```bash
# Register a new server
curl -X POST http://your-pulse-app.test/api/servers/register \
  -H "Content-Type: application/json" \
  -d '{"server_name": "web-server-01", "environment": "production", "region": "us-east"}'
```

This will return a unique API key that the server should use for all metrics submissions.

### Submitting Metrics

Servers can submit metrics using the API:

```bash
# Submit a metric
curl -X POST http://your-pulse-app.test/api/status-metrics \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your-server-api-key" \
  -d '{
    "source": "app",
    "key": "response_time",
    "value": "120",
    "status": "ok"
  }'
```

### Viewing Metrics

Add the MultiServerMetricsCard to your Pulse dashboard in `resources/views/vendor/pulse/dashboard.blade.php`:

```blade
<x-pulse>
    <!-- Other cards -->
    <livewire:multi-server-metrics-card cols="6" rows="4" title="API Response Times" source="api" metricKey="response_time" />
    <livewire:multi-server-metrics-card cols="6" rows="4" title="Server Health" source="server" metricKey="health" />
    <!-- More cards -->
</x-pulse>
```

## Card Customization Options

The `MultiServerMetricsCard` component accepts several parameters:

- `title`: Display title for the card
- `source`: Filter metrics by source
- `metricKey`: Filter metrics by key
- `environment`: Initial environment filter
- `region`: Initial region filter
- `serverName`: Initial server filter
- `groupBy`: How to group metrics (server, environment, source, key)
- `showOnlyAlerts`: Whether to only show alerts
- `limit`: Maximum number of metrics to display
- `refreshInterval`: Polling interval in seconds

## Security

- Each server uses a unique API key for authentication
- Only registered and active servers can submit metrics
- API keys can be revoked at any time

## Client Libraries

For easy integration with your servers, you can use one of these client libraries:

- **Laravel**: Use the `laravel-pulse-client` package
- **Node.js**: Use the `node-pulse-metrics` package
- **Python**: Use the `python-pulse-client` package

## Best Practices

1. **Server Naming**: Use consistent server naming conventions
2. **Metric Organization**: Group related metrics under the same source
3. **Status Standards**: Use consistent status values (ok, warning, critical)
4. **Metadata**: Include relevant context in the metadata field

## Extending Further

This implementation can be extended with:

1. **Advanced Visualization**: Add charts and graphs for metric trends
2. **Notification System**: Send alerts via email, Slack, or SMS
3. **Historical Analysis**: Add time-based analysis of metric trends
4. **Custom Dashboards**: Allow users to create custom dashboard layouts

## Troubleshooting

- **API Key Issues**: Ensure the X-API-Key header is included in all requests
- **Missing Data**: Check that servers are actively reporting metrics
- **Filter Not Working**: Verify filter parameter spelling and values

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.