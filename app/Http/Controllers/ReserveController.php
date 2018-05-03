<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
	public function add(Request $req)
	{
		return response()->json([
			'errcode' => 0,
			'data' => array_values($req->books)
		]);
	}

	public function modifyById(Request $req, $id)
	{
		return response()->json([
			'errcode' => 0,
			'data' => array_values($req->books)
		]);
	}
}
