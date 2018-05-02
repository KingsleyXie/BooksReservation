<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSessionController extends Controller
{
	public function login(Request $req)
	{
		//Default username and password(Hash Method: SHA256) are both 'test', please remember to change it when you  deploy it online
		$hash =
		'9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08';

		if ($req->username != 'test'
			|| hash('sha256', $req->password) != $hash)
			return response()->json([
				'errcode' => 1,
				'errmsg' => '用户名或密码错误！'
			]);

		$req->session()->put('admin', true);

		return response()->json([
			'errcode' => 0
		]);
	}

	public function logout(Request $req)
	{
		$req->session()->forget('admin');
		$req->session()->flush();

		return response()->json([
			'errcode' => 0
		]);
	}

	public function status(Request $req)
	{
		$status = $req->session()->exists('admin');
		return response()->json([
			'errcode' => 0,
			"data" => $status
		]);
	}
}
