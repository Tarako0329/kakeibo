<?php
  require "php_header.php";
  if(empty($_GET)){
    //exit();
  }
  //log_writer('\$_SESSION["uid"]',++$a);
  $sql = "select getudo,concat(left(getudo,4),'-',right(getudo,2)) as getudo2,left(getudo,4) as nendo
    from kakeibo as kake 
    where kake.uid = :uid 
    group by uid,getudo,left(getudo,4) ,concat(left(getudo,4),'-',right(getudo,2)) 
    order by getudo desc";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql = "select left(getudo,4) as nendo
    from kakeibo as kake 
    where kake.uid = :uid 
    group by uid,left(getudo,4)
    order by getudo desc";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql = "select 
    max(getudo) as max_getudo,max(concat(left(getudo,4),'-',right(getudo,2))) as max_getudo2,max(left(getudo,4)) as max_nendo 
    ,min(getudo) as min_getudo,min(concat(left(getudo,4),'-',right(getudo,2))) as min_getudo2,min(left(getudo,4)) as min_nendo 
    from kakeibo as kake 
    where kake.uid = :uid 
    group by uid";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //log_writer("",$return);

  $return = array(
    "ymlist" => $dataset,
    "ylist" => $dataset2,
    "max_min" => $dataset3
  );

  header('Content-type: application/json');
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
