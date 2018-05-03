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

        // For list checker
        $req->list = $prebooks;

        // For collision checker
        $req->books = array_diff($req->books, $prebooks);

        return $next($req);
    }
}
