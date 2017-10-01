<?php
require_once('./config.php');

existCheck('operation');
switch ($_POST['operation']) {
	case 'all':
		$sql = '
		SELECT * FROM books
		ORDER BY
			remainingAmount > 0 DESC,
			bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute();
		$emptyMsg = '数据库还是空的，过段时间再来看看吧';
		break;
	
	case 'search':
		existCheck('keyWord');

		$sql = '
		SELECT * FROM books
		WHERE
			(title LIKE ?)
			OR (author LIKE ?)
		ORDER BY
			remainingAmount > 0 DESC,
			bookID DESC';
		$stmt = $connect->prepare($sql);
		$stmt->execute([
			'%' . $_POST['keyWord'] . '%',
			'%' . $_POST['keyWord'] . '%'
		]);
		$emptyMsg = '未找到相关书籍，换个关键词试试吧';
		break;

	case 'category':
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
		} elseif ($_POST['bookCategory'] == 'CategoryB') {
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
		} else {
			response(1, '错误请求');
		}
		$emptyMsg = '未找到相关书籍，换个分类试试吧';
		break;

	default:
		response(2, '错误请求');
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = empty($result) ? response(3, $emptyMsg) : ['code' => 0];

foreach($result as $book) {
	array_push($response, [
		'bookID' => htmlspecialchars($book['bookID']),
		'title' => htmlspecialchars($book['title']),
		'author' => htmlspecialchars($book['author']),
		'isMultipleAuthor' => htmlspecialchars($book['isMultipleAuthor']),
		'press' => htmlspecialchars($book['press']),
		'pubdate' => htmlspecialchars($book['pubdate']),
		'image' => (isset($_POST['loadImg']) ?
			htmlspecialchars($book['image']) : './assets/pictures/defaultCover.png'),
		'remainingAmount' => htmlspecialchars($book['remainingAmount'])
	]);
}

echo json_encode($response);
