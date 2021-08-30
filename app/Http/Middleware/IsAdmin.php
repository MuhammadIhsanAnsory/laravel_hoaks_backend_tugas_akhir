<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class IsAdmin
{

    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        }

        if ($user->role == 'admin') {
            if ($user->blocked == 1) {
                return response()->json([
                'status' => false,
                'message' => 'User has been blocked, call admin for open block'
                ], 400);
            } else {
                return $next($request);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'You are not admin'
            ], 400);
        }
    }
}
