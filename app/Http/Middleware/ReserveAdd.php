<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

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
        $validator = Validator::make(
            $req->all(), [
                'stuname' => 'required',
                'stuno' => 'required',
                'dorm' => 'required',
                'contact' => 'required',
                'takeday' => 'required',
                'taketime' => 'required',
                'book0' => 'required',
                'book1' => 'required',
                'book2' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errcode' => 1,
                'errmsg' => '缺少必要参数'
            ]);
        }

        $books = [
            $req->book0,
            $req->book1,
            $req->book2
        ];

        // For list checker
        $req->list = $books;

        // For modify filter & collision checker
        $req->books = $books;

        return $next($req);
    }
}
