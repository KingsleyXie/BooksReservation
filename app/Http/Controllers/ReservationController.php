<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Resources\Reservation as ReservationResource;
use App\Http\Resources\UserBook as UserBookResource;

class ReservationController extends Controller
{
	public function index()
	{
		$reservations = DB::table('reservation')
			->orderBy('submited', 'DESC')
			->get();

		$dataset = [];
		foreach ($reservations as $reservation) {
			$books = DB::table('book')
				->whereIn('id', [
					$reservation->book0,
					$reservation->book1,
					$reservation->book2
				])
				->get();

			$data = new ReservationResource($reservation);
			$data = json_decode(json_encode($data), true);

			$books = UserBookResource::collection($books);
			$data['books'] = json_decode(json_encode($books), true);

			array_push($dataset, $data);
		}

		return response()->json([
			'errcode' => 0,
			'data' => $dataset
		]);
	}

	public function searchByStuno($stuno)
	{
		$reservation = DB::table('reservation')
			->where('stuno', $stuno)
			->first();

		if ($reservation == null) {
			return response()->json([
				'errcode' => 1,
				'errmsg' => '未找到对应订单信息'
			]);
		}

		$books = DB::table('book')
			->whereIn('id', [
				$reservation->book0,
				$reservation->book1,
				$reservation->book2
			])
			->get();

		$data = new ReservationResource($reservation);
		$data = json_decode(json_encode($data), true);

		$books = UserBookResource::collection($books);
		$data['books'] = json_decode(json_encode($books), true);

		return response()->json([
			'errcode' => 0,
			'data' => $data
		]);
	}
}
