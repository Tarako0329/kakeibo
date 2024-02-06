<?php
  //単月・前月比・前年比
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer("\$_GET",$_GET);
  if($_GET["to"]==="m"){
    $to = date("Ym", strtotime($_GET["fm"]."01"." -1 month"));
  }else if($_GET["to"]==="y"){
    $to = date("Ym", strtotime($_GET["fm"]."01"." -12 month"));
  }else{
    exit();
  }
  //log_writer("\$to",$to);
  $sql = "select ms.sort ,temp.daikoumoku ,temp.chuukoumoku 
    ,sum(hikaku_chuukei) as hikaku_chuukei 
    ,sum(sum(hikaku_daikei)) over(PARTITION BY daikoumoku) as hikaku_daikei
    ,sum(moto_chuukei) as moto_chuukei
    ,sum(sum(moto_daikei)) over(PARTITION BY daikoumoku) as moto_daikei 
    from (
      select kake.daikoumoku,chuukoumoku ,0 as hikaku_chuukei,0 as hikaku_daikei,(kin) as moto_chuukei,((kin)) as moto_daikei
      from kakeibo as kake
      where uid = :uid and getudo = :baseYM
      UNION ALL 
      select kake.daikoumoku,chuukoumoku ,(kin) as hikaku_chuukei,((kin)) as hikaku_daikei, 0 as hikaku_chuukei,0 as hikaku_daikei
      from kakeibo as kake
      where uid = :uid2 and getudo = :hikakuYM
    ) as temp
    inner join daikoumoku_ms as ms
    on temp.daikoumoku=ms.daikoumoku
    group by ms.sort,temp.daikoumoku,temp.chuukoumoku 
    order by ms.sort,chuukoumoku 
    ";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid2", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM", $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("hikakuYM", $to, PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  header('Content-type: application/json');  
  echo json_encode($dataset, JSON_UNESCAPED_UNICODE);
  exit();
?>
