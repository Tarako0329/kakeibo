<?php
  require "php_header.php";
  /*if(empty($_GET)){
    exit();
  }*/
  //log_writer('\$_SESSION["uid"]',++$a);
  //log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  log_writer('\$_POST',$_POST);
  //$dataset = json_decode($_POST["bunrui_ms"], true);
  $dataset = $_POST["bunrui_ms"];
  //$json = file_get_contents("php://input");
  //$dataset = json_decode($json, true);
  log_writer('\$dataset',$dataset);
  $status = "warning";
  $msg = "";
  try{
    //デリイン
    $pdo_h->beginTransaction();

    $sql = "delete from daikoumoku_ms where uid = :uid";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("uid", $_POST["uid"], PDO::PARAM_STR);
    $stmt->execute();

    $sql = "insert into daikoumoku_ms values(:uid,:sort,:daikoumoku)";
    
    foreach($dataset as $row){
      log_writer('\$row',$row);
      $stmt = $pdo_h->prepare($sql);

      $stmt->bindValue("uid", $row["uid"], PDO::PARAM_STR);
      $stmt->bindValue("sort", $row["sort"], PDO::PARAM_STR);
      $stmt->bindValue("daikoumoku", $row["daikoumoku"], PDO::PARAM_STR);

      $stmt->execute();
    }

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
