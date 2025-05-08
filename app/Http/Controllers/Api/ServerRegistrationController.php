<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerRegistrationController extends Controller
{
    /**
     * Register a new server.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'server_name' => 'required|string|max:255|unique:server_registrations',
            'server_ip' => 'nullable|string|max:45',
            'environment' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate a unique API key for the server
        $apiKey = ServerRegistration::generateApiKey();

        // Create the server registration
        $server = ServerRegistration::create([
            'server_name' => $request->server_name,
            'server_ip' => $request->server_ip ?? $request->ip(),
            'environment' => $request->environment ?? config('app.env', 'production'),
            'region' => $request->region,
            'api_key' => $apiKey,
            'description' => $request->description,
            'metadata' => $request->metadata,
            'active' => true,
            'last_reported_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Server registered successfully',
            'data' => [
                'server_id' => $server->id,
                'server_name' => $server->server_name,
                'api_key' => $apiKey,
            ]
        ], 201);
    }
    
    /**
     * List all registered servers.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'environment' => 'nullable|string|max:50',
            'active_only' => 'nullable|boolean',
            'inactive_threshold' => 'nullable|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $query = new ServerRegistration();
        
        // Filter by environment if provided
        if ($request->has('environment')) {
            $query = $query->byEnvironment($request->environment);
        }
        
        // Filter active only servers if requested
        if ($request->boolean('active_only', false)) {
            $query = $query->active();
        }
        
        // Filter inactive servers if threshold provided
        if ($request->has('inactive_threshold')) {
            $query = $query->inactive($request->integer('inactive_threshold'));
        }
        
        // Get servers with their last reported time and status
        $servers = $query->orderBy('server_name')->get()->map(function ($server) {
            $data = $server->toArray();
            $data['inactive'] = $server->last_reported_at === null || 
                                $server->last_reported_at->diffInMinutes(now()) > 30;
            
            // Don't expose the API key in the response
            unset($data['api_key']);
            
            return $data;
        });
        
        // Group servers by environment
        $grouped = $servers->groupBy('environment');
        
        return response()->json([
            'data' => $servers,
            'grouped' => $grouped,
            'total' => $servers->count(),
            'active' => $servers->where('active', true)->count(),
            'inactive' => $servers->where('inactive', true)->count(),
        ]);
    }
    
    /**
     * Get server details.
     */
    public function show($id)
    {
        $server = ServerRegistration::findOrFail($id);
        
        $data = $server->toArray();
        
        // Don't expose the API key in the response
        unset($data['api_key']);
        
        // Add inactive status
        $data['inactive'] = $server->last_reported_at === null || 
                            $server->last_reported_at->diffInMinutes(now()) > 30;
        
        // Get latest metrics for this server
        $latestMetrics = $server->metrics()
                                ->latest()
                                ->take(5)
                                ->get();
        
        // Get latest alerts for this server
        $alerts = $server->metrics()
                          ->onlyAlerts()
                          ->latest()
                          ->take(5)
                          ->get();
        
        return response()->json([
            'data' => $data,
            'latest_metrics' => $latestMetrics,
            'alerts' => $alerts,
        ]);
    }
    
    /**
     * Deactivate a server.
     */
    public function deactivate($id)
    {
        $server = ServerRegistration::findOrFail($id);
        $server->update(['active' => false]);
        
        return response()->json([
            'message' => 'Server deactivated successfully',
            'data' => $server,
        ]);
    }
    
    /**
     * Reactivate a server.
     */
    public function activate($id)
    {
        $server = ServerRegistration::findOrFail($id);
        $server->update(['active' => true]);
        
        return response()->json([
            'message' => 'Server activated successfully',
            'data' => $server,
        ]);
    }
}