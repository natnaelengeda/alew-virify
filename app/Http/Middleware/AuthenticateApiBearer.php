<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiBearer
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('api.bearer_token');

        if (! is_string($configuredToken) || $configuredToken === '') {
            return response()->json([
                'status' => 'failed',
                'message' => 'API authentication is not configured.',
            ], 503);
        }

        $provided = $request->bearerToken();

        if (! is_string($provided) || $provided === '' || ! hash_equals($configuredToken, $provided)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
