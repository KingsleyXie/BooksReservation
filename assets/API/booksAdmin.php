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
}
else {
	$sql = 'SELECT * from books ORDER BY bookID DESC';
	$stmt = $connect->prepare($sql);
	$stmt->execute();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result)) {
	$response[0] = array('code' => 2);
}
else {
	$response[0] = array('code' => 0);
}

$index = 1;
foreach($result as $books) {
	$response[$index] = array('bookID' => htmlspecialchars($books['bookID']), 'title' => htmlspecialchars($books['title']), 'author' => htmlspecialchars($books['author']), 'isMultipleAuthor' => htmlspecialchars($books['isMultipleAuthor']), 'press' => htmlspecialchars($books['press']), 'pubdate' => htmlspecialchars($books['pubdate']), 'image' => htmlspecialchars($books['image']), 'bookCategory' => htmlspecialchars($books['bookCategory']), 'grade' => htmlspecialchars($books['grade']), 'major' => htmlspecialchars($books['major']), 'extracurricularCategory' => htmlspecialchars($books['extracurricularCategory']), 'remainingAmount' => htmlspecialchars($books['remainingAmount']), 'importTime' => htmlspecialchars($books['importTime']), 'updateTime' => htmlspecialchars($books['updateTime']));
	$index++;
}
echo json_encode($response);
