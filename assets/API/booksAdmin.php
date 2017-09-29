<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_SESSION['username'])) {
	$response[0] = array('code' => 1);
	echo json_encode($response);
	return;
}

if (isset($_POST['bookID'])) {
	$sql = 'SELECT * from books WHERE bookID = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['bookID']));
} else {
	$sql = 'SELECT * from books ORDER BY bookID DESC';
	$stmt = $connect->prepare($sql);
	$stmt->execute();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response[0] = empty($result) ? ['code' => 2] : ['code' => 0];

$index = 1;
foreach($result as $book) {
	$response[$index] = [
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
	];
	$index++;
}
echo json_encode($response);
