<?php
  //単月・前月比・前年比
  require "php_header.php";
  if(empty($_GET)){
    exit();
  }
  //log_writer("\$_GET",$_GET);

  $i=12;$j=0;
  $kikan[] = date("Y-m", strtotime($_GET["fm"]."01"." -".(60-$i)." month"));
  while($i<60){
    $i = $i+12;
    if($j===3){
    }
    $kikan[] = date("Y-m", strtotime($_GET["fm"]."01"." -".(60-$i)." month"));
    $j++;
  }
  $kikan[] = "去年±万";
  $kikan[] = "過去4年avg";
  $kikan[] = "平均±万";

  $special = $_GET["special"];
  $sql = "SELECT 
      tmp.* 
      /*去年との差矢印*/
      ,CASE
        WHEN m8c > 0 AND m7c > 0 THEN 'Blue'
        WHEN m8c > 0 AND m7c < 0 THEN 'Red'
        WHEN m8c < 0 AND m7c > 0 THEN 'uRed'
        WHEN m8c < 0 AND m7c < 0 THEN 'dBlue'
        ELSE ''
      END AS c_color
      ,CASE
        WHEN m8d > 0 AND m7d > 0 THEN 'Blue'
        WHEN m8d > 0 AND m7d < 0 THEN 'Red'
        WHEN m8d < 0 AND m7d > 0 THEN 'uRed'
        WHEN m8d < 0 AND m7d < 0 THEN 'dBlue'
        ELSE ''
      END AS d_color
      /*平均との差矢印*/
      ,CASE
        WHEN m8c > 0 AND m5c > 0 THEN 'Blue'
        WHEN m8c > 0 AND m5c < 0 THEN 'Red'
        WHEN m8c < 0 AND m5c > 0 THEN 'uRed'
        WHEN m8c < 0 AND m5c < 0 THEN 'dBlue'
        ELSE ''
      END AS c_color2
      ,CASE
        WHEN m8d > 0 AND m5d > 0 THEN 'Blue'
        WHEN m8d > 0 AND m5d < 0 THEN 'Red'
        WHEN m8d < 0 AND m5d > 0 THEN 'uRed'
        WHEN m8d < 0 AND m5d < 0 THEN 'dBlue'
        ELSE ''
      END AS d_color2
    FROM (
    SELECT COALESCE(ms.sort,999) as sort ,temp.daikoumoku ,temp.chuukoumoku 
    ,sum(IF(Special<=$special,m5,0)) as m12c
    ,sum(sum(IF(Special<=$special,m5,0))) over(PARTITION BY daikoumoku) as m12d 
    ,sum(IF(m5<>0,Special,0)) as m12sflg
    ,sum(sum(IF(m5<>0,Special,0))) over(PARTITION BY daikoumoku) as m12dflg 
    ,sum(IF(Special<=$special,m4,0)) as m11c
    ,sum(sum(IF(Special<=$special,m4,0))) over(PARTITION BY daikoumoku) as m11d 
    ,sum(IF(m4<>0,Special,0)) as m11sflg
    ,sum(sum(IF(m4<>0,Special,0))) over(PARTITION BY daikoumoku) as m11dflg 
    ,sum(IF(Special<=$special,m3,0)) as m10c
    ,sum(sum(IF(Special<=$special,m3,0))) over(PARTITION BY daikoumoku) as m10d 
    ,sum(IF(m3<>0,Special,0)) as m10sflg
    ,sum(sum(IF(m3<>0,Special,0))) over(PARTITION BY daikoumoku) as m10dflg 
    ,sum(IF(Special<=$special,m2,0)) as m9c
    ,sum(sum(IF(Special<=$special,m2,0))) over(PARTITION BY daikoumoku) as m9d 
    ,sum(IF(m2<>0,Special,0)) as m9sflg
    ,sum(sum(IF(m2<>0,Special,0))) over(PARTITION BY daikoumoku) as m9dflg 
    ,sum(IF(Special<=$special,m1,0)) as m8c
    ,sum(sum(IF(Special<=$special,m1,0))) over(PARTITION BY daikoumoku) as m8d 
    ,sum(IF(m1<>0,Special,0)) as m8sflg
    ,sum(sum(IF(m1<>0,Special,0))) over(PARTITION BY daikoumoku) as m8dflg 
    /*去年と今年の差額*/
    ,ROUND(((sum(IF(Special<=$special,m2,0)) ) - sum(IF(Special<=$special,m1,0))) / IF(m1 > 0, -10000,10000), 0) as m7c /*去年と今年の差額*/
    ,ROUND((((sum(sum(IF(Special<=$special,m2,0))) over(PARTITION BY daikoumoku)) ) - (sum(sum(IF(Special<=$special,m1,0))) over(PARTITION BY daikoumoku))) / IF(m1 > 0, -10000,10000), 0) as m7d /* 去年と今年の差額 */
    ,sum(IF(m2+m1<>0,Special,0)) as m7sflg
    ,sum(sum(IF(m2+m1<>0,Special,0))) over(PARTITION BY daikoumoku) as m7dflg 
    /*過去四年平均*/
    ,ROUND(sum(IF(Special<=$special,zennennkei,0)) / 4, 0) as m6c /*中項目過去四年平均*/
    ,ROUND((sum(sum(IF(Special<=$special,zennennkei,0))) over(PARTITION BY daikoumoku)) / 4, 0) as m6d /* 大項目過去四年平均 */
    ,sum(IF(zennennkei<>0,Special,0)) as m6sflg
    ,sum(sum(IF(zennennkei<>0,Special,0))) over(PARTITION BY daikoumoku) as m6dflg 
    /*過去四年平均と今年の差*/
    ,ROUND(((sum(IF(Special<=$special,zennennkei,0)) / 4) - sum(IF(Special<=$special,m1,0))) / IF(m1 > 0, -10000,10000), 0) as m5c /*中項目過去四年平均と今年の差額*/
    ,ROUND((((sum(sum(IF(Special<=$special,zennennkei,0))) over(PARTITION BY daikoumoku)) / 4) - (sum(sum(IF(Special<=$special,m1,0))) over(PARTITION BY daikoumoku))) / IF(m1 > 0, -10000,10000), 0) as m5d /* 大項目過去四年平均と今年の差額 */
    ,sum(IF(zennennkei+m1<>0,Special,0)) as m5sflg
    ,sum(sum(IF(zennennkei+m1<>0,Special,0))) over(PARTITION BY daikoumoku) as m5dflg 

    /*,sum(IF(Special<=$special,total,0)) as c_total
    ,sum(sum(IF(Special<=$special,total,0))) over(PARTITION BY daikoumoku) as d_total 
    ,sum(IF(Special<=$special,zennennkei,0)) / 4 as c_average
    ,(sum(sum(IF(Special<=$special,zennennkei,0))) over(PARTITION BY daikoumoku)) / 4 as d_average */
    from (
      SELECT
        uid,
        daikoumoku,
        chuukoumoku,
        SUM(CASE WHEN getudo BETWEEN :baseYM9 AND :baseYM10 THEN kin ELSE 0 END) AS m5,
        SUM(CASE WHEN getudo BETWEEN :baseYM7 AND :baseYM8 THEN kin ELSE 0 END) AS m4,
        SUM(CASE WHEN getudo BETWEEN :baseYM5 AND :baseYM6 THEN kin ELSE 0 END) AS m3,
        SUM(CASE WHEN getudo BETWEEN :baseYM3 AND :baseYM4 THEN kin ELSE 0 END) AS m2,
        SUM(CASE WHEN getudo BETWEEN :baseYM1 AND :baseYM2 THEN kin ELSE 0 END) AS m1,
        SUM(CASE WHEN getudo BETWEEN :baseYM11 AND :baseYM12 THEN kin ELSE 0 END) AS zennennkei,
        SUM(kin) AS total,
        Special
      FROM
        kakeibo
      WHERE
        (uid = :uid1 AND getudo BETWEEN :YMFROM AND :YMTO) 
      GROUP BY
        uid,
        daikoumoku,
        chuukoumoku,
        Special
    ) as temp
    left join daikoumoku_ms as ms
    on temp.daikoumoku=ms.daikoumoku
    and temp.uid = ms.uid
    group by COALESCE(ms.sort,999),temp.daikoumoku,temp.chuukoumoku 
    having temp.daikoumoku <> '') AS tmp
    order by sort,chuukoumoku 
    ";
    /*
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,0 as m3,0 as m2,kin as m1, Special
      from kakeibo
      where uid = :uid1 and getudo between :baseYM1 and :baseYM2
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,0 as m3,kin as m2,0 as m1, Special
      from kakeibo
      where uid = :uid2 and getudo between :baseYM3 and :baseYM4
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,0 as m4,kin as m3,0 as m2,0 as m1, Special
      from kakeibo
      where uid = :uid3 and getudo between :baseYM5 and :baseYM6
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,0 as m5,kin as m4,0 as m3,0 as m2,0 as m1, Special
      from kakeibo
      where uid = :uid4 and getudo between :baseYM7 and :baseYM8
      UNION ALL 
      select uid,daikoumoku,chuukoumoku ,kin as m5,0 as m4,0 as m3,0 as m2,0 as m1, Special
      from kakeibo
      where uid = :uid5 and getudo between :baseYM9 and :baseYM10
    */
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid1", $_SESSION["uid"], PDO::PARAM_STR);
	/*$stmt->bindValue("uid2", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid3", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid4", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->bindValue("uid5", $_SESSION["uid"], PDO::PARAM_STR);*/
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
  
  $stmt->bindValue("baseYM11",  date("Ym", strtotime($_GET["fm"]."01"." -59 month")), PDO::PARAM_STR);
  $stmt->bindValue("baseYM12",  date("Ym", strtotime($_GET["fm"]."01"." -12 month")), PDO::PARAM_STR);
  $stmt->bindValue("YMFROM",  date("Ym", strtotime($_GET["fm"]."01"." -59 month")), PDO::PARAM_STR);
  $stmt->bindValue("YMTO",  $_GET["fm"], PDO::PARAM_STR);
  //$stmt->bindValue("special",  $_GET["special"], PDO::PARAM_INT);
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
