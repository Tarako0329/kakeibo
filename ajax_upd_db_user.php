<?php
  require "php_header.php";
  /*if(empty($_GET)){
    exit();
  }*/
  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  log_writer('\$_POST',$_POST);
  $status = "warning";
  $msg = "";
  try{
    if($_POST["pass_hen"]==="true"){
      $pass = passEx($_POST["pass"],$_POST["uid"],NOM);
      log_writer("パスワード変更",$pass);

      $sql = "update user set pass=:pass where uid = :uid";
      $stmt = $pdo_h->prepare($sql);
      $stmt->bindValue("pass", $pass, PDO::PARAM_STR);
      $stmt->bindValue("uid", $_POST["uid"], PDO::PARAM_STR);
      $stmt->execute();
    }
    $sql = "update user set mail=:mail,kisanbi=:kisanbi,shukuzitu=:shukuzitu,nendomatu=:nendomatu where uid = :uid";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("mail", $_POST["mail"], PDO::PARAM_STR);
    $stmt->bindValue("kisanbi", $_POST["kisanbi"], PDO::PARAM_STR);
    $stmt->bindValue("kisanbi", $_POST["kisanbi"], PDO::PARAM_STR);
    $stmt->bindValue("shukuzitu", $_POST["shukuzitu"], PDO::PARAM_STR);
    $stmt->bindValue("nendomatu", $_POST["nendomatu"], PDO::PARAM_STR);
    $stmt->bindValue("uid", $_POST["uid"], PDO::PARAM_STR);
    $stmt->execute();

    upd_getudo($pdo_h,0,0);

    $status = "success";
  }catch(Exception $e){
    $status = "danger";
    $msg = json_encode($e, JSON_UNESCAPED_UNICODE);
  }


  $return = array(
    "status" => $status,
    "msg" => $msg
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
