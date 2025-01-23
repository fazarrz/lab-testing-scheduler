<?php

namespace App\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if token is in the Authorization header
        $token = $request->bearerToken();
        
        // If not in header, check if token is in cookies
        if (!$token) {
            $token = $request->cookie('token');
        }

        if (!$token) {
            return redirect()->route('login')->with('error', 'Token not provided');
        }

        try {
            // Attempt to parse and authenticate the token
            JWTAuth::setToken($token)->authenticate();
        } catch (JWTException $e) {
            return redirect()->route('login')->with('error', 'Token is invalid or expired');
        }

        return $next($request);
    }
}
