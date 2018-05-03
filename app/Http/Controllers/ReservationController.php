<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReservationController extends Controller
{
	public function index()
	{
		$reservations = DB::table('reservation')
			->orderBy('submit_time', 'DESC')
			->get();

		return response()->json([
			'errcode' => 0,
			'data' => $reservations
		]);
	}

	public function searchByStuNo($stuno)
	{
		$reservation = DB::table('reservation')
			->where('stu_num', $stuno)
			->first();

		if ($reservation == null) {
			return response()->json([
				'errcode' => 1,
				'errmsg' => '未找到对应订单信息'
			]);
		}

		return response()->json([
			'errcode' => 0,
			'data' => $reservation
		]);
	}
}
