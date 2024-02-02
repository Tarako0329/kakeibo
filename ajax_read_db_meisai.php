<?php
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer('\$_SESSION["uid"]',++$a);
  $sql = "select date as date,date as date2,meisai,kin,shuppimoto,kake.daikoumoku,chuukoumoku,memo,guid ,sort
    from kakeibo as kake
    left join daikoumoku_ms as ms
    on kake.daikoumoku=ms.daikoumoku
    where uid = :uid and getudo between :from and :to order by date";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", "tarako", PDO::PARAM_STR);
  $stmt->bindValue("from", $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("to", (empty($_GET["to"]))?$_GET["fm"]:$_GET["to"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  header('Content-type: application/json');  
  echo json_encode($dataset, JSON_UNESCAPED_UNICODE);
  exit();
?>
