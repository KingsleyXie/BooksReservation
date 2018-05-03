<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollisionCheck
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
        $collision = DB::table('book')
            ->where('quantity', '<=', 0)
            ->whereIn('id', $req->books)
            ->exists();

        if ($collision) {
            $notice = json_encode([
                $req->stuNo, $req->stuName, $req->books
            ]);
            Log::channel('collision')->notice($notice);

            return response()->json([
                'errcode' => 1,
                'data' => '列表中存在余量为 0 的书籍'
            ]);
        }

        return $next($req);
    }
}
