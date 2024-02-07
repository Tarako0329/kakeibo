<?php
require "php_header.php";

//log_writer("\$_FILES",$_FILES);
$tempfile = $_FILES['user_file_name']['tmp_name'];
$filename = 'upload/' . $_FILES['user_file_name']['name'];
$stats = "false";

if (is_uploaded_file($tempfile)) {
    if ( move_uploaded_file($tempfile , $filename )) {
	    $msg = $filename . "をアップロードしました。";
        $stats = "success";
    } else {
        $msg = "ファイルをアップロードできません。";
    }
} else {
    $msg = "ファイルが選択されていません。";
} 

$return = array(
    "filename" => $filename
    ,"msg" => $msg
    ,"status" => $stats
);

//jsonとして出力
header('Content-type: application/json');
echo json_encode($return, JSON_UNESCAPED_UNICODE);
?>