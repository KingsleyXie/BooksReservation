<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_SESSION['username'])) response(1, '请登录系统！');

$isMA = 0;
if (isset($_POST['isMultipleAuthor']))  $isMA = 1;

if (empty($_POST['image'])) {
	$_POST['image'] = "./assets/pictures/defaultCover.png";
}

if ($_POST['bookCategory'] == 'CategoryA') {
	if (!(isset($_POST['title']) AND isset($_POST['author']) AND isset($_POST['press']) AND isset($_POST['pubdate']) AND isset($_POST['grade']) AND isset($_POST['major']) AND isset($_POST['remainingAmount']))) {
		response(2, '请将书籍信息填写完整！');
	}

	$sql = 'INSERT INTO `books`	(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `grade`, `major`, `remainingAmount`) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['ISBN'], $_POST['title'], $_POST['author'], $isMA, $_POST['press'], $_POST['pubdate'], $_POST['image'], $_POST['bookCategory'], $_POST['grade'], $_POST['major'], $_POST['remainingAmount']));
}

if ($_POST['bookCategory'] == 'CategoryB') {
	if (!(isset($_POST['title']) AND isset($_POST['author']) AND isset($_POST['press']) AND isset($_POST['pubdate']) AND isset($_POST['extracurricularCategory']) AND isset($_POST['remainingAmount']))) {
		response(2, '请将书籍信息填写完整！');
	}
	
	$sql = 'INSERT INTO `books`	(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `extracurricularCategory`, `remainingAmount`) VALUES (?,?,?,?,?,?,?,?,?,?)';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['ISBN'], $_POST['title'], $_POST['author'], $isMA, $_POST['press'], $_POST['pubdate'], $_POST['image'], $_POST['bookCategory'], $_POST['extracurricularCategory'], $_POST['remainingAmount']));
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($result)) {
	response(0);
}
else {
	response(3, '添加书籍失败，请联系管理员');
}