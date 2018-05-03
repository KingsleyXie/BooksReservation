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

		$record = [
			'stuname' => $req->stuname,
			'stuno' => $req->stuno,
			'dorm' => $req->dorm,
			'contact' => $req->contact,
			'takeday' => $req->takeday,
			'taketime' => $req->taketime
		];

		if ($req->book0 != 0) $record['book0'] = $req->book0;
		if ($req->book1 != 0) $record['book1'] = $req->book1;
		if ($req->book2 != 0) $record['book2'] = $req->book2;

		DB::table('reservation')->insert($record);

		DB::table('book')
			->whereIn('id', [
				$req->book0,
				$req->book1,
				$req->book2
			])
			->where('quantity', '>', 0)
			->decrement('quantity');

		return response()->json([
			'errcode' => 0
		]);
	}

	public function modify(Request $req)
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

		$record = [
			'stuname' => $req->stuname,
			'dorm' => $req->dorm,
			'contact' => $req->contact,
			'takeday' => $req->takeday,
			'taketime' => $req->taketime
		];

		if ($req->book0 != 0) $record['book0'] = $req->book0;
		if ($req->book1 != 0) $record['book1'] = $req->book1;
		if ($req->book2 != 0) $record['book2'] = $req->book2;

		DB::table('reservation')
			->where('stuno', $req->stuno)
			->update($record);

		DB::table('book')
			->whereIn('id', [
				$req->prebook0,
				$req->prebook1,
				$req->prebook2
			])
			->increment('quantity');

		DB::table('book')
			->whereIn('id', [
				$req->book0,
				$req->book1,
				$req->book2
			])
			->where('quantity', '>', 0)
			->decrement('quantity');

		return response()->json([
			'errcode' => 0
		]);
	}
}
