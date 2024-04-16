<?php
require "php_header.php";
if(MAIN_DOMAIN!=="localhost:81"){
  //session_regenerate_id();
}
$success=false;
log_writer("\$_POST",$_POST);

$pass = passEx($_POST["pass"],$_POST["id"],NOM);

if(!empty($_POST)){
  log_writer("\$pass",$pass);
  if($_POST["login"]==="login"){
    $sql = "select * from user where uid = :id and pass = :pass";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_POST["id"], PDO::PARAM_STR);
    $stmt->bindValue("pass",$pass , PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();
    if(!empty($user[0]["uid"])){
      $_SESSION["uid"] = $user[0]["uid"];
      $_SESSION["name"] = $user[0]["name"];
      setCookie("mesp_uid", $user[0]["uid"], time()+60*60*24*7, "/", "",true,true);
      $url = "index.php?v=";
      $success=true;
    }else{
      log_writer("","ログイン失敗");
      $_SESSION["MSG"]="メールアドレスまたはパスワードが違います";
    }
  }else if($_POST["login"]==="newlogin"){
    try{
      $pdo_h->beginTransaction();

      $sql = "insert into user(uid,pass,kisanbi,shukuzitu,nendomatu) values(:id,:pass,1,1,12)";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("id", $_POST["id"], PDO::PARAM_STR);
      $stmt->bindValue("pass", $pass, PDO::PARAM_STR);
      $stmt->execute();

      $_SESSION["uid"] = $_POST["id"];
      setCookie("mesp_uid", $_POST["uid"], time()+60*60*24*7, "/", "",true,true);
      $pdo_h->commit();
      $url = "user_setting.php?v=";
      $success=true;
    }catch(Exception $e){
      log_writer("\$e",$e);
      $_SESSION["MSG"]="登録に失敗しました。ユーザーIDがすでに登録されてます。";
      $pdo_h->rollBack();
    }
  }else if($_POST["login"]==="setpass"){
    $sql = "select * from user where uid = :id";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("id", $_POST["id"], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();
    if(!empty($user[0]["uid"])){
      $_SESSION=[];
      setCookie("mesp_uid","", -1, "/", "",true,true);
      $url = ROOT_URL."user_setting.php?val=".rot13encrypt2($_POST["id"]);
      send_mail($_POST["id"],"パスワード再設定",$url);
      $_SESSION["MSG"]="メールを送信しました";
      $url = "login.php?v=";
      $success=true;
    }else{
      log_writer("","ログイン失敗");
      $_SESSION["MSG"]="メールアドレスは登録されてません";
    }

  }
}
if($success){
  //リダイレクト
  $token=get_token();
  try{

    log_writer("","ログイン成功");
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$url.$token);
    exit();
  }catch(Exception $e){
    $_SESSION["MSG"]="loginkeeper登録失敗。";
    log_writer("loginkeeper登録失敗。",$e);
    $pdo_h->rollBack();
  }
}
//リダイレクト
log_writer("","ログイン失敗");
header("HTTP/1.1 301 Moved Permanently");
header("Location: login.php");
exit();
?>