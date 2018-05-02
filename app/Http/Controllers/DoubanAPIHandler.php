<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DoubanAPIHandler extends Controller
{
	public static function getBook($ISBN)
	{
		$book = @file_get_contents(
			'https://api.douban.com/v2/book/isbn/' . $ISBN
		);
		if (!$book) return false;

		$book = json_decode($book, true);

		// Specially write to handle books with multiple authors
		$author = $book['author'][0];
		if (count($book['author']) > 1)
			$author .= ' 等';

		return [
			$ISBN,
			$book['title'],
			$author,
			$book['publisher'],
			$book['pubdate'],
			$book['images']['large']
		];
	}
}
