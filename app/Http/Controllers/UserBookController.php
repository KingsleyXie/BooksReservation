<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserBook as UserBookResource;

class UserBookController extends Controller
{
	public function index()
	{
		$books = DB::table('books')
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
			'data' => DB::table('books')->count()
		]);
	}

	public function pagedIndex($page, $limit)
	{
		$books = DB::table('books')
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
		$books = DB::table('books')
			->whereIn('id', $this->getIdsFromElasticSearch($req->keyword))
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
		$count = DB::table('books')
			->whereIn('id', $this->getIdsFromElasticSearch($req->keyword))
			->count();

		return response()->json([
			'errcode' => 0,
			'data' => $count
		]);
	}

	public function pagedSearch(Request $req, $page, $limit)
	{
		$books = DB::table('books')
			->whereIn('id', $this->getIdsFromElasticSearch($req->keyword))
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



	public function getIdsFromElasticSearch($keyword)
	{
		$redis_key = "key$keyword";
		if (Redis::exists($redis_key))
			return json_decode(Redis::get($redis_key), true);

		$books = Book::complexSearch([
			'body' => [
				'_source' => ['id'],
				"min_score" => 0.3,
				'query' => ['multi_match' => [
					'query' => $keyword,
					'fields' => ['title^5', 'author^3', 'publisher']
				]]
			]
		]);

		$ids = [];
		foreach ($books as $value) {
			$ids[] = $value->id;
		}

		Redis::set($redis_key, json_encode($ids));
		Redis::expire($redis_key, 300);

		return $ids;
	}
}
