<?php

namespace App\Http\Middleware;

use App\Models\ServerRegistration;
use Closure;
use Illuminate\Http\Request;

class VerifyServerApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the API key is provided in the request header
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is missing',
                'message' => 'Please provide a valid API key in the X-API-Key header'
            ], 401);
        }
        
        // Find the server by API key
        $server = ServerRegistration::findByApiKey($apiKey);
        
        if (!$server) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or the server is inactive'
            ], 401);
        }
        
        // Update the last reported time
        $server->markReported();
        
        // Attach the server model to the request for controllers to access
        $request->attributes->set('serverRegistration', $server);
        
        return $next($request);
    }
}