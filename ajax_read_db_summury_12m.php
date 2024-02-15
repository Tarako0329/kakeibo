<?php
  //単月・前月比・前年比
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer("\$_GET",$_GET);
  /*
  if($_GET["to"]==="m"){
    $to = date("Ym", strtotime($_GET["fm"]."01"." -1 month"));
  }else if($_GET["to"]==="y"){
    $to = date("Ym", strtotime($_GET["fm"]."01"." -12 month"));
  }else{
    exit();
  }
  */
  //log_writer("\$to",$to);

  //$kikan[] = "月度";
  $i=1;
  $kikan[] = date("Y/m", strtotime($_GET["fm"]."01"." -".(12-$i)." month"));
  while($i<12){
    $i = $i+1;
    $kikan[] = date("Y/m", strtotime($_GET["fm"]."01"." -".(12-$i)." month"));
  }

  $sql = "select COALESCE(ms.sort,999) as sort ,temp.daikoumoku ,temp.chuukoumoku 
    ,sum(m12) as m12c 
    ,sum(sum(m12)) over(PARTITION BY daikoumoku) as m12d
    ,sum(m11) as m11c
    ,sum(sum(m11)) over(PARTITION BY daikoumoku) as m11d 
    ,sum(m10) as m10c
    ,sum(sum(m10)) over(PARTITION BY daikoumoku) as m10d 
    ,sum(m9) as m9c
    ,sum(sum(m9)) over(PARTITION BY daikoumoku) as m9d 
    ,sum(m8) as m8c
    ,sum(sum(m8)) over(PARTITION BY daikoumoku) as m8d 
    ,sum(m7) as m7c
    ,sum(sum(m7)) over(PARTITION BY daikoumoku) as m7d 
    ,sum(m6) as m6c
    ,sum(sum(m6)) over(PARTITION BY daikoumoku) as m6d 
    ,sum(m5) as m5c
    ,sum(sum(m5)) over(PARTITION BY daikoumoku) as m5d 
    ,sum(m4) as m4c
    ,sum(sum(m4)) over(PARTITION BY daikoumoku) as m4d 
    ,sum(m3) as m3c
    ,sum(sum(m3)) over(PARTITION BY daikoumoku) as m3d 
    ,sum(m2) as m2c
    ,sum(sum(m2)) over(PARTITION BY daikoumoku) as m2d 
    ,sum(m1) as m1c
    ,sum(sum(m1)) over(PARTITION BY daikoumoku) as m1d 
    from (
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,kin as m1
      from kakeibo
      where uid = :uid1 and getudo = :baseYM1
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,kin as m2,0 as m1
      from kakeibo
      where uid = :uid2 and getudo = :baseYM2
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,kin as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid3 and getudo = :baseYM3
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,kin as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid4 and getudo = :baseYM4
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,kin as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid5 and getudo = :baseYM5
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,kin as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid6 and getudo = :baseYM6
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,0 as m8,kin as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid7 and getudo = :baseYM7
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,0 as m9,kin as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid8 and getudo = :baseYM8
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,0 as m10,kin as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid9 and getudo = :baseYM9
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,0 as m11,kin as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid10 and getudo = :baseYM10
      UNION ALL 
      select daikoumoku,chuukoumoku ,0 as m12,kin as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid11 and getudo = :baseYM11
      UNION ALL 
      select daikoumoku,chuukoumoku ,kin as m12,0 as m11,0 as m10,0 as m9,0 as m8,0 as m7,0 as m6,0 as m5,0 as m4,0 as m3,0 as m2,0 as m1
      from kakeibo
      where uid = :uid12 and getudo = :baseYM12
    ) as temp
    left join daikoumoku_ms as ms
    on temp.daikoumoku=ms.daikoumoku
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
	$stmt->bindValue("uid6", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid7", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid8", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid9", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid10", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid11", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid12", $_SESSION["uid"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM1",  $_GET["fm"], PDO::PARAM_STR);
  $stmt->bindValue("baseYM2",  date("Ym", strtotime($_GET["fm"]."01"." -1 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM3",  date("Ym", strtotime($_GET["fm"]."01"." -2 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM4",  date("Ym", strtotime($_GET["fm"]."01"." -3 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM5",  date("Ym", strtotime($_GET["fm"]."01"." -4 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM6",  date("Ym", strtotime($_GET["fm"]."01"." -5 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM7",  date("Ym", strtotime($_GET["fm"]."01"." -6 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM8",  date("Ym", strtotime($_GET["fm"]."01"." -7 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM9",  date("Ym", strtotime($_GET["fm"]."01"." -8 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM10", date("Ym", strtotime($_GET["fm"]."01"." -9 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM11", date("Ym", strtotime($_GET["fm"]."01"." -10 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM12", date("Ym", strtotime($_GET["fm"]."01"." -11 month")), PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $data = array(
    "data" => $dataset,
    "label" => $kikan
  );
  header('Content-type: application/json');  
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit();
?>
