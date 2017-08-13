<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_POST['type'])) {
	header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	return;
}

// type value - 1: login, 2: logout

switch ($_POST['type']) {
	case 1:
		if (!(isset($_POST['username']) AND isset($_POST['password']))) response(2, '请输入用户名和密码！');
		//Default username and password(Hash Method: SHA256) are both 'test', please remember to change it when you  deploy it online
		if ($_POST['username'] != 'test' OR hash('sha256', $_POST['password']) != '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08') {
		    response(1, '用户名或密码错误！');
		}
		$_SESSION['username'] = 'admin';
		response(0);
		break;
	
	case 2:
		unset ($_SESSION['username']);
		response(0);
		break;
	
	default:
		response(3, '请求有误');
		break;
}
