<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReserveController extends Controller
{
	public function add(Request $req)
	{
		$exists = DB::table('reservations')
			->where('stuno', $req->stuno)
			->exists();

		if ($exists) {
			return response()->json([
				'errcode' => 6,
				'errmsg' => '该学号已存在预约订单！'
			]);
		}

		$record = [
			'stuname' => $req->stuname,
			'stuno' => $req->stuno,
			'dorm' => $req->dorm,
			'contact' => $req->contact,
			'takeday' => $req->takeday,
			'taketime' => $req->taketime
		];

		$bookIds = [$req->book0, $req->book1, $req->book2];

		DB::transaction(function () use ($bookIds, $record) {
			DB::table('books')
				->whereIn('id', $bookIds)
				->where('quantity', '>', 0)
				->decrement('quantity');

			$reservationId = DB::table('reservations')->insertGetId($record);
			$relation = [
				'reservation_id' => $reservationId,
				'book_id' => 0
			];

			foreach ($bookIds as $bookId) {
				if ($bookId != 0) {
					$relation['book_id'] = $bookId;
					DB::table('reservation_book')->insert($relation);
				}
			}
		});

		return response()->json([
			'errcode' => 0,
			'data' => '订单提交成功！'
		]);
	}

	public function modify(Request $req)
	{
		$exists = DB::table('reservations')
			->where('stuno', $req->stuno)
			->exists();

		if (!$exists) {
			return response()->json([
				'errcode' => 7,
				'errmsg' => '未找到该学号对应的订单信息！'
			]);
		}

		$record = [
			'stuname' => $req->stuname,
			'dorm' => $req->dorm,
			'contact' => $req->contact,
			'takeday' => $req->takeday,
			'taketime' => $req->taketime
		];

		$preBookIds = [$req->prebook0, $req->prebook1, $req->prebook2];
		$bookIds = [$req->book0, $req->book1, $req->book2];

		$reservationId = DB::table('reservations')
			->where('stuno', $req->stuno)
			->value('id');

		DB::transaction(function () use ($preBookIds, $bookIds, $record, $reservationId) {
			DB::table('books')
				->whereIn('id', $preBookIds)
				->increment('quantity');

			DB::table('books')
				->whereIn('id', $bookIds)
				->where('quantity', '>', 0)
				->decrement('quantity');

			DB::table('reservations')
				->where('id', $reservationId)
				->update($record);

			$relation = [
				'reservation_id' => $reservationId,
				'book_id' => 0
			];

			foreach ($preBookIds as $bookId) {
				if ($bookId != 0) {
					$relation['book_id'] = $bookId;
					DB::table('reservation_book')->where($relation)->delete();
				}
			}

			foreach ($bookIds as $bookId) {
				if ($bookId != 0) {
					$relation['book_id'] = $bookId;
					DB::table('reservation_book')->insert($relation);
				}
			}
		});

		return response()->json([
			'errcode' => 0,
			'data' => '订单修改成功！'
		]);
	}
}
