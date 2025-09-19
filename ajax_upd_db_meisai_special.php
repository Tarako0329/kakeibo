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
    //kakeiboテーブルのSpecialを更新。POST[SEQ]で指定されたレコードの項目Specialが0の場合は1に1の場合は0に更新する。
    $pdo_h->beginTransaction();
    $sql = "update kakeibo set Special = CASE WHEN Special = 0 THEN 1 ELSE 0 END where SEQ = :SEQ and uid = :uid";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("SEQ", $_POST["SEQ"], PDO::PARAM_INT);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->execute();
    $pdo_h->commit();

    $status = "success";
  }catch(Exception $e){
    $pdo_h->rollBack();

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
