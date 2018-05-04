<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSessionController extends Controller
{
	public function login(Request $req)
	{
		// Default username and password are both 'test'
		// Please remember to change them when you deploy online
		// Hash generate: password_hash($password, PASSWORD_DEFAULT);

		$username = 'test';
		$hash = '$2y$10$/lRYaYQFCD6rkZyEN8YJ4OnALIYPh7gqNFL2zCFFrHt8pTGItWBQy';

		if (!(($req->username == $username)
			&& password_verify($req->password, $hash)))
			return response()->json([
				'errcode' => 8,
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
}
