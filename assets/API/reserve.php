<?php
header('Content-Type: application/json');
require_once('./config.php');


if (!isset($_POST['count'])) {
	header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	return;
}

if ($_POST['count'] <= 0 OR empty($_POST['studentName']) OR empty($_POST['studentNo']) OR empty($_POST['dormitory']) OR empty($_POST['contact']) OR empty($_POST['date']) OR empty($_POST['timePeriod']) OR !isset($_POST['list0']) OR !isset($_POST['list1']) OR !isset($_POST['list2'])) {
	$response = array('code' => 1);
	echo json_encode($response);
	return;
}

$dup = 0;
if ($_POST['count'] == 2 && $_POST['list0'] == $_POST['list1']) {
	$dup = 1;
}
if ($_POST['count'] == 3 && (($_POST['list0'] == $_POST['list1']) OR ($_POST['list1'] == $_POST['list2']) OR ($_POST['list0'] == $_POST['list2']))) {
	$dup = 1;
}

if ($_POST['count'] > 3 OR $dup == 1) {
	$response = array('code' => 3);
	echo json_encode($response);
	return;
}



$sql = 'SELECT * from students WHERE studentNo = ?';
$stmt = $connect->prepare($sql);
$stmt->execute(array($_POST['studentNo']));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($result)) {
	$response = array('code' => 2);
	echo json_encode($response);
	return;
}


$sql = 'SELECT * FROM books WHERE (bookID = ? AND remainingAmount <= 0) OR (bookID = ? AND remainingAmount <= 0) OR (bookID = ? AND remainingAmount <= 0)';
$stmt = $connect->prepare($sql);
$stmt->execute(array($_POST['list0'], $_POST['list1'], $_POST['list2']));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($result)) {
	$response = array('code' => 5);
	echo json_encode($response);

	date_default_timezone_set('Asia/Shanghai');
	$time=date('Y.m.d H:i:s');
	$logFile = fopen("../collision.log", "a");
	$logText = "[" . $time . "]\tstudentNo:" . $_POST['studentNo'] . "\tlist0:" . $_POST['list0'] . "\tlist1:" . $_POST['list1'] . "\tlist2:" . $_POST['list2'] . "\n\n";
	fwrite($logFile, $logText);
	fclose($logFile);

	return;
}



$sql = 'INSERT INTO `students`(`studentName`, `studentNo`, `dormitory`, `contact`)  VALUES (?,?,?,?)';
$stmt = $connect->prepare($sql);
$stmt->execute(array($_POST['studentName'], $_POST['studentNo'], $_POST['dormitory'], $_POST['contact']));
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result)) {
	$sql = 'SELECT studentID from students WHERE studentNo = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['studentNo']));
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stuID = $result[0]['studentID'];

	$sql = 'INSERT INTO `reservations`(`date`, `timePeriod`, `studentID`, `bookID_1`, `bookID_2`, `bookID_3`) VALUES (?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['date'], $_POST['timePeriod'], $stuID, $_POST['list0'], $_POST['list1'], $_POST['list2']));
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (empty($result)) {
		$sql = 'UPDATE books SET remainingAmount = remainingAmount - 1 WHERE (bookID = ?) OR (bookID = ?) OR (bookID = ?)';
		$stmt = $connect->prepare($sql);
		$stmt->execute(array($_POST['list0'], $_POST['list1'], $_POST['list2']));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result)) {
			$response = array('code' => 0);
			echo json_encode($response);
			return;
		}
	}
}

$response = array('code' => 4);
echo json_encode($response);
