<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminSessionController extends Controller
{
	public function login(Request $req)
	{
		$credentials = $req->only('username', 'password');
		if (!Auth::attempt($credentials)) {
			return response()->json([
				'errcode' => 8,
				'errmsg' => '用户名或密码错误！'
			]);
		}

		return response()->json([
			'errcode' => 0
		]);
	}

	public function logout()
	{
		Auth::logout();

		return response()->json([
			'errcode' => 0
		]);
	}

	public function permissionStatus()
	{
		$user = Auth::user();
		return response()->json([
			'books.view' => $user->hasPermissionTo('books.view'),
			'books.import' => $user->hasPermissionTo('books.import'),
			'books.update' => $user->hasPermissionTo('books.update'),
			'reservations.view' => $user->hasPermissionTo('reservations.view')
		]);
	}
}
