<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Controllers\DoubanAPIHandler as Douban;

class AdminBookController extends Controller
{
	public function index()
	{
		$books = DB::table('book')
			->orderBy('id', 'DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => $books
		]);
	}

	public function getById($id)
	{
		$book = DB::table('book')
			->where('id', $id)
			->first();

		return response()->json([
			'errcode' => 0,
			'data' => $book
		]);
	}

	public function getByPage($page, $limit)
	{
		$books = DB::table('book')
			->orderBy('id', 'DESC')
			->skip(($page - 1) * $limit)
			->take($limit)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => $books
		]);
	}

	private function add(
		$isbn, $title, $author, $publisher, $pubdate, $cover, $quantity
	)
	{
		return DB::table('book')
			->insertGetId([
				'isbn' => $isbn,
				'title' => $title,
				'author' => $author,
				'publisher' => $publisher,
				'pubdate' => $pubdate,
				'cover' => $cover,
				'quantity' => $quantity
			]);
	}

	public function addByRaw(Request $req)
	{
		$bookID = $this->add([
			$req->isbn,
			$req->title,
			$req->author,
			$req->publisher,
			$req->pubdate,
			$req->cover,
			$req->quantity
		]);

		return response()->json([
			'errcode' => 0,
			'data' => $bookID
		]);
	}

	public function addByISBN(Request $req)
	{
		//Todo
	}

	public function updateById(Request $req, $id)
	{
		DB::table('book')
			->where('id', $id)
			->update([
				'title' => $req->title,
				'author' => $req->author,
				'publisher' => $req->publisher,
				'pubdate' => $req->pubdate,
				'cover' => $req->cover,
				'quantity' => $req->quantity
			]);

		return response()->json([
			'errcode' => 0
		]);
	}

	public function searchByISBN($ISBN)
	{
		$result = Douban::getBook($ISBN);

		if (!$result) {
			return response()->json([
				'errcode' => 1,
				'errmsg' => '未找到对应书籍信息，请手动录入相关数据'
			]);
		}

		return response()->json([
			'errcode' => 0,
			'data' => $result
		]);
	}
}
