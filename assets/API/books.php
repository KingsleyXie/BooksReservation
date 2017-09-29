<?php
header('Content-Type: application/json');
require_once('./config.php');

existCheck('type');

// type value - 0: all, 1: search, 2: category
$loadImg = isset($_POST['loadImg']) ? true : false;

$defaultCover = "./assets/pictures/defaultCover.png";

switch ($_POST['type']) {
	case '0':
		$sql = '
		SELECT * FROM books
		ORDER BY
			remainingAmount > 0 DESC,
			bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute();
		break;
	
	case '1':
		existCheck('keyWord');

		$sql = '
		SELECT * FROM books
		WHERE (title LIKE ?) OR (author LIKE ?)
		ORDER BY
			remainingAmount > 0 DESC,
			bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			'%' . $_POST['keyWord'] . '%',
			'%' . $_POST['keyWord'] . '%'
		]);
		break;

	case '2':
		existCheck('bookCategory', 'grade', 'major');

		if ($_POST['bookCategory'] == 'CategoryA') {
			$sql = '
			SELECT * FROM books
			WHERE
				(bookCategory = "CategoryA")
				AND ((grade = ?) OR ("all" = ?))
				AND ((major = ?) OR ("all" = ?))
			ORDER BY
				remainingAmount > 0 DESC,
				bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute([
				$_POST['grade'],
				$_POST['grade'],
				$_POST['major'],
				$_POST['major']
			]);
		}

		if ($_POST['bookCategory'] == 'CategoryB') {
			$sql = '
			SELECT * FROM books
			WHERE
				(bookCategory = "CategoryB")
				AND ((extracurricularCategory = ?) OR ("all" = ?))
			ORDER BY
				remainingAmount > 0 DESC,
				bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute([
				$_POST['extracurricularCategory'],
				$_POST['extracurricularCategory']
			]);
		}
		break;

	default:
		response(2, '请求错误');
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response[0] = empty($result) ? ['code' => 1] : ['code' => 0];

$index = 1;
foreach($result as $book) {
	$response[$index] = [
		'bookID' => htmlspecialchars($book['bookID']),
		'title' => htmlspecialchars($book['title']),
		'author' => htmlspecialchars($book['author']),
		'isMultipleAuthor' => htmlspecialchars($book['isMultipleAuthor']),
		'press' => htmlspecialchars($book['press']),
		'pubdate' => htmlspecialchars($book['pubdate']),
		'image' => ($loadImg ? htmlspecialchars($book['image']) : $defaultCover),
		'remainingAmount' => htmlspecialchars($book['remainingAmount'])
	];
	$index++;
}

echo json_encode($response);
