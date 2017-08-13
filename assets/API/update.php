<?php
session_start();
header('Content-Type: application/json');
require_once('./config.php');

if (!isset($_SESSION['username'])) {
	$response = array('code' => 1);
	echo json_encode($response);
	return;
}

$isMA = 0;
if (isset($_POST['updIsMultipleAuthor'])) {
	$isMA = 1;
}

if (!isset($_POST['updImage'])) {
	$_POST['updImage'] = "./assets/pictures/defaultCover.png";
}

if ($_POST['updBookCategory'] == 'CategoryA') {
	if (!(isset($_POST['updTitle']) AND isset($_POST['updAuthor']) AND isset($_POST['updPress']) AND isset($_POST['updPubdate']) AND isset($_POST['updGrade']) AND isset($_POST['updMajor']) AND isset($_POST['updRemainingAmount']))) {
		$response = array('code' => 2);
		echo $response;
		return;
	}

	$sql = 'UPDATE `books` SET `title`=?,`author`=?,`isMultipleAuthor`=?,`press`=?,`pubdate`=?,`image`=?,`bookCategory`=?,`grade`=?,`major`=?,`remainingAmount`=? WHERE  `bookID` = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['updTitle'], $_POST['updAuthor'], $isMA, $_POST['updPress'], $_POST['updPubdate'], $_POST['updImage'], $_POST['updBookCategory'], $_POST['updGrade'], $_POST['updMajor'], $_POST['updRemainingAmount'], $_POST['bookID']));
}

if ($_POST['updBookCategory'] == 'CategoryB') {
	if (!(isset($_POST['updTitle']) AND isset($_POST['updAuthor']) AND isset($_POST['updPress']) AND isset($_POST['updPubdate']) AND isset($_POST['updExtracurricularCategory']) AND isset($_POST['updRemainingAmount']))) {
		$response = array('code' => 2);
		echo json_encode($response);
		return;
	}
	
	$sql = 'UPDATE `books` SET `title`=?,`author`=?,`isMultipleAuthor`=?,`press`=?,`pubdate`=?,`image`=?,`bookCategory`=?,`extracurricularCategory`=?,`remainingAmount`=? WHERE  `bookID` = ?';
	$stmt = $connect->prepare($sql);
	$stmt->execute(array($_POST['updTitle'], $_POST['updAuthor'], $isMA, $_POST['updPress'], $_POST['updPubdate'], $_POST['updImage'], $_POST['updBookCategory'], $_POST['updExtracurricularCategory'], $_POST['updRemainingAmount'], $_POST['bookID']));
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($result)) {
	$response = array('code' => 0);
}
else {
	$response = array('code' => 3);
}
echo json_encode($response);
?>
