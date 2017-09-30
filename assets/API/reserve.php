<?php
header('Content-Type: application/json');
require_once('./config.php');

existCheck('operation');

switch ($_POST['operation']) {
	case 'new':
		existCheck('list0', 'list1', 'list2');
		blankCheck('studentName', 'studentNo', 'dormitory', 'contact', 'date', 'timePeriod');

		$arr = array_filter(
			[$_POST['list0'], $_POST['list1'], $_POST['list2']],
			function($val) {
				return $val != '0';
			}
		);
		if ($arr != array_unique($arr)) response(1, '错误请求');

		$sql = 'SELECT * FROM reservations WHERE studentNo = ?';
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
			$file = fopen('../collision.log', 'a');
			$log =
				'[' . date('Y.m.d H:i:s') . '] ' .
				$_POST['studentNo'] . ' ' . $_POST['studentName'] . ' ' .
				$_POST['list0'] . ' ' . $_POST['list1'] . ' ' . $_POST['list2'];
			fwrite($file, $log . PHP_EOL);
			fclose($file);
			response(3, '列表中有书籍已被他人预约，请重新选择<br><br>预约信息不需要重新填写O(∩_∩)O');
		}

		$sql = '
		INSERT INTO `reservations`
			(`date`, `timePeriod`, `studentName`, `studentNo`, `dormitory`, `contact`, `bookID_1`, `bookID_2`, `bookID_3`)
		VALUES
			(?, ?, ?, ?, ?, ?, NULLIF(?, 0), NULLIF(?, 0), NULLIF(?, 0))';
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
		response(4, '订单提交失败，请联系管理员或重试');
		break;
	
	case 'modify':
		existCheck('list0', 'list1', 'list2', 'preList0', 'preList1', 'preList2');
		blankCheck('studentName', 'studentNo', 'dormitory', 'contact', 'date', 'timePeriod');

		$arr = array_filter(
			[$_POST['list0'], $_POST['list1'], $_POST['list2']],
			function($val) {
				return $val != '0';
			}
		);
		if ($arr != array_unique($arr)) response(5, '错误请求');

		$arr = array_filter(
			[$_POST['preList0'], $_POST['preList1'], $_POST['preList2']],
			function($val) {
				return $val != '0';
			}
		);
		if ($arr != array_unique($arr)) response(6, '错误请求');

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
			$file = fopen('../collision.log', 'a');
			$log =
				'[' . date('Y.m.d H:i:s') . '] ' .
				$_POST['studentNo'] . ' ' . $_POST['studentName'] . ' ' .
				$_POST['list0'] . ' ' . $_POST['list1'] . ' ' . $_POST['list2'];
			fwrite($file, $log . PHP_EOL);
			fclose($file);
			response(7, '列表中有书籍已被他人预约，请重新选择<br><br>预约信息不需要重新填写O(∩_∩)O');
		}

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
			studentNo = ?';
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
			$_POST['studentNo']
		]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (empty($result)) {
			$sql = '
			UPDATE books
			SET remainingAmount = remainingAmount + 1
			WHERE
				(bookID = ?)
				OR (bookID = ?)
				OR (bookID = ?)';
			$stmt = $connect->prepare($sql);
			$stmt->execute([
				$_POST['preList0'],
				$_POST['preList1'],
				$_POST['preList2']
			]);
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$sql = '
			UPDATE books
			SET remainingAmount = remainingAmount - 1
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
		response(8, '订单修改失败，请联系管理员或重试');
		break;
}
