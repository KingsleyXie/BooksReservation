<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
	public function add(Request $req)
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
				'data' => '列表中存在余量为0的书籍'
			]);
		}
	}

	public function modifyById(Request $req, $id)
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
				'data' => '列表中存在余量为0的书籍'
			]);
		}
	}
}
