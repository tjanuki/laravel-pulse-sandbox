<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusMetricController extends Controller
{
    /**
     * Store a new status metric.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source' => 'required|string|max:255',
            'server_name' => 'nullable|string|max:255',
            'server_ip' => 'nullable|string|max:45',
            'environment' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'status' => 'nullable|string|in:ok,warning,critical',
            'metadata' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the authenticated server from the request (set by VerifyServerApiKey middleware)
        $server = $request->server;
        
        // Use server details from the registration, or from the request if available
        $serverName = $request->server_name ?? $server->server_name;
        $serverIp = $request->server_ip ?? $server->server_ip;
        $environment = $request->environment ?? $server->environment;
        $region = $request->region ?? $server->region;
        
        // Set expiry date for 60 days from now
        $expiresAt = now()->addDays(60);
        
        // Create new metric
        $metric = StatusMetric::create([
            'source' => $request->source,
            'server_name' => $serverName,
            'server_ip' => $serverIp,
            'environment' => $environment,
            'region' => $region,
            'key' => $request->key,
            'value' => $request->value,
            'status' => $request->status ?? StatusMetric::STATUS_OK,
            'metadata' => $request->metadata,
            'expires_at' => $expiresAt,
        ]);
        
        return response()->json([
            'message' => 'Status metric recorded successfully',
            'data' => $metric
        ], 201);
    }
    
    /**
     * Get latest metrics for a specific source.
     */
    public function getLatest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source' => 'nullable|string|max:255',
            'server_name' => 'nullable|string|max:255',
            'environment' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'key' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:ok,warning,critical',
            'only_alerts' => 'nullable|boolean',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $query = new StatusMetric();
        
        // Apply filters
        if ($request->has('source')) {
            $query = $query->where('source', $request->source);
        }
        
        if ($request->has('server_name')) {
            $query = $query->byServer($request->server_name);
        }
        
        if ($request->has('environment')) {
            $query = $query->byEnvironment($request->environment);
        }
        
        if ($request->has('region')) {
            $query = $query->byRegion($request->region);
        }
        
        if ($request->has('key')) {
            $query = $query->where('key', $request->key);
        }
        
        if ($request->has('status')) {
            $query = $query->where('status', $request->status);
        }
        
        // Option to only return alerts (warning or critical)
        if ($request->boolean('only_alerts')) {
            $query = $query->onlyAlerts();
        }
        
        $limit = $request->input('limit', 10);
        
        // Get results
        $metrics = $query->latest()->take($limit)->get();
        
        // Group metrics by server for better organization
        $groupedMetrics = $metrics->groupBy('server_name');
        
        return response()->json([
            'data' => $metrics,
            'grouped_by_server' => $groupedMetrics
        ]);
    }
}