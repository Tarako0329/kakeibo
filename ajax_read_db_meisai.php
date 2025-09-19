<?php
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer("\$_GET",$_GET);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $daikoumoku = empty($_GET["daikoumoku"])?"%":$_GET["daikoumoku"];
  $chuukoumoku = empty($_GET["chuukoumoku"])?"%":$_GET["chuukoumoku"];
  
  $sql = "SELECT 
      date as date
      ,date as date2
      ,meisai
      ,kin
      ,shuppimoto
      ,kake.daikoumoku
      ,chuukoumoku
      ,memo
      ,guid
      ,sort
      ,meisai as fl_meisai
      ,kin as  fl_kin
      ,shuppimoto as fl_shuppimoto
      ,kake.daikoumoku as fl_daikoumoku
      ,chuukoumoku as fl_chuukoumoku
      ,memo as fl_memo
      ,b_moto
      ,b_pair_no
      ,SEQ
      ,Special
    from kakeibo as kake
    left join daikoumoku_ms as ms
    on kake.daikoumoku=ms.daikoumoku
    and kake.uid = ms.uid
    where kake.uid = :uid 
      and getudo between :from and :to 
      and kake.daikoumoku like :daikoumoku
      and kake.chuukoumoku like :chuukoumoku
    order by date desc,b_pair_no desc,b_moto desc,meisai,memo,kin";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("from", $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("to", (empty($_GET["to"]))?$_GET["fm"]:$_GET["to"], PDO::PARAM_STR);
  $stmt->bindValue("daikoumoku", $daikoumoku, PDO::PARAM_STR);
  $stmt->bindValue("chuukoumoku", $chuukoumoku, PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //$stmt = [];
  $sql = "SELECT * from daikoumoku_ms
    where uid = :uid order by sort";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql = "SELECT max(b_pair_no) as max_pair_no from kakeibo
    where uid = :uid group by uid";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $return = array(
    "meisai" => $dataset,
    "daikou_ms" => $dataset2,
    "max_pair_no" => $dataset3[0],
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
