<?php
require_once('./config.php');

existCheck('operation', 'list0', 'list1', 'list2', 'studentName', 'studentNo', 'dormitory', 'contact', 'date', 'timePeriod');
blankCheck('studentName', 'studentNo', 'dormitory', 'contact', 'date', 'timePeriod');

/* ==========================================================================
   0. Check If There Are Duplicate Books In The Reservation List
   ========================================================================== */
$newList = array_filter([$_POST['list0'], $_POST['list1'], $_POST['list2']],
	function($val) { return $val != 0;}
);
$newList = empty($newList) ? response(1, '列表内未包含有效书籍') : $newList;
if ($newList != array_unique($newList))
	response(2, '列表中存在重复书籍');

$preList = [];
if ($_POST['operation'] == 'modify') {
	existCheck('preList0', 'preList1', 'preList2');
	$preList = array_filter([$_POST['preList0'], $_POST['preList1'], $_POST['preList2']],
		function($val) { return $val != 0; }
	);
	$preList = empty($preList) ? response(3, '列表内未包含有效书籍') : $preList;
	if ($preList != array_unique($preList))
		response(4, '错误请求');
}
$arr = array_pad(array_diff($newList, $preList), 3, 0);

/* ==========================================================================
   1. Check If Collision Happens And Record A Log If It Does
   ========================================================================== */
$sql = '
SELECT * FROM books
WHERE
	(bookID = ? AND remainingAmount <= 0)
	OR (bookID = ? AND remainingAmount <= 0)
	OR (bookID = ? AND remainingAmount <= 0)';
$stmt = $connect->prepare($sql);
$stmt->execute($arr);

if (!empty($stmt->fetchAll(PDO::FETCH_ASSOC))) {
	date_default_timezone_set('Asia/Shanghai');
	$file = fopen('../collision.log', 'a');
	$log =
		'[' . date('Y.m.d H:i:s') . '] ' .
		$_POST['studentNo'] . ' ' . $_POST['studentName'] . ' ' .
		$_POST['list0'] . ' ' . $_POST['list1'] . ' ' . $_POST['list2'];
	fwrite($file, $log . PHP_EOL);
	fclose($file);
	response(5, '列表中存在余量为0的书籍');
}

/* ==========================================================================
   2. Add Or Update A Reservation
   ========================================================================== */
switch ($_POST['operation']) {
	case 'new':
		$sql = 'SELECT * FROM reservations WHERE studentNo = ?';
		$stmt = $connect->prepare($sql);
		$stmt->execute([$_POST['studentNo']]);
		if (!empty($stmt->fetchAll(PDO::FETCH_ASSOC))) response(6, '该学号已存在预约订单！');

		$sql = '
		INSERT INTO `reservations`
			(`date`, `timePeriod`, `studentName`, `studentNo`, `dormitory`, `contact`, `bookID_1`, `bookID_2`, `bookID_3`)
		VALUES
			(?, ?, ?, ?, ?, ?, NULLIF(?, 0), NULLIF(?, 0), NULLIF(?, 0));
		UPDATE books
		SET
			remainingAmount = remainingAmount - 1
		WHERE
			(bookID = ?)
			OR (bookID = ?)
			OR (bookID = ?)';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			$_POST['date'],
			$_POST['timePeriod'],
			$_POST['studentName'],
			$_POST['studentNo'],
			$_POST['dormitory'],
			$_POST['contact'],
			$_POST['list0'],
			$_POST['list1'],
			$_POST['list2'],
			$_POST['list0'],
			$_POST['list1'],
			$_POST['list2']
		]);
		if (empty($stmt->fetchAll(PDO::FETCH_ASSOC))) response(0);
		response(7, '订单提交失败，请联系管理员或重试');
		break;
	
	case 'modify':
		$sql = 'SELECT * FROM reservations WHERE studentNo = ?';
		$stmt = $connect->prepare($sql);
		$stmt->execute([$_POST['studentNo']]);
		if (empty($stmt->fetchAll(PDO::FETCH_ASSOC))) response(8, '未找到该学号对应订单信息！');

		$sql = '
		UPDATE `reservations`
		SET
			`date` = ?,
			`timePeriod` = ?,
			`studentName` = ?,
			`studentNo` = ?,
			`dormitory` = ?,
			`contact` = ?,
			`bookID_1` = NULLIF(?, 0),
			`bookID_2` = NULLIF(?, 0),
			`bookID_3` = NULLIF(?, 0)
		WHERE
			studentNo = ?;
		UPDATE books
		SET remainingAmount = remainingAmount + 1
		WHERE
			(bookID = ?)
			OR (bookID = ?)
			OR (bookID = ?);
		UPDATE books
		SET remainingAmount = remainingAmount - 1
		WHERE
			(bookID = ?)
			OR (bookID = ?)
			OR (bookID = ?)';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			$_POST['date'],
			$_POST['timePeriod'],
			$_POST['studentName'],
			$_POST['studentNo'],
			$_POST['dormitory'],
			$_POST['contact'],
			$_POST['list0'],
			$_POST['list1'],
			$_POST['list2'],
			$_POST['studentNo'],
			$_POST['preList0'],
			$_POST['preList1'],
			$_POST['preList2'],
			$_POST['list0'],
			$_POST['list1'],
			$_POST['list2']
		]);
		if (empty($stmt->fetchAll(PDO::FETCH_ASSOC))) response(0);
		response(9, '订单修改失败，请联系管理员或重试');
		break;
}
