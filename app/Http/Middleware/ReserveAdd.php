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

        // For list checker
        $req->list = $books;

        // For modify filter & collision checker
        $req->books = $books;

        return $next($req);
    }
}
