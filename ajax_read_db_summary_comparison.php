<?php
  //単月・前月比・前年比
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer("\$_GET",$_GET);
  if($_GET["to"]==="m"){
    $m_ago = date("Ym", strtotime($_GET["fm"]."01"." -1 month"));
    $y_ago = date("Ym", strtotime($_GET["fm"]."01"." -12 month"));
  }else{
    exit();
  }
  //log_writer("\$to",$to);
  $sql = "select ms.sort ,temp.daikoumoku ,temp.chuukoumoku 
    ,sum(1y_ago_chuukei) as y_ago_chuukei 
    ,sum(sum(1y_ago_daikei)) over(PARTITION BY daikoumoku) as y_ago_daikei
    ,sum(1m_ago_chuukei) as m_ago_chuukei 
    ,sum(sum(1m_ago_daikei)) over(PARTITION BY daikoumoku) as m_ago_daikei
    ,sum(moto_chuukei) as moto_chuukei
    ,sum(sum(moto_daikei)) over(PARTITION BY daikoumoku) as moto_daikei 
    from (
      select kake.daikoumoku,chuukoumoku ,0 as 1y_ago_chuukei,0 as 1y_ago_daikei,0 as 1m_ago_chuukei,0 as 1m_ago_daikei,kin as moto_chuukei,kin as moto_daikei
      from kakeibo as kake
      where uid = :uid and getudo = :baseYM
      UNION ALL
      select kake.daikoumoku,chuukoumoku ,0 as 1y_ago_chuukei,0 as 1y_ago_daikei,kin as 1m_ago_chuukei,kin as 1m_ago_daikei, 0 as moto_chuukei,0 as moto_daikei
      from kakeibo as kake
      where uid = :uid2 and getudo = :1m_ago
      UNION ALL
      select kake.daikoumoku,chuukoumoku ,kin as 1y_ago_chuukei,kin as 1y_ago_daikei,0 as 1m_ago_chuukei,0 as 1m_ago_daikei, 0 as moto_chuukei,0 as moto_daikei
      from kakeibo as kake
      where uid = :uid3 and getudo = :1y_ago
    ) as temp
    inner join daikoumoku_ms as ms
    on temp.daikoumoku=ms.daikoumoku
    group by ms.sort,temp.daikoumoku,temp.chuukoumoku 
    order by ms.sort,chuukoumoku 
    ";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid2", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid3", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM", $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("1m_ago", $m_ago, PDO::PARAM_STR);
  $stmt->bindValue("1y_ago", $y_ago, PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //log_writer("\$dataset",$dataset);

  header('Content-type: application/json');  
  echo json_encode($dataset, JSON_UNESCAPED_UNICODE);
  exit();
?>
