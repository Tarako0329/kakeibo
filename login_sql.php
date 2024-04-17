<?php
require "php_header.php";
if(MAIN_DOMAIN!=="localhost:81"){
  //session_regenerate_id();
}
$success=false;
log_writer("\$_POST",$_POST);

//$pass = passEx($_POST["pass"],$_POST["mail"],NOM);

if(!empty($_POST)){
  //log_writer("\$pass",$pass);
  if($_POST["login"]==="login"){
    //メアドからユーザIDを取得
    $sql = "select * from user where mail = :mail";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("mail", $_POST["mail"], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();

    $pass = passEx($_POST["pass"],$user[0]["uid"],NOM);
    log_writer("\$pass",$pass);

    $sql = "select * from user where mail = :mail and pass = :pass";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("mail", $_POST["mail"], PDO::PARAM_STR);
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

      $stmt = $pdo_h->prepare("select uid from user where uid = :uid FOR UPDATE");
      while(true){
          //乱数からオーダーナンバーを発行し、受注ヘッダで重複してなければ使用する
          $params["uid"] = substr("0000000".((string)rand(0,99999999)),-8);
          $stmt->bindValue("uid", $params["uid"], PDO::PARAM_STR);
          $stmt->execute();
          $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if(empty($row[0]["uid"])){
              break;
          }
      }
      $pass = passEx($_POST["pass"],$params["uid"],NOM);
      $sql = "insert into user(uid,mail,pass,kisanbi,shukuzitu,nendomatu) values(:uid,:mail,:pass,1,1,12)";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("uid", $params["uid"], PDO::PARAM_STR);
      $stmt->bindValue("mail", $_POST["mail"], PDO::PARAM_STR);
      $stmt->bindValue("pass", $pass, PDO::PARAM_STR);
      $stmt->execute();

      $_SESSION["uid"] = $params["uid"];
      setCookie("mesp_uid", $params["uid"], time()+60*60*24*7, "/", "",true,true);
      $pdo_h->commit();
      $url = "user_setting.php?v=";
      $success=true;
    }catch(Exception $e){
      log_writer("\$e",$e);
      $_SESSION["MSG"]="登録に失敗しました。ユーザーIDがすでに登録されてます。";
      $pdo_h->rollBack();
    }
  }else if($_POST["login"]==="setpass"){
    $sql = "select * from user where mail = :mail";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("mail", $_POST["mail"], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll();
    if(!empty($user[0]["uid"])){
      $_SESSION=[];
      setCookie("mesp_uid","", -1, "/", "",true,true);
      $url = ROOT_URL."user_setting.php?val=".rot13encrypt2($_POST["mail"]);
      send_mail($_POST["mail"],"パスワード再設定",$url);
      $_SESSION["MSG"]="メールを送信しました";
      $url = "login.php?v=";
      $success=true;
    }else{
      log_writer("","メアド未登録：：".$_POST["mail"]);
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