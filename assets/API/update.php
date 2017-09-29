<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_SESSION['username']))
	response(1, '请登录系统！');

existCheck('updImage', 'updBookCategory');

$isMA = isset($_POST['updIsMultipleAuthor']) ? 1 : 0;
$_POST['updImage'] = empty($_POST['updImage']) ? './assets/pictures/defaultCover.png' : $_POST['updImage'];

if ($_POST['updBookCategory'] == 'CategoryA') {
	existCheck('updTitle', 'updAuthor', 'updPress', 'updPubdate', 'updGrade', 'updMajor', 'updRemainingAmount');

	$sql = '
	UPDATE `books`
	SET
	`title` = ?,
	`author` = ?,
	`isMultipleAuthor` = ?,
	`press` = ?,
	`pubdate` = ?,
	`image` = ?,
	`bookCategory` = ?,
	`grade` = ?,
	`major` = ?,
	`remainingAmount` = ?
	WHERE `bookID` = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$_POST['updTitle'],
		$_POST['updAuthor'],
		$isMA,
		$_POST['updPress'],
		$_POST['updPubdate'],
		$_POST['updImage'],
		$_POST['updBookCategory'],
		$_POST['updGrade'],
		$_POST['updMajor'],
		$_POST['updRemainingAmount'],
		$_POST['bookID']
	]);
}

if ($_POST['updBookCategory'] == 'CategoryB') {
	existCheck('updTitle', 'updAuthor', 'updPress', 'updPubdate', 'updExtracurricularCategory', 'updRemainingAmount');

	$sql = '
	UPDATE `books`
	SET
	`title` = ?,
	`author` = ?,
	`isMultipleAuthor` = ?,
	`press` = ?,
	`pubdate` = ?,
	`image` = ?,
	`bookCategory` = ?,
	`extracurricularCategory` = ?,
	`remainingAmount` = ?
	WHERE  `bookID` = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$_POST['updTitle'],
		$_POST['updAuthor'],
		$isMA,
		$_POST['updPress'],
		$_POST['updPubdate'],
		$_POST['updImage'],
		$_POST['updBookCategory'],
		$_POST['updExtracurricularCategory'],
		$_POST['updRemainingAmount'],
		$_POST['bookID']
	]);
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result))
	response(0);
else
	response(2, '更新书籍信息失败，请联系管理员');
