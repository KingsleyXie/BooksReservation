<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

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
        $validator = Validator::make(
            $req->all(), [
                'prebook0' => 'required',
                'prebook1' => 'required',
                'prebook2' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errcode' => 2,
                'errmsg' => '缺少必要参数'
            ]);
        }

        $prebooks = [
            $req->prebook0,
            $req->prebook1,
            $req->prebook2
        ];

        // For list checker
        $req->list = $prebooks;

        // For collision checker
        $req->books = array_diff($req->books, $prebooks);

        return $next($req);
    }
}
