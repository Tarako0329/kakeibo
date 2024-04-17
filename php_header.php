<?php
date_default_timezone_set('Asia/Tokyo'); 

//ini_set('max_execution_time', -1);
//ini_set('max_input_time', -1);
require "./vendor/autoload.php";
require "functions.php";

//.envの取得
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
define("MAIN_DOMAIN",$_ENV["MAIN_DOMAIN"]);
define("ROOT_URL",$_ENV["HTTP"]);

session_start();

if(MAIN_DOMAIN==="localhost:81"){
	$time=date('Ymd-His');
	$id="r.tamura-az@azuma.agns.co.jp";
	$pass="tarako";
}else{
	$time=date('Ymd-His')."up03";
	$id="";
	$pass="";
}


//$pass=dirname(__FILE__);
$title="Me・サポ";

// DBとの接続
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["USER"]);
define("PASSWORD", $_ENV["PASS"]);
$pdo_h = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());

define("SAVEDIR", $_ENV["SAVEDIR"]);
define("NOM", $_ENV["SIO"]);

if(!empty($_SESSION["MSG"])){

}else if(!empty($_POST["login"])){

}else if(empty($_SESSION["uid"])){
	if(empty($_COOKIE["mesp_uid"])){
		//リダイレクト
		log_writer("","セッション切れです");
		$_SESSION["MSG"] = "セッション切れです。再ログインしてください。";
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: login.php?");
		exit();
	}else{
		log_writer("","セッション切れ/クッキーで再セット");
		$_SESSION["uid"]=$_COOKIE["mesp_uid"];
	}
}

//メール送信関連
define("HOST", $_ENV["HOST"]);
define("PORT", $_ENV["PORT"]);
define("FROM", $_ENV["FROM"]);
define("PROTOCOL", $_ENV["PROTOCOL"]);
define("POP_HOST", $_ENV["POP_HOST"]);
define("POP_USER", $_ENV["POP_USER"]);
define("POP_PASS", $_ENV["POP_PASS"]);
define("EXEC_MODE", $_ENV["EXEC_MODE"]);

?>