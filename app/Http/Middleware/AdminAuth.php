<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($req, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json([
                'errcode' => -1,
                'errmsg' => '请登录系统！'
            ]);
        }

        return $next($req);
    }
}
