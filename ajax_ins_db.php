<?php
  require "php_header.php";
  $return_satas = "success";
  //log_writer("\$POST",$_POST);
  //log_writer("\$POST csv",json_decode($_POST["csv"], true));

  $dataset = json_decode($_POST["csv"], true);
  $start = $_POST["start"];
  $end = (empty($_POST["end"]))?$_POST["start"]:$_POST["end"];

  if(strlen($start)===6){//[yyyymm]
    $flg="getudo";
  }else if(strlen($start)===10){//[yyyy-mm-dd]
    $flg="import";
  }else{
    exit();
  }

  $return = "false";
  try{
    $pdo_h->beginTransaction();
    if($flg!=="getudo"){
      $stmt = $pdo_h->prepare("delete from kakeibo_plus where guid in ( select guid from kakeibo where date between :start and :end)");
      $stmt->bindValue("start", $start, PDO::PARAM_STR);
      $stmt->bindValue("end", $end, PDO::PARAM_STR);
      $stmt->execute();
    }else{
      //月度指定時はデータ更新なので[kakeibo_plus]は更新しない
    }
    

    if($flg==="getudo"){
      $stmt = $pdo_h->prepare("delete from kakeibo where getudo between :start and :end");
    }else{
      $stmt = $pdo_h->prepare("delete from kakeibo where date between :start and :end");
    }
    
    $stmt->bindValue("start", $start, PDO::PARAM_STR);
    $stmt->bindValue("end", $end, PDO::PARAM_STR);
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

      if($flg!=="getudo"){
        $stmt2->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
        $stmt2->bindValue("guid", $guid, PDO::PARAM_STR);
        $stmt2->bindValue("daikoumoku", $row["daikoumoku"], PDO::PARAM_STR);
        $stmt2->bindValue("chuukoumoku", $row["chuukoumoku"], PDO::PARAM_STR);
        $stmt2->execute();
      }
    }
    
    $pdo_h->commit();
    upd_getudo($pdo_h);
    $return = "success";
  }catch(Exception $e){
    log_writer("Exception \$e",$e);
    $pdo_h->rollBack();
  }
  //jsonとして出力
  header('Content-type: application/json');
  echo json_encode($return, JSON_UNESCAPED_UNICODE);


?>
