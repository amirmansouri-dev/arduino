<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Carbon\Carbon;

class RefreshTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = JWTAuth::getToken();

        if ($token) {
            try {
                // Refresh the token
                $refreshedToken = JWTAuth::refresh($token);

                // Set the refreshed token as the new token in the request
                JWTAuth::setToken($refreshedToken);

                // Add the new token to the response headers
                $response = $next($request);
                $response->headers->set('Authorization', 'Bearer ' . $refreshedToken);

                // Optionally, set a new expiration time in the cookie or local storage
                // This step depends on how you handle token storage in your frontend

                return $response;
            } catch (\Exception $e) {
                // Token refresh failed, return unauthorized error
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        // No token found, return unauthorized error
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
