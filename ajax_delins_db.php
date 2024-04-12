<?php
  require "php_header.php";
  $return = "false";
  if(empty($_POST)){
    exit();
  }
  //log_writer("\$POST",$_POST);

  $dataset = json_decode($_POST["csv"], true);
  $start_date = $_POST["start"];
  $end_date = $_POST["end"];
  $start_YM = $_POST["startYM"];
  $end_YM = (empty($_POST["endYM"]))?$_POST["startYM"]:$_POST["endYM"];

  
  try{
    $pdo_h->beginTransaction();

    $stmt = $pdo_h->prepare("delete from kakeibo where getudo between :start and :end and uid = :uid");
    $stmt->bindValue("start", $start_YM, PDO::PARAM_STR);
    $stmt->bindValue("end", $end_YM, PDO::PARAM_STR);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
  
    $stmt->execute();
    
    $sql = "insert into kakeibo(uid,guid,date,meisai,kin,shuppimoto,daikoumoku,chuukoumoku,memo) values(:uid,:guid,:date,:meisai,:kin,:shuppimoto,:daikoumoku,:chuukoumoku,:memo)";
    $stmt = $pdo_h->prepare($sql);
    foreach($dataset as $row){
      //log_writer("\$row[date]",$row[0]);
      $guid = empty($row["guid"])?getGUID():$row["guid"];

      $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
      $stmt->bindValue("guid", $guid, PDO::PARAM_STR);
      $stmt->bindValue("date", $row['date'], PDO::PARAM_STR);
      $stmt->bindValue("meisai", $row["meisai"], PDO::PARAM_STR);
      $stmt->bindValue("kin", $row["kin"]+0, PDO::PARAM_INT);
      $stmt->bindValue("shuppimoto", $row["shuppimoto"], PDO::PARAM_STR);
      $stmt->bindValue("daikoumoku", $row["daikoumoku"], PDO::PARAM_STR);
      $stmt->bindValue("chuukoumoku", $row["chuukoumoku"], PDO::PARAM_STR);
      $stmt->bindValue("memo", empty($row["memo"])?"":$row["memo"], PDO::PARAM_STR);
      $stmt->execute();
    }
    
    $pdo_h->commit();
    upd_getudo($pdo_h,$start_date,$end_date);
    $return = "success";
  }catch(Exception $e){
    log_writer("Exception \$e",$e);
    $pdo_h->rollBack();
  }

  $data = array(
    "status" => $return
  );
  //log_writer("\$data",$data);
  //jsonとして出力
  header('Content-type: application/json');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
