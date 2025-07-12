<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class HealthController extends Controller
{
    /**
     * Health check endpoint for Docker and monitoring
     *
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $checks = [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'checks' => []
        ];

        // Database connectivity check
        try {
            DB::connection()->getPdo();
            $checks['checks']['database'] = 'ok';
        } catch (Exception $e) {
            $checks['checks']['database'] = 'error';
            $checks['status'] = 'error';
        }

        // Cache check
        try {
            Cache::put('health_check', 'ok', 10);
            $cacheValue = Cache::get('health_check');
            $checks['checks']['cache'] = $cacheValue === 'ok' ? 'ok' : 'error';
        } catch (Exception $e) {
            $checks['checks']['cache'] = 'error';
            $checks['status'] = 'error';
        }

        // Basic app check
        $checks['checks']['app'] = 'ok';
        
        $statusCode = $checks['status'] === 'ok' ? 200 : 503;
        
        return response()->json($checks, $statusCode);
    }

    /**
     * Simple health check for lightweight monitoring
     *
     * @return JsonResponse
     */
    public function simple(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString()
        ]);
    }
}
