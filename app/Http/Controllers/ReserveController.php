<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
	public function add(Request $req)
	{
		$collision = DB::table('book')
			->where('quantity', '<=', 0)
			->whereIn('id', $req->books)
			->exists();

		return response()->json([
			'errcode' => 0,
			'data' => $collision
		]);
	}

	public function modifyById(Request $req, $id)
	{
		$collision = DB::table('book')
			->where('quantity', '<=', 0)
			->whereIn('id', $req->books)
			->exists();

		return response()->json([
			'errcode' => 0,
			'data' => $collision
		]);
	}
}
