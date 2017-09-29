<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

existCheck('operation');

// Return All Reservations' Information
if ($_POST['operation'] == 'all') {
	if (!isset($_SESSION['username'])) {
		$response[0] = array('code' => 2);
		echo json_encode($response);
		return;
	}

	$sql = 'SELECT * FROM students ORDER BY importTime DESC';
	$stmt = $connect->prepare($sql);
	$stmt->execute();
	$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (empty($students)) {
		$response[0] = array('code' => 1);
		echo json_encode($response);
		return;
	}

	$response[0] = array('code' => 0);
	$index = 1;
	foreach ($students as $stu) {
		$sql = 'SELECT * FROM reservations WHERE studentID = ?';
		$stmt = $connect->prepare($sql);
		$stmt->execute([$stu['studentID']]);
		$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

		$bookIndex = 0;
		foreach($result as $book) {
			$books[$bookIndex] = [
				'bookID' => $book['bookID'],
				'title' => $book['title'],
				'author' => $book['author'],
				'isMultipleAuthor' => $book['isMultipleAuthor'],
				'press' => $book['press'],
				'pubdate' => $book['pubdate'],
				'image' => $book['image']
			];
			$bookIndex++;
		}

		$response[$index] = [
			'reservationNo' => $reservation[0]['reservationNo'],
			'stuName' => $stu['studentName'],
			'stuNo' => $stu['studentNo'],
			'contact' => $stu['contact'],
			'dormitory' => $stu['dormitory'],
			'date' => $reservation[0]['date'],
			'timePeriod' => $reservation[0]['timePeriod'],
			'sbmTime' => $reservation[0]['submitTime'],
			'updTime' => $reservation[0]['updateTime'],
			'books' => $books
		];
		unset($books);
		$index ++;
	}
	echo json_encode($response);
	exit(0);
}

// Search With Posted Student Number
existCheck('stuNo');
$sql = 'SELECT * FROM students WHERE studentNo = ?';
$stmt = $connect->prepare($sql);
$stmt->execute([$_POST['stuNo']]);
$stu = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($stu)) {
	$response[0] = array('code' => 1);
	echo json_encode($response);
	return;
}

$sql = 'SELECT * FROM reservations WHERE studentID = ?';
$stmt = $connect->prepare($sql);
$stmt->execute(array($stu[0]['studentID']));
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

$bookIndex = 0;
foreach($result as $book) {
	$books[$bookIndex] = [
		'bookID' => $book['bookID'],
		'title' => $book['title'],
		'author' => $book['author'],
		'isMultipleAuthor' => $book['isMultipleAuthor'],
		'press' => $book['press'],
		'pubdate' => $book['pubdate'],
		'image' => $book['image']
	];
	$bookIndex++;
}

$response[0] = array('code' => 0);
$response[1] = [
	'reservationNo' => $reservation[0]['reservationNo'],
	'stuName' => $stu[0]['studentName'],
	'stuNo' => $stu[0]['studentNo'],
	'contact' => $stu[0]['contact'],
	'dormitory' => $stu[0]['dormitory'],
	'date' => $reservation[0]['date'],
	'timePeriod' => $reservation[0]['timePeriod'],
	'sbmTime' => $reservation[0]['submitTime'],
	'updTime' => $reservation[0]['updateTime'],
	'books' => $books
];
unset($books);

echo json_encode($response);
