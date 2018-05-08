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

		if (count($book['author']) == 0) return false;

		$author = $book['author'][0];
		if (count($book['author']) > 1)
			$author .= ' 等';

		// Handle Douban image 403 forbidden problem
		// with replacement of previous url
		//
		// Another solution: replace it with
		// https://images.weserv.nl/?url=douban_image_path
		$cover = preg_replace(
			'/(.*?)\/view\/subject\/l\/public\/(.*)/',
			'$1/lpic/$2',
			$book['images']['large']
		);

		return [
			'isbn' => $ISBN,
			'author' => $author,
			'cover' => $cover,
			'title' => $book['title'],
			'publisher' => $book['publisher'],
			'pubdate' => $book['pubdate']
		];
	}
}
