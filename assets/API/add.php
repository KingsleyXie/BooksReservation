<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_SESSION['username']))
	response(1, '请登录系统！');

existCheck('image', 'bookCategory');

$isMA = isset($_POST['isMultipleAuthor']) ? 1 : 0;
$_POST['image'] = empty($_POST['image']) ? './assets/pictures/defaultCover.png' : $_POST['image'];

if ($_POST['bookCategory'] == 'CategoryA') {
	existCheck('title', 'author', 'press', 'pubdate', 'grade', 'major', 'remainingAmount');

	$sql = '
	INSERT INTO `books`
	(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `grade`, `major`, `remainingAmount`)
	VALUES
	(?,?,?,?,?,?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$_POST['ISBN'],
		$_POST['title'],
		$_POST['author'],
		$isMA,
		$_POST['press'],
		$_POST['pubdate'],
		$_POST['image'],
		$_POST['bookCategory'],
		$_POST['grade'],
		$_POST['major'],
		$_POST['remainingAmount']
	]);
}

if ($_POST['bookCategory'] == 'CategoryB') {
	existCheck('title', 'author', 'press', 'pubdate', 'extracurricularCategory', 'remainingAmount');
	
	$sql = '
	INSERT INTO `books`
	(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `extracurricularCategory`, `remainingAmount`)
	VALUES (?,?,?,?,?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute([
		$_POST['ISBN'],
		$_POST['title'],
		$_POST['author'],
		$isMA,
		$_POST['press'],
		$_POST['pubdate'],
		$_POST['image'],
		$_POST['bookCategory'],
		$_POST['extracurricularCategory'],
		$_POST['remainingAmount']
	]);
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result))
	response(0);
else
	response(2, '添加书籍失败，请联系管理员');
