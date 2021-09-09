<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
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
        try{
            $user = JWTAuth::parseToken()->authenticate();
        }catch(Exception $e){
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'status' => 'Token is Invalid'
                ]);
            }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token is Expired']);
            }else if($user->blocked == true || $user->blocked == 1 || $user->blocked == '1'){
                return response()->json(['status' => 'Pengguna diblokir silahkan hubungi admin untuk mengaktifkan kembali']);
            }else{
                return response()->json(['status' => 'Authorization Token Not Found']);
            }
        }

        return $next($request);
    }
}
