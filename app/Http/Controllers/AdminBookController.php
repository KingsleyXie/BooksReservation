<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminBook as AdminBookResource;
use App\Http\Controllers\DoubanAPIHandler as Douban;

class AdminBookController extends Controller
{
	public function index()
	{
		$books = DB::table('books')
			->orderBy('id', 'DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => AdminBookResource::collection($books)
		]);
	}

	public function getById($id)
	{
		$book = DB::table('books')
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
			'data' => new AdminBookResource($book)
		]);
	}

	public function getByPage($page, $limit)
	{
		$books = DB::table('books')
			->orderBy('id', 'DESC')
			->skip(($page - 1) * $limit)
			->take($limit)
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => AdminBookResource::collection($books)
		]);
	}

	private function add($book)
	{
		$record = [
			'title' => $book['title'],
			'author' => $book['author'],
			'publisher' => $book['publisher'],
			'pubdate' => $book['pubdate'],
			'cover' => $book['cover']
		];

		// Add By ISBN Without Quantity
		if (isset($book['isbn']))
			$record['isbn'] = $book['isbn'];

		// Add By Raw Without ISBN
		if (isset($book['quantity']))
			$record['quantity'] = $book['quantity'];

		$bookId = DB::table('books')->insertGetId($record);

		$book = Book::find($bookId);
		$book->addToIndex();

		return $bookId;
	}

	public function addByRaw(Request $req)
	{
		$bookId = $this->add([
			'title' => $req->title,
			'author' => $req->author,
			'publisher' => $req->publisher,
			'pubdate' => $req->pubdate,
			'cover' => $req->cover,
			'quantity' => $req->quantity
		]);

		return response()->json([
			'errcode' => 0,
			'data' => '书籍新增成功，编号为 ' . $bookId
		]);
	}

	public function addByISBN(Request $req)
	{
		$affected = DB::table('books')
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
				'errmsg' => '请稍后手动录入此书信息'
			]);
		}

		$bookId = $this->add($book);

		return response()->json([
			'errcode' => 0,
			'data' => '书籍新增成功，编号为 ' . $bookId
		]);
	}

	public function updateById(Request $req, $id)
	{
		DB::table('books')
			->where('id', $id)
			->update([
				'title' => $req->title,
				'author' => $req->author,
				'publisher' => $req->publisher,
				'pubdate' => $req->pubdate,
				'cover' => $req->cover,
				'quantity' => $req->quantity,
				'updated' => DB::raw('NOW()')
			]);

		Book::reindex();

		return response()->json([
			'errcode' => 0,
			'data' => '书籍信息修改成功'
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
			'data' => new AdminBookResource($book)
		]);
	}

	public function initElasticIndex()
	{
		Book::addAllToIndex();

		$keys = Redis::keys('key:*');
		if ($keys) Redis::del($keys);

		return view('index', ['info' => 'Elasticsearch 索引初始化成功！']);
	}

	public function resetElasticIndex()
	{
		Book::reindex();

		$keys = Redis::keys('key:*');
		if ($keys) Redis::del($keys);

		return view('index', ['info' => 'Elasticsearch 索引重置成功！']);
	}
}
