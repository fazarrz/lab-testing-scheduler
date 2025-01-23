<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // Ambil token dari cookie atau request
            $token = $request->cookie('token');
            JWTAuth::setToken($token)->authenticate();

            $user = JWTAuth::user(); // Mendapatkan user yang sedang login

            // Cek apakah role user sesuai
            if (!in_array($user->role, $roles)) {
                return redirect('/unauthorized')->with('error', 'You are not authorized to access this page.');
            }

            return $next($request);
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'You must be logged in.');
        }
    }
}
