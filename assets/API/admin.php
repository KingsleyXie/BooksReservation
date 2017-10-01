<?php
session_start();
require_once('./config.php');

existCheck('operation');
switch ($_POST['operation']) {
	/* ==========================================================================
	   Module 0. Login
	   ========================================================================== */
	case 'login':
		existCheck('username', 'password');
		//Default username and password(Hash Method: SHA256) are both 'test', please remember to change it when you  deploy it online
		if ($_POST['username'] != 'test' OR hash('sha256', $_POST['password']) !=
			'9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08')
		    response(1, '用户名或密码错误！');
		$_SESSION['username'] = 'admin';
		response(0);
	
	/* ==========================================================================
	   Module 1. Logout
	   ========================================================================== */
	case 'logout':
		unset($_SESSION['username']);
		response(0);

	/* ==========================================================================
	   Module 2. Add Or Update A Book
	   ========================================================================== */
	case 'add':
	case 'update':
		if (!isset($_SESSION['username'])) response(2, '请登录系统！');

		existCheck('image', 'bookCategory');

		$isMA = isset($_POST['isMultipleAuthor']) ? 1 : 0;
		$_POST['image'] = empty($_POST['image']) ?
			'./assets/pictures/defaultCover.png' : $_POST['image'];

		//Shunt The Database Operation
		if ($_POST['bookCategory'] == 'CategoryA') {
			existCheck('title', 'author', 'press', 'pubdate', 'grade', 'major', 'remainingAmount');
			$_POST['operation'] .= 'A';
		}
		if ($_POST['bookCategory'] == 'CategoryB') {
			existCheck('title', 'author', 'press', 'pubdate', 'extracurricularCategory', 'remainingAmount');
			$_POST['operation'] .= 'B';
		}

		switch ($_POST['operation']) {
			case 'addA':
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
				break;
			
			case 'addB':
				$sql = '
				INSERT INTO `books`
					(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `extracurricularCategory`, `remainingAmount`)
				VALUES
					(?,?,?,?,?,?,?,?,?,?)';
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
				break;

			case 'updateA':
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
					$_POST['title'],
					$_POST['author'],
					$isMA,
					$_POST['press'],
					$_POST['pubdate'],
					$_POST['image'],
					$_POST['bookCategory'],
					$_POST['grade'],
					$_POST['major'],
					$_POST['remainingAmount'],
					$_POST['bookID']
				]);
				break;

			case 'updateB':
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
				WHERE `bookID` = ?';
				$stmt = $connect->prepare($sql);
				$stmt->execute([
					$_POST['title'],
					$_POST['author'],
					$isMA,
					$_POST['press'],
					$_POST['pubdate'],
					$_POST['image'],
					$_POST['bookCategory'],
					$_POST['extracurricularCategory'],
					$_POST['remainingAmount'],
					$_POST['bookID']
				]);
				break;

			default:
				response(3, '错误请求');
				break;
		}

		if (empty($stmt->fetchAll(PDO::FETCH_ASSOC)))
			response(0);
		else
			response(4, '操作失败，请联系管理员');

	/* ==========================================================================
	   Module 3. Get Detailed Data On Books
	   ========================================================================== */
	case 'books':
		if (!isset($_SESSION['username'])) response(5, '请登录系统！');

		if (isset($_POST['bookID'])) {
			$sql = 'SELECT * FROM books WHERE bookID = ?';
			$stmt = $connect->prepare($sql);
			$stmt->execute([$_POST['bookID']]);
		} else {
			$sql = 'SELECT * FROM books ORDER BY bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute();
		}
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$response = empty($result) ?
			isset($_POST['bookID']) ?
				response(6, '未找到对应书籍，请检查输入 ID 是否正确！') :
				response(7, '数据库中暂无书籍信息') :
			['code' => 0];

		foreach($result as $book) {
			array_push($response, [
				'bookID' => htmlspecialchars($book['bookID']),
				'title' => htmlspecialchars($book['title']),
				'author' => htmlspecialchars($book['author']),
				'isMultipleAuthor' => htmlspecialchars($book['isMultipleAuthor']),
				'press' => htmlspecialchars($book['press']),
				'pubdate' => htmlspecialchars($book['pubdate']),
				'image' => htmlspecialchars($book['image']),
				'bookCategory' => htmlspecialchars($book['bookCategory']),
				'grade' => htmlspecialchars($book['grade']),
				'major' => htmlspecialchars($book['major']),
				'extracurricularCategory' => htmlspecialchars($book['extracurricularCategory']),
				'remainingAmount' => htmlspecialchars($book['remainingAmount']),
				'importTime' => htmlspecialchars($book['importTime']),
				'updateTime' => htmlspecialchars($book['updateTime'])
			]);
		}
		echo json_encode($response);
		return;
}
