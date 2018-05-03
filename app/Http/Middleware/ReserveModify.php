<?php

namespace App\Http\Middleware;

use Closure;

class ReserveModify
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
        $prebooks = [
            $req->prebook0,
            $req->prebook1,
            $req->prebook2
        ];

        $prebooks = array_filter($prebooks, function($val) {
            return $val != 0;
        });

        if (empty($prebooks))
            return response()->json([
                'errcode' => 1,
                'errmsg' => '修改列表内未包含有效书籍'
            ]);

        if ($prebooks != array_unique($prebooks))
            return response()->json([
                'errcode' => 2,
                'errmsg' => '修改列表中存在重复书籍'
            ]);

        $req->books = array_diff($req->books, $prebooks);
        return $next($req);
    }
}
