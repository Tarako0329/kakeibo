<?php
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $sql = "select date as date,date as date2,meisai,kin,shuppimoto,kake.daikoumoku,chuukoumoku,memo,guid ,sort
    from kakeibo as kake
    left join daikoumoku_ms as ms
    on kake.daikoumoku=ms.daikoumoku
    where kake.uid = :uid and getudo between :from and :to order by date";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("from", $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("to", (empty($_GET["to"]))?$_GET["fm"]:$_GET["to"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //$stmt = [];
  $sql = "select * from daikoumoku_ms
    where uid = :uid order by sort";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $return = array(
    "meisai" => $dataset,
    "daikou_ms" => $dataset2
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
