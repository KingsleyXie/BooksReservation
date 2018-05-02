<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

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
        if (!$req->session()->exists('admin'))
            return response()->json([
                'errcode' => -1,
                'errmsg' => '请登录系统！'
            ]);

        return $next($req);
    }
}
