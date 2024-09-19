<?php
  //単月・前月比・前年比
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer("\$_GET",$_GET);

  $i=12;
  $kikan[] = date("Y/m", strtotime($_GET["fm"]."01"." -".(60-$i)." month"));
  while($i<60){
    $i = $i+12;
    $kikan[] = date("Y/m", strtotime($_GET["fm"]."01"." -".(60-$i)." month"));
  }

  $sql = "select COALESCE(ms.sort,999) as sort ,temp.daikoumoku ,temp.chuukoumoku 
    ,sum(m5) as m12c
    ,sum(sum(m5)) over(PARTITION BY daikoumoku) as m12d 
    ,sum(m4) as m11c
    ,sum(sum(m4)) over(PARTITION BY daikoumoku) as m11d 
    ,sum(m3) as m10c
    ,sum(sum(m3)) over(PARTITION BY daikoumoku) as m10d 
    ,sum(m2) as m9c
    ,sum(sum(m2)) over(PARTITION BY daikoumoku) as m9d 
    ,sum(m1) as m8c
    ,sum(sum(m1)) over(PARTITION BY daikoumoku) as m8d 
    from (
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,0 as m3,0 as m2,kin as m1
      from kakeibo
      where uid = :uid1 and getudo between :baseYM1 and :baseYM2
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,0 as m3,kin as m2,0 as m1
      from kakeibo
      where uid = :uid2 and getudo between :baseYM3 and :baseYM4
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,kin as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid3 and getudo between :baseYM5 and :baseYM6
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,kin as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid4 and getudo between :baseYM7 and :baseYM8
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,kin as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid5 and getudo between :baseYM9 and :baseYM10
    ) as temp
    left join daikoumoku_ms as ms
    on temp.daikoumoku=ms.daikoumoku
    and temp.uid = ms.uid
    group by COALESCE(ms.sort,999),temp.daikoumoku,temp.chuukoumoku 
    having temp.daikoumoku <> ''
    order by COALESCE(ms.sort,999),chuukoumoku 
    ";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid1", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid2", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid3", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid4", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid5", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM1",  date("Ym", strtotime($_GET["fm"]."01"." -11 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM2",  $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM3",  date("Ym", strtotime($_GET["fm"]."01"." -23 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM4",  date("Ym", strtotime($_GET["fm"]."01"." -12 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM5",  date("Ym", strtotime($_GET["fm"]."01"." -35 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM6",  date("Ym", strtotime($_GET["fm"]."01"." -24 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM7",  date("Ym", strtotime($_GET["fm"]."01"." -47 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM8",  date("Ym", strtotime($_GET["fm"]."01"." -36 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM9",  date("Ym", strtotime($_GET["fm"]."01"." -59 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM10", date("Ym", strtotime($_GET["fm"]."01"." -48 month")), PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);
/*
  log_writer("baseYM1" ,date("Ym", strtotime($_GET["fm"]."01"." -11 month")));
  log_writer("baseYM2" ,$_GET["fm"]);
  log_writer("baseYM3" ,date("Ym", strtotime($_GET["fm"]."01"." -23 month")));
  log_writer("baseYM4" ,date("Ym", strtotime($_GET["fm"]."01"." -12 month")));
  log_writer("baseYM5" ,date("Ym", strtotime($_GET["fm"]."01"." -35 month")));
  log_writer("baseYM6" ,date("Ym", strtotime($_GET["fm"]."01"." -24 month")));
  log_writer("baseYM7" ,date("Ym", strtotime($_GET["fm"]."01"." -47 month")));
  log_writer("baseYM8" ,date("Ym", strtotime($_GET["fm"]."01"." -36 month")));
  log_writer("baseYM9" ,date("Ym", strtotime($_GET["fm"]."01"." -59 month")));
  log_writer("baseYM10",date("Ym", strtotime($_GET["fm"]."01"." -48 month")));
*/


  $data = array(
    "data" => $dataset,
    "label" => $kikan
  );
  header('Content-type: application/json');  
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit();
?>
