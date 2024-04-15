<?php
  require "php_header.php";
  /*if(empty($_GET)){
    exit();
  }*/
  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $status = "warning";
  $msg = "";
  try{
    $sql = "update user set kisanbi=:kisanbi,shukuzitu=:shukuzitu,nendomatu=:nendomatu where uid = :uid";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("kisanbi", $_POST["kisanbi"], PDO::PARAM_STR);
    $stmt->bindValue("shukuzitu", $_POST["shukuzitu"], PDO::PARAM_STR);
    $stmt->bindValue("nendomatu", $_POST["nendomatu"], PDO::PARAM_STR);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
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
