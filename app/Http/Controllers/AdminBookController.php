<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminBookController extends Controller
{
	public function index()
	{
		$books = DB::table('book')->get();
		return response()->json([
			'errcode' => 0,
			'data' => $books
		]);
	}

	public function searchByISBN($ISBN)
	{
		$result = @file_get_contents(
			'https://api.douban.com/v2/book/isbn/' . $ISBN
		);

		if (!$result) {
			return response()->json([
				'errcode' => 1,
				'errmsg' => '未找到对应书籍信息，请手动录入相关数据'
			]);
		}

		$result = json_decode($result, true);

		// Specially write to handle books with multiple authors
		$author = $result['author'][0];
		if (count($result['author']) > 1) {
			$author .= ' 等';
		}

		return response()->json([
			'errcode' => 0,
			'data' => [
				'title' => $result['title'],
				'author' => $author,
				'publisher' => $result['publisher'],
				'pub_date' => $result['pubdate'],
				'cover' => $result['images']['large']
			]
		]);
	}
}
