<?php
require "php_header.php";

log_writer("\$_FILES",$_FILES);
$tempfile = $_FILES['user_file_name']['tmp_name'];
$filename = './upload/' . $_FILES['user_file_name']['name'];

if (is_uploaded_file($tempfile)) {
    if ( move_uploaded_file($tempfile , $filename )) {
	$return = $filename . "をアップロードしました。";
    } else {
        $return = "ファイルをアップロードできません。";
    }
} else {
    $return = "ファイルが選択されていません。";
} 

//jsonとして出力
header('Content-type: application/json');
echo json_encode($return, JSON_UNESCAPED_UNICODE);
?>