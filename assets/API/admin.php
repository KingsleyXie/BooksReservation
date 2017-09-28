<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

existCheck('type');

// type value - 1: login, 2: logout
switch ($_POST['type']) {
	case 1:
		existCheck('username', 'password');
		//Default username and password(Hash Method: SHA256) are both 'test', please remember to change it when you  deploy it online
		if ($_POST['username'] != 'test' OR hash('sha256', $_POST['password']) !=
			'9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08')
		    response(1, '用户名或密码错误！');
		$_SESSION['username'] = 'admin';
		response(0);
	
	case 2:
		unset ($_SESSION['username']);
		response(0);
}

response(2, '请求有误');
