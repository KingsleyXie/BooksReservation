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

		if ($book == null) {
			return response()->json([
				'errcode' => 9,
				'errmsg' => '未找到对应书籍信息'
			]);
		}

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

	private function add($book)
	{
		$record = [
			'isbn' => $book[0],
			'title' => $book[1],
			'author' => $book[2],
			'publisher' => $book[3],
			'pubdate' => $book[4],
			'cover' => $book[5]
		];

		if (isset($book[6]))
			$record['quantity'] = $book[6];

		return DB::table('book')
			->insertGetId($record);
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
		$affected = DB::table('book')
			->where('isbn', $req->isbn)
			->increment('quantity');

		if ($affected == 1) {
			return response()->json([
				'errcode' => 0,
				'data' => '书籍添加成功'
			]);
		}

		// Book is not in the database
		$book = Douban::getBook($req->isbn);

		if (!$book) {
			return response()->json([
				'errcode' => 10,
				'errmsg' => '请稍后手动录入此书相关数据'
			]);
		}

		$bookID = $this->add($book);

		return response()->json([
			'errcode' => 0,
			'data' => '书籍新增成功，编号为 ' . $bookID
		]);
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
		$book = Douban::getBook($ISBN);

		if (!$book) {
			return response()->json([
				'errcode' => 11,
				'errmsg' => '未找到对应书籍信息'
			]);
		}

		return response()->json([
			'errcode' => 0,
			'data' => $book
		]);
	}
}
