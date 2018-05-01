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
			'code' => 0,
			'data' => $books
		]);
	}
}
