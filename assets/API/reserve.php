<?php
header('Content-Type: application/json');
require_once('./config.php');

existCheck('count', 'list0', 'list1', 'list2');
blankCheck('studentName', 'studentNo', 'dormitory', 'contact', 'date', 'timePeriod');

$arr = array_filter(
	[$_POST['list0'], $_POST['list1'], $_POST['list2']],
	function($val) {
		return $val != '0';
	}
);
if ($arr != array_unique($arr)) response(3, '错误请求');

$sql = 'SELECT * FROM students WHERE studentNo = ?';
$stmt = $connect->prepare($sql);
$stmt->execute([$_POST['studentNo']]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($result)) response(2, '该学号已存在预约订单！');

$sql = '
SELECT * FROM books
WHERE
	(bookID = ? AND remainingAmount <= 0)
	OR (bookID = ? AND remainingAmount <= 0)
	OR (bookID = ? AND remainingAmount <= 0)';
$stmt = $connect->prepare($sql);
$stmt->execute([
	$_POST['list0'],
	$_POST['list1'],
	$_POST['list2']
]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($result)) {
	date_default_timezone_set('Asia/Shanghai');
	$time=date('Y.m.d H:i:s');
	$logFile = fopen('../collision.log', 'a');
	$logText =
		"[" . $time . "]\tstudentNo:" .
		$_POST['studentNo'] .
		"\tlist0:" . $_POST['list0'] .
		"\tlist1:" . $_POST['list1'] .
		"\tlist2:" . $_POST['list2'] .
		"\n\n";
	fwrite($logFile, $logText);
	fclose($logFile);

	response(5, '列表中有书籍已被他人预约，请重新选择<br><br>预约信息不需要重新填写O(∩_∩)O');
}

$sql = '
INSERT INTO `students`
	(`studentName`, `studentNo`, `dormitory`, `contact`)
VALUES
	(?,?,?,?)';
$stmt = $connect->prepare($sql);
$stmt->execute([
	$_POST['studentName'],
	$_POST['studentNo'],
	$_POST['dormitory'],
	$_POST['contact']
]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result)) {
	$sql = 'SELECT studentID FROM students WHERE studentNo = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute([$_POST['studentNo']]);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stuID = $result[0]['studentID'];

	$sql = '
	INSERT INTO `reservations`
		(`date`, `timePeriod`, `studentID`, `bookID_1`, `bookID_2`, `bookID_3`)
	VALUES
		(?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$_POST['date'],
		$_POST['timePeriod'],
		$stuID,
		$_POST['list0'],
		$_POST['list1'],
		$_POST['list2']
	]);
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (empty($result)) {
		$sql = '
		UPDATE books
		SET
			remainingAmount = remainingAmount - 1
		WHERE
			(bookID = ?)
			OR (bookID = ?)
			OR (bookID = ?)';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			$_POST['list0'],
			$_POST['list1'],
			$_POST['list2']
		]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result)) response(0);
	}
}

response(4, '订单提交失败，请联系管理员或重试');
