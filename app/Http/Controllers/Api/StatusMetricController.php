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
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'status' => 'nullable|string|in:ok,warning,critical',
            'metadata' => 'nullable|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set expiry date for 60 days from now
        $expiresAt = now()->addDays(60);
        
        // Create new metric
        $metric = StatusMetric::create([
            'source' => $request->source,
            'key' => $request->key,
            'value' => $request->value,
            'status' => $request->status ?? 'ok',
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
            'source' => 'required|string|max:255',
            'key' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $query = StatusMetric::where('source', $request->source);
        
        if ($request->has('key')) {
            $query->where('key', $request->key);
        }
        
        $limit = $request->input('limit', 10);
        
        $metrics = $query->latest()->take($limit)->get();
        
        return response()->json([
            'data' => $metrics
        ]);
    }
}