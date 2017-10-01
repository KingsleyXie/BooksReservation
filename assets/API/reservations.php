<?php
session_start();
require_once('./config.php');

existCheck('operation');
switch ($_POST['operation']) {
	//Search With Posted Student Number
	case 'search':
		existCheck('stuNo');
		$sql = 'SELECT * FROM reservations WHERE studentNo = ?';
		$stmt = $connect->prepare($sql);
		$stmt->execute([$_POST['stuNo']]);
		$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($reservations)) response(1, '未查询到相关订单');
		break;

	//Get All Reservations' Information
	case 'all':
		if (!isset($_SESSION['username'])) response(2, '请登录系统！');

		$sql = 'SELECT * FROM reservations ORDER BY submitTime DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute();
		$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($reservations)) response(3, '暂无订单数据');
		break;

	default:
		response(4, '错误请求');
}

$response = ['code' => 0];
foreach ($reservations as $reservation) {
	$sql = '
	SELECT * FROM books
	WHERE (bookID = ?)
		OR (bookID = ?)
		OR (bookID = ?)
	';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$reservation['bookID_1'],
		$reservation['bookID_2'],
		$reservation['bookID_3']
	]);
	$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

	array_push($response, [
		'reservationNo' => htmlspecialchars($reservation['reservationNo']),
		'stuName' => htmlspecialchars($reservation['studentName']),
		'stuNo' => htmlspecialchars($reservation['studentNo']),
		'contact' => htmlspecialchars($reservation['contact']),
		'dormitory' => htmlspecialchars($reservation['dormitory']),
		'date' => htmlspecialchars($reservation['date']),
		'timePeriod' => htmlspecialchars($reservation['timePeriod']),
		'sbmTime' => htmlspecialchars($reservation['submitTime']),
		'updTime' => htmlspecialchars($reservation['updateTime']),
		'books' => []
	]);

	foreach($books as $book) {
		array_push($response[count($response) - 2]['books'], [
			'bookID' => htmlspecialchars($book['bookID']),
			'title' => htmlspecialchars($book['title']),
			'author' => htmlspecialchars($book['author']),
			'isMultipleAuthor' => htmlspecialchars($book['isMultipleAuthor']),
			'press' => htmlspecialchars($book['press']),
			'pubdate' => htmlspecialchars($book['pubdate']),
			'image' => htmlspecialchars($book['image'])
		]);
	}
}
echo json_encode($response);
