<?php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['type'])) {
	header('Location: http://p1.img.cctvpic.com/20120409/images/1333902721891_1333902721891_r.jpg');
	return;
}

// type value - 1: login, 2: logout

switch ($_POST['type']) {
	case 1:
		if (!(isset($_POST['username']) AND isset($_POST['password'])))
		{
		    $response = array('code' => 2);
		    echo json_encode($response);
		    return;
		}
		if ($_POST['username'] != 'test' OR hash('sha256', $_POST['password']) != '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08') {
		// if ($_POST['username'] != 'admin' OR hash('sha256', $_POST['password']) != 'e7488ae5d86390d0c79135f12107cbb086f3a544d459db412b6410edd44a2a29') {
		    $response = array('code' => 1);
		    echo json_encode($response);
		    return;
		}
		$_SESSION['username'] = 'admin';
		$response = array('code' => 0);
		break;
	
	case 2:
		unset ($_SESSION['username']);
	    $response = array('code' => 0);
		break;
	
	default:
		$response = array('error' => 1);
		break;
}

echo json_encode($response);
