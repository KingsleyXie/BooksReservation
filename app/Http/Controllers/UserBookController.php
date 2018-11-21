<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserBook as UserBookResource;

class UserBookController extends Controller
{
	public function index()
	{
		$books = DB::table('book')
			->orderByRaw(
				'quantity = 0,
				quantity > 21,
				quantity DESC,
				id ASC'
			)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => UserBookResource::collection($books)
		]);
	}

	public function countedIndex()
	{
		return response()->json([
			'errcode' => 0,
			'data' => DB::table('book')->count()
		]);
	}

	public function pagedIndex($page, $limit)
	{
		$books = DB::table('book')
			->orderByRaw(
				'quantity = 0,
				quantity > 21,
				quantity DESC,
				id ASC'
			)
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
		// Book::addAllToIndex();
		// dd(Book::searchByQuery([
		// 	'multi_match' => [
		// 		'title' => $req->keyword,
		// 		'author' => $req->keyword,
		// 		'publisher' => $req->keyword
		// 	]
		// ]));

		// dd(Book::search($req->keyword));

		// dd(Book::complexSearch(array(
		// 	'body' => array(
		// 		'query' => array(
		// 			'multi_match' => array(
		// 				"query" => $req->keyword,
		// 				"fields" => ['title', 'author', 'publisher']
		// 			)
		// 		)
		// 	)
		// )));

		$books = DB::table('book')
			->whereRaw(
				'(title LIKE ?) OR (author LIKE ?)',
				[
					'%' . $req->keyword . '%',
					'%' . $req->keyword . '%'
				]
			)
			->orderByRaw(
				'quantity = 0,
				quantity > 21,
				quantity DESC,
				id ASC'
			)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => UserBookResource::collection($books)
		]);
	}

	public function countedSearch(Request $req)
	{
		$count = DB::table('book')
			->whereRaw(
				'(title LIKE ?) OR (author LIKE ?)',
				[
					'%' . $req->keyword . '%',
					'%' . $req->keyword . '%'
				]
			)
			->count();

		return response()->json([
			'errcode' => 0,
			'data' => $count
		]);
	}

	public function pagedSearch(Request $req, $page, $limit)
	{
		$books = DB::table('book')
			->whereRaw(
				'(title LIKE ?) OR (author LIKE ?)',
				[
					'%' . $req->keyword . '%',
					'%' . $req->keyword . '%'
				]
			)
			->orderByRaw(
				'quantity = 0,
				quantity > 21,
				quantity DESC,
				id ASC'
			)
			->skip(($page - 1) * $limit)
			->take($limit)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => UserBookResource::collection($books)
		]);
	}
}
