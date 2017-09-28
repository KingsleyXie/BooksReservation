<?php
/* --- Configuration Part Start --- */

//Database Configurations:
$addr = 'localhost';	//Database Address
$dbname = 'books_reservation';	//Database Name
$user = 'books_reservation_DB_username_here';	//Username for Project Database
$password = 'corresponding_password_here';	//Password for Project Database

/* --- Configuration Part End --- */





//Database Connection based on PDO:
try {
	$connect = new PDO("mysql:host=$addr;dbname=$dbname;charset=utf8", $user, $password);
} catch(PDOException $ex) {
    response(2333, '数据库连接出错，请联系管理员');
}

//Return Code Process Function:
function response($code, $errMsg='success') {
	echo json_encode(['code' => $code, 'errMsg' => $errMsg]);
	exit(0);
}

//Check whether required paraments exist or not
function existCheck() {
	for($i = 0; $i < func_num_args(); $i++) {
		if (!isset($_POST[func_get_arg($i)]))
			header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	}
}

//Check if necessary paraments are blank
function blankCheck() {
	for($i = 0; $i < func_num_args(); $i++) {
		if (($_POST[func_get_arg($i)] == '') OR ($_POST[func_get_arg($i)] === 0))
			response(233, '必填项中含有空值');
	}
}
