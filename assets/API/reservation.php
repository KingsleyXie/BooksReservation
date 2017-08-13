<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_POST['type'])) {
	header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	return;
}

// type value - 0: all, 1: search



if ($_POST['type'] == "0") {
	
	if (!isset($_SESSION['username'])) {
		$response[0] = array('code' => 2);
		echo json_encode($response);
		return;
	}

	$sql = 'SELECT * from students ORDER BY importTime DESC';
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
		$sql = 'SELECT * from reservations where studentID = ?';
		$stmt = $connect->prepare($sql);
		$stmt->execute(array($stu['studentID']));
		$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$sql = 'SELECT * from books where (bookID = ?) OR (bookID = ?) OR (bookID = ?)';
		$stmt = $connect->prepare($sql);
		$stmt->execute(array($reservation[0]['bookID_1'], $reservation[0]['bookID_2'], $reservation[0]['bookID_3']));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$bookIndex = 0;
		foreach($result as $book) {
			$books[$bookIndex] = array('bookID' => $book['bookID'], 'title' => $book['title'], 'author' => $book['author'], 'isMultipleAuthor' => $book['isMultipleAuthor'], 'press' => $book['press'], 'pubdate' => $book['pubdate'], 'image' => $book['image']);
			$bookIndex++;
		}

		$response[$index] = array('reservationNo' => $reservation[0]['reservationNo'], 'stuName' => $stu['studentName'], 'stuNo' => $stu['studentNo'], 'contact' => $stu['contact'], 'dormitory' => $stu['dormitory'], 'date' => $reservation[0]['date'], 'timePeriod' => $reservation[0]['timePeriod'], 'sbmTime' => $reservation[0]['submitTime'], 'updTime' => $reservation[0]['updateTime'], 'books' => $books);
		unset($books);
		$index ++;
	}
	echo json_encode($response);
	return;
}



$sql = 'SELECT * from students where studentNo = ?';
$stmt = $connect->prepare($sql);
$stmt->execute(array($_POST['stuNo']));
$stu = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($stu)) {
	$response[0] = array('code' => 1);
	echo json_encode($response);
	return;
}


$sql = 'SELECT * from reservations where studentID = ?';
$stmt = $connect->prepare($sql);
$stmt->execute(array($stu[0]['studentID']));
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT * from books where (bookID = ?) OR (bookID = ?) OR (bookID = ?)';
$stmt = $connect->prepare($sql);
$stmt->execute(array($reservation[0]['bookID_1'], $reservation[0]['bookID_2'], $reservation[0]['bookID_3']));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$bookIndex = 0;
foreach($result as $book) {
	$books[$bookIndex] = array('bookID' => $book['bookID'], 'title' => $book['title'], 'author' => $book['author'], 'isMultipleAuthor' => $book['isMultipleAuthor'], 'press' => $book['press'], 'pubdate' => $book['pubdate'], 'image' => $book['image']);
	$bookIndex++;
}

$response[0] = array('code' => 0);
$response[1] = array('reservationNo' => $reservation[0]['reservationNo'], 'stuName' => $stu[0]['studentName'], 'stuNo' => $stu[0]['studentNo'], 'contact' => $stu[0]['contact'], 'dormitory' => $stu[0]['dormitory'], 'date' => $reservation[0]['date'], 'timePeriod' => $reservation[0]['timePeriod'], 'sbmTime' => $reservation[0]['submitTime'], 'updTime' => $reservation[0]['updateTime'], 'books' => $books);

unset($books);

echo json_encode($response);
