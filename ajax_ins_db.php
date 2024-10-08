<?php
  require "php_header.php";
  $return = "false";
  if(empty($_POST)){
    exit();
  }

  $dataset = json_decode($_POST["csv"], true);
  $start_date = $_POST["start"];
  $end_date = $_POST["end"];
  $filename = $_POST["filename"];
  log_writer("\$POST",$_POST);

  try{
    $pdo_h->beginTransaction();

    $stmt = $pdo_h->prepare("select guid from kakeibo where date between :start and :end and uid = :uid");
    $stmt->bindValue("start", $start_date, PDO::PARAM_STR);
    $stmt->bindValue("end", $end_date, PDO::PARAM_STR);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->execute();
    $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($stmt as $row){
      $stmt2 = $pdo_h->prepare("delete from kakeibo_plus where guid = :guid and uid = :uid");
      $stmt2->bindValue("guid", $row["guid"], PDO::PARAM_STR);
      $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
      $stmt2->execute();
    }

    $stmt = $pdo_h->prepare("delete from kakeibo where date between :start and :end and uid = :uid");
    $stmt->bindValue("start", $start_date, PDO::PARAM_STR);
    $stmt->bindValue("end", $end_date, PDO::PARAM_STR);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->execute();
    
    $sql = "insert into kakeibo(uid,guid,date,meisai,kin,shuppimoto,daikoumoku,chuukoumoku,memo) values(:uid,:guid,:date,:meisai,:kin,:shuppimoto,:daikoumoku,:chuukoumoku,:memo)";
    $stmt = $pdo_h->prepare($sql);
    $sql2 = "insert into kakeibo_plus(uid,guid,cus_1,cus_2) values(:uid,:guid,:daikoumoku,:chuukoumoku)";
    $stmt2 = $pdo_h->prepare($sql2);
    foreach($dataset as $row){
      //log_writer("\$row[date]",$row[0]);
      $guid = empty($row["guid"])?getGUID():$row["guid"];
      //log_writer("\$row",$row);
      //log_writer("\$_SESSION[uid]",$_SESSION["uid"]);
      //log_writer("\$guid",$guid);

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

      $stmt2->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
      $stmt2->bindValue("guid", $guid, PDO::PARAM_STR);
      $stmt2->bindValue("daikoumoku", $row["daikoumoku"], PDO::PARAM_STR);
      $stmt2->bindValue("chuukoumoku", $row["chuukoumoku"], PDO::PARAM_STR);
      $stmt2->execute();
    }
    
    
    //ログを追記
    $sql = "insert into import_log(id,filename,from_ymd,to_ymd) values(:uid,:filename,:start,:end)";
    $stmt = $pdo_h->prepare($sql);
    $stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
    $stmt->bindValue("filename", $filename, PDO::PARAM_STR);
    $stmt->bindValue("start", $start_date, PDO::PARAM_STR);
    $stmt->bindValue("end", $end_date, PDO::PARAM_STR);
    $stmt->execute();
    $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
