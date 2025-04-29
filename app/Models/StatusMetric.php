<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',      // Where the metric came from (e.g., 'blogs', 'users', 'external-app')
        'key',         // Type of metric (e.g., 'count', 'response_time', 'error_rate')
        'value',       // The actual value (stored as string for flexibility)
        'status',      // Optional status indicator (e.g., 'ok', 'warning', 'critical')
        'metadata',    // JSON field for additional context
        'expires_at',  // When this metric should be purged (for 60-day retention)
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];
}