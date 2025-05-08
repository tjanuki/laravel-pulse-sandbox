<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMetric extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    public const STATUS_OK = 'ok';
    public const STATUS_WARNING = 'warning';
    public const STATUS_CRITICAL = 'critical';
    
    protected $fillable = [
        'source',      // Where the metric came from (e.g., 'blogs', 'users', 'external-app')
        'server_name', // Server hostname or identifier (e.g., 'web-01', 'app-server-2')
        'server_ip',   // Server IP address for identification
        'environment', // Environment (e.g., 'production', 'staging', 'development')
        'region',      // Region or data center (e.g., 'us-east', 'eu-west')
        'key',         // Type of metric (e.g., 'count', 'response_time', 'error_rate')
        'value',       // The actual value (stored as string for flexibility)
        'status',      // Status indicator (e.g., 'ok', 'warning', 'critical')
        'metadata',    // JSON field for additional context
        'expires_at',  // When this metric should be purged (for 60-day retention)
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];
    
    /**
     * Scope a query to filter by server name.
     */
    public function scopeByServer($query, $serverName)
    {
        return $query->where('server_name', $serverName);
    }
    
    /**
     * Scope a query to filter by environment.
     */
    public function scopeByEnvironment($query, $environment)
    {
        return $query->where('environment', $environment);
    }
    
    /**
     * Scope a query to filter by region.
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }
    
    /**
     * Scope a query to only include metrics with warning or critical status.
     */
    public function scopeOnlyAlerts($query)
    {
        return $query->whereIn('status', [self::STATUS_WARNING, self::STATUS_CRITICAL]);
    }
}