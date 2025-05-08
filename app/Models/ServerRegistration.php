<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServerRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_name',
        'server_ip',
        'environment',
        'region',
        'api_key',
        'description',
        'metadata',
        'active',
        'last_reported_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'active' => 'boolean',
        'last_reported_at' => 'datetime',
    ];

    /**
     * Generate a unique API key for the server.
     */
    public static function generateApiKey(): string
    {
        return Str::random(64);
    }

    /**
     * Find a server registration by its API key.
     */
    public static function findByApiKey(string $apiKey)
    {
        return static::where('api_key', $apiKey)->where('active', true)->first();
    }

    /**
     * Mark the server as having reported.
     */
    public function markReported(): void
    {
        $this->update(['last_reported_at' => now()]);
    }

    /**
     * Scope a query to only include active servers.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include servers from a specific environment.
     */
    public function scopeByEnvironment($query, $environment)
    {
        return $query->where('environment', $environment);
    }

    /**
     * Scope a query to only include servers that haven't reported recently.
     */
    public function scopeInactive($query, $minutes = 30)
    {
        return $query->where('active', true)
            ->where(function ($q) use ($minutes) {
                $q->whereNull('last_reported_at')
                    ->orWhere('last_reported_at', '<', now()->subMinutes($minutes));
            });
    }

    /**
     * Get metrics for this server.
     */
    public function metrics()
    {
        return $this->hasMany(StatusMetric::class, 'server_name', 'server_name');
    }
}