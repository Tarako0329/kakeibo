<?php
require "config.php";
require "functions.php";
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=AnpiKaitou.csv");

//データベースに接続する
$sql = str_replace("\\","",$_POST["SQL"]);

$result = $mysqli->query( $sql );

$str = "社員No,社員名,本人安否,家族安否,現在地,自宅損害,出社可否,回答日時,伝言\n";

echo mb_convert_encoding($str,"sjis-win","UTF-8");

while($row = $result->fetch_assoc()){
	call_user_func("info_disp_grid", $row);
}


// =========================================================
// 個別情報表示(表形式)
// =========================================================
function info_disp_grid($array){
	echo ($array["syainno"]).",";
	echo mb_convert_encoding(rot13decrypt($array["syainmei"]),"sjis-win","UTF-8").",";
	switch ($array["myself"]) {
		case 1:
			$str =  "無事,";
			break;
		case 2:
			$str =  "軽症,";
			break;
		case 3:
			$str =  "重症,";
			break;
		default:
			$str =  "未入力,";
			break;
	}
	echo mb_convert_encoding($str,"sjis-win","UTF-8");
	switch ($array["family"]) {
		case 1:
			$str =  "全員無事,";
			break;
		case 2:
			$str =  "軽症者がいる,";
			break;
		case 3:
			$str =  "重症者がいる,";
			break;
		case 4:
			$str =  "わからない,";
			break;
		default:
			$str =  "未入力,";
			break;
	}
	echo mb_convert_encoding($str,"sjis-win","UTF-8");
	switch ($array["address"]) {
		case 1:
			$str =  "自宅,";
			break;
		case 2:
			$str =  "会社,";
			break;
		case 3:
			$str =  "取引先･現場,";
			break;
		case 4:
			$str =  "通勤途中,";
			break;
		case 5:
			$str =  "避難場所,";
			break;
		case 6:
			$str =  "その他,";
			break;
		default:
			$str =  "未入力,";
			break;
	}
	echo mb_convert_encoding($str,"sjis-win","UTF-8");
	switch ($array["home"]) {
		case 1:
			$str =  "無事,";
			break;
		case 2:
			$str =  "半壊,";
			break;
		case 3:
			$str =  "全壊,";
			break;
		case 4:
			$str =  "わからない,";
			break;
		default:
			$str =  "未入力,";
			break;
	}
	echo mb_convert_encoding($str,"sjis-win","UTF-8");
	switch ($array["office"]) {
		case 1:
			$str =  "可能,";
			break;
		case 2:
			$str =  "不可能,";
			break;
		case 2:
			$str =  "安否確認を優先したい,";
			break;
		default:
			$str =  "未入力,";
			break;
	}
	echo mb_convert_encoding($str,"sjis-win","UTF-8");
	echo $array["timestamp"].",";
	echo mb_convert_encoding($array["etc"],"sjis-win","UTF-8")."\n";
}

$mysqli->close();
?>