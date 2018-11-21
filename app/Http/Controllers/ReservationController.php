<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reservation as ReservationResource;
use App\Http\Resources\UserBook as UserBookResource;

class ReservationController extends Controller
{
	private function getData($reservation)
	{
		$books = DB::table('books')
			->whereIn('id', function($query) use ($reservation) {
				$query->select('book_id')
				->from('reservation_book')
				->where('reservation_id', $reservation->id);
			})
			->get();

		$data = new ReservationResource($reservation);
		$data = json_decode(json_encode($data), true);
		$data['books'] = UserBookResource::collection($books);

		return $data;
	}

	public function index()
	{
		$reservations = DB::table('reservations')
			->orderBy('submited', 'DESC')
			->get();

		$dataset = [];
		foreach ($reservations as $reservation) {
			array_push($dataset, $this->getData($reservation));
		}

		return response()->json([
			'errcode' => 0,
			'data' => $dataset
		]);
	}

	public function searchByStuno($stuno)
	{
		$reservation = DB::table('reservations')
			->where('stuno', $stuno)
			->first();

		if ($reservation == null) {
			return response()->json([
				'errcode' => 5,
				'errmsg' => '未找到对应的订单信息'
			]);
		}

		return response()->json([
			'errcode' => 0,
			'data' => $this->getData($reservation)
		]);
	}
}
