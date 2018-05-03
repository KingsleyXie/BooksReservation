<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
	public function add(Request $req)
	{
		$exists = DB::table('reservation')
			->where('stuno', $req->stuno)
			->exists();

		if ($exists) {
			return response()->json([
				'errcode' => 1,
				'data' => '该学号已存在预约订单！'
			]);
		}

		return response()->json([
			'errcode' => 0
		]);
	}

	public function modifyById(Request $req, $id)
	{
		$exists = DB::table('reservation')
			->where('stuno', $req->stuno)
			->exists();

		if (!$exists) {
			return response()->json([
				'errcode' => 1,
				'data' => '未找到该学号对应的订单信息！'
			]);
		}

		return response()->json([
			'errcode' => 0
		]);
	}
}
