<?php
header('Content-Type: application/json');
$handle = @fopen("https://api.douban.com/v2/book/isbn/" . $_POST['ISBN'],"rb");
if (empty($_POST['ISBN']) OR !$handle) {
	$response = json_encode(array('code' => 1));
}
else {
	$data = "";
	while (!feof($handle)) {
		$data .= fread($handle, 10000);
	}
	fclose($handle);
	$prep = json_decode($data);
	$response = json_encode(array('code' => 0, 'title' => $prep->title, 'author' => $prep->author[0], 'isMultipleAuthor' => count($prep->author)==1 ? 0:1, 'press' => $prep->publisher, 'pubdate' => $prep->pubdate, 'image' => $prep->images->large));
}
echo $response;
