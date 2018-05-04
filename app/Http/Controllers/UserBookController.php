<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserBook as UserBookResource;

class UserBookController extends Controller
{
	public function index()
	{
		$books = DB::table('book')
			->orderByRaw('quantity > 0 DESC, id DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => UserBookResource::collection($books)
		]);
	}

	public function getByPage($page, $limit)
	{
		$books = DB::table('book')
			->orderByRaw('quantity > 0 DESC, id DESC')
			->skip(($page - 1) * $limit)
			->take($limit)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => UserBookResource::collection($books)
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
			'data' => UserBookResource::collection($books)
		]);
	}
}
