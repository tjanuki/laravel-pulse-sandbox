<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    /**
     * A simple test endpoint to verify API routing is working.
     */
    public function test()
    {
        return response()->json([
            'message' => 'API is working correctly',
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}