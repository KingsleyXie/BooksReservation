<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserBookController extends Controller
{
	public function index()
	{
		$books = DB::table('book')
			->orderByRaw('quantity > 0 DESC, id DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => $books
		]);
	}

	public function search(Request $req)
	{
		$books = DB::table('book')
			->whereRaw(
				'(title LIKE ?) OR (author LIKE ?)',
				[
					'%' . $req->keyword . '%',
					'%' . $req->keyword . '%'
				]
			)
			->orderByRaw('quantity > 0 DESC, id DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => $books
		]);
	}
}
