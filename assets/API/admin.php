<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

existCheck('operation');

switch ($_POST['operation']) {
	/*Module 1: Login */
	case 'login':
		existCheck('username', 'password');
		//Default username and password(Hash Method: SHA256) are both 'test', please remember to change it when you  deploy it online
		if ($_POST['username'] != 'test' OR hash('sha256', $_POST['password']) !=
			'9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08')
		    response(1, '用户名或密码错误！');
		$_SESSION['username'] = 'admin';
		response(0);
	
	/* Module 2: Logout */
	case 'logout':
		unset($_SESSION['username']);
		response(0);

	/* Module 3: Add A Book To Database */
	case 'add':
		if (!isset($_SESSION['username']))
			response(2, '请登录系统！');

		existCheck('image', 'bookCategory');

		$isMA = isset($_POST['isMultipleAuthor']) ? 1 : 0;
		$_POST['image'] = empty($_POST['image']) ?
			'./assets/pictures/defaultCover.png' : $_POST['image'];

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
		}

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (empty($result))
			response(0);
		else
			response(3, '添加书籍失败，请联系管理员');

	/* Module 4: Update A Book's Information */
	case 'update':
		if (!isset($_SESSION['username']))
			response(4, '请登录系统！');

		existCheck('updImage', 'updBookCategory');

		$isMA = isset($_POST['updIsMultipleAuthor']) ? 1 : 0;
		$_POST['updImage'] = empty($_POST['updImage']) ?
			'./assets/pictures/defaultCover.png' : $_POST['updImage'];

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
			response(5, '更新书籍信息失败，请联系管理员');

	/* Module 5: Get Detailed Books' Information */
	case 'books':
		if (!isset($_SESSION['username'])) {
			response(6, '请登录系统！');
		}

		if (isset($_POST['bookID'])) {
			$sql = 'SELECT * FROM books WHERE bookID = ?';
			$stmt = $connect->prepare($sql);
			$stmt->execute(array($_POST['bookID']));
		} else {
			$sql = 'SELECT * FROM books ORDER BY bookID DESC';
			$stmt = $connect->prepare($sql);
			$stmt->execute();
		}
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$response = empty($result) ?
			isset($_POST['bookID']) ?
				response(7, '未找到对应书籍，请检查输入 ID 是否正确！') :
				response(8, '数据库中暂无书籍信息') :
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
