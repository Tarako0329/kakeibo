<?php
  require "php_header.php";
  if(empty($_GET)){
    //exit();
  }
  log_writer("\$_GET",$_GET);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $sql = "SELECT * from daikoumoku_ms
    where uid = :uid order by sort";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $return = array(
    "daikou_ms" => $dataset2,
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
