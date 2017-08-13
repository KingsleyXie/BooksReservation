<?php
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_POST['type'])) {
	header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	return;
}

// type value - 0: all, 1: search, 2: category

$loadImg = false;
if (isset($_POST['loadImg'])) $loadImg = true;

$defaultCover = "./assets/pictures/defaultCover.png";

switch ($_POST['type']) {
	case 0:
		$sql = 'SELECT * from books ORDER BY remainingAmount > 0 DESC, bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		break;
	
	case 1:
		$sql = 'SELECT * from books WHERE (title LIKE ?) OR (author LIKE ?) ORDER BY remainingAmount > 0 DESC, bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute(array('%' . $_POST['keyWord'] . '%', '%' . $_POST['keyWord'] . '%'));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		break;

	case 2:
		if ($_POST['bookCategory'] == 'CategoryA') {
			$sql = 'SELECT * from books WHERE (bookCategory = \'CategoryA\') AND ((grade = ?) OR (\'all\' = ?)) AND ((major = ?) OR (\'all\' = ?)) ORDER BY remainingAmount > 0 DESC, bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute(array($_POST['grade'], $_POST['grade'], $_POST['major'], $_POST['major']));
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		if ($_POST['bookCategory'] == 'CategoryB') {
			$sql = 'SELECT * from books WHERE (bookCategory = \'CategoryB\') AND ((extracurricularCategory = ?) OR (\'all\' = ?)) ORDER BY remainingAmount > 0 DESC, bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute(array($_POST['extracurricularCategory'], $_POST['extracurricularCategory']));
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		break;

	default:
		response(3, '请求错误');
		break;
}

if (empty($result)) {
	$response[0] = array('code' => 1);
}
else {
	$response[0] = array('code' => 0);
}


$index = 1;
foreach($result as $books) {
	$response[$index] = array('bookID' => htmlspecialchars($books['bookID']), 'title' => htmlspecialchars($books['title']), 'author' => htmlspecialchars($books['author']), 'isMultipleAuthor' => htmlspecialchars($books['isMultipleAuthor']), 'press' => htmlspecialchars($books['press']), 'pubdate' => htmlspecialchars($books['pubdate']), 'image' => ($loadImg ? htmlspecialchars($books['image']) : $defaultCover), 'remainingAmount' => htmlspecialchars($books['remainingAmount']));
	$index++;
}


echo json_encode($response);
