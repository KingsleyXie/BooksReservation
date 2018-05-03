<?php

namespace App\Http\Middleware;

use Closure;

class ReserveAdd
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
        $books = [
            $req->book0,
            $req->book1,
            $req->book2
        ];

        $books = array_filter($books, function($val) {
            return $val != 0;
        });

        if (empty($books))
            return response()->json([
                'errcode' => 1,
                'errmsg' => '预约列表内未包含有效书籍'
            ]);

        if ($books != array_unique($books))
            return response()->json([
                'errcode' => 2,
                'errmsg' => '预约列表中存在重复书籍'
            ]);

        $req->books = $books;
        return $next($req);
    }
}
