<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class BookManage
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
        $validator = Validator::make(
            $req->all(), [
                'title' => 'required',
                'author' => 'required',
                'publisher' => 'required',
                'pubdate' => 'required',
                'cover' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errcode' => 12,
                'data' => '缺少必要参数'
            ]);
        }

        return $next($req);
    }
}
