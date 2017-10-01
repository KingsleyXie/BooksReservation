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
		$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($reservation)) response(1, '未查询到相关订单');

		$sql = '
		SELECT * FROM books
		WHERE (bookID = ?)
			OR (bookID = ?)
			OR (bookID = ?)
		';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			$reservation[0]['bookID_1'],
			$reservation[0]['bookID_2'],
			$reservation[0]['bookID_3']
		]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$response = ['code' => 0];
		$response[0] = [
			'reservationNo' => $reservation[0]['reservationNo'],
			'stuName' => $reservation[0]['studentName'],
			'stuNo' => $reservation[0]['studentNo'],
			'contact' => $reservation[0]['contact'],
			'dormitory' => $reservation[0]['dormitory'],
			'date' => $reservation[0]['date'],
			'timePeriod' => $reservation[0]['timePeriod'],
			'sbmTime' => $reservation[0]['submitTime'],
			'updTime' => $reservation[0]['updateTime'],
			'books' => []
		];
		foreach($result as $book) {
			array_push($response[0]['books'], [
				'bookID' => $book['bookID'],
				'title' => $book['title'],
				'author' => $book['author'],
				'isMultipleAuthor' => $book['isMultipleAuthor'],
				'press' => $book['press'],
				'pubdate' => $book['pubdate'],
				'image' => $book['image']
			]);
		}
		echo json_encode($response);
		exit(0);

	//Get All Reservations' Information
	case 'all':
		if (!isset($_SESSION['username'])) {
			response(2, '请登录系统！');
		}

		$sql = 'SELECT * FROM reservations ORDER BY submitTime DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute();
		$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($reservations)) response(3, '暂无订单数据');

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
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			array_push($response, [
				'reservationNo' => $reservation['reservationNo'],
				'stuName' => $reservation['studentName'],
				'stuNo' => $reservation['studentNo'],
				'contact' => $reservation['contact'],
				'dormitory' => $reservation['dormitory'],
				'date' => $reservation['date'],
				'timePeriod' => $reservation['timePeriod'],
				'sbmTime' => $reservation['submitTime'],
				'updTime' => $reservation['updateTime'],
				'books' => []
			]);

			foreach($result as $book) {
				array_push($response[count($response) - 2]['books'], [
					'bookID' => $book['bookID'],
					'title' => $book['title'],
					'author' => $book['author'],
					'isMultipleAuthor' => $book['isMultipleAuthor'],
					'press' => $book['press'],
					'pubdate' => $book['pubdate'],
					'image' => $book['image']
				]);
			}
			unset($books);
		}
		echo json_encode($response);
		exit(0);
}
