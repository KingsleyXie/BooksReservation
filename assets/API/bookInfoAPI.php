<?php
header('Content-Type: application/json');
require_once('./config.php');

$handle = @fopen("https://api.douban.com/v2/book/isbn/" . $_POST['ISBN'],"rb");
if (empty($_POST['ISBN']) OR !$handle) {
	response(1, '未找到书籍信息，请手动录入相关数据');
}
else {
	$data = "";
	while (!feof($handle)) {
		$data .= fread($handle, 10000);
	}
	fclose($handle);
	$prep = json_decode($data);
	echo json_encode(array('code' => 0, 'title' => $prep->title, 'author' => $prep->author[0], 'isMultipleAuthor' => count($prep->author)==1 ? 0:1, 'press' => $prep->publisher, 'pubdate' => $prep->pubdate, 'image' => $prep->images->large));
}
