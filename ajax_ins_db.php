<?php
  require "php_header.php";
  $return_satas = "success";
  log_writer("\$POST",$_POST);

  $dataset = $_POST["csv"];
  $start = $_POST["start"];
  $end = $_POST["end"];
  $return = "false";
  try{
    $pdo_h->beginTransaction();
    $stmt = $pdo_h->prepare("delete from kakeibo where date between :start and :end");
    $stmt->bindValue("start", $start, PDO::PARAM_STR);
    $stmt->bindValue("end", $end, PDO::PARAM_STR);
    $stmt->execute();

    $sql = "insert into kakeibo(uid,guid,date,meisai,kin,shuppimoto,daikoumoku,chuukoumoku,memo) values(:uid,:guid,:date,:meisai,:kin,:shuppimoto,:daikoumoku,:chuukoumoku,:memo)";
    $stmt = $pdo_h->prepare($sql);
    foreach($dataset as $row){
      //log_writer("\$row",$row);
      //log_writer("\$row[date]",$row[0]);
      $stmt->bindValue("uid", "tarako", PDO::PARAM_STR);
      $stmt->bindValue("guid", getGUID(), PDO::PARAM_STR);
      $stmt->bindValue("date", $row['date'], PDO::PARAM_STR);
      $stmt->bindValue("meisai", $row["meisai"], PDO::PARAM_STR);
      $stmt->bindValue("kin", $row["kin"]+0, PDO::PARAM_INT);
      $stmt->bindValue("shuppimoto", $row["shuppimoto"], PDO::PARAM_STR);
      $stmt->bindValue("daikoumoku", $row["daikou"], PDO::PARAM_STR);
      $stmt->bindValue("chuukoumoku", $row["chuukou"], PDO::PARAM_STR);
      $stmt->bindValue("memo", empty($row["memo"])?"":$row["memo"], PDO::PARAM_STR);
      $stmt->execute();
  
    }
    $pdo_h->commit();
    $return = "success";
  }catch(Exception $e){
    log_writer("Exception \$e",$e);
    $pdo_h->rollBack();
  }
  //jsonとして出力
  header('Content-type: application/json');
  echo json_encode($return, JSON_UNESCAPED_UNICODE);


?>
