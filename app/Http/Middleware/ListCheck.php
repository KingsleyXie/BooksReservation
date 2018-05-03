<?php

namespace App\Http\Middleware;

use Closure;

class ListCheck
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
        $list = $req->list;

        $list = array_filter($list, function($val) {
            return $val != 0;
        });

        if (empty($list)) {
            return response()->json([
                'errcode' => 1,
                'errmsg' => '列表内未包含有效书籍'
            ]);
        }

        if ($list != array_unique($list)) {
            return response()->json([
                'errcode' => 2,
                'errmsg' => '列表中存在重复书籍'
            ]);
        }

        return $next($req);
    }
}
