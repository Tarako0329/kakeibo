<?php
  require "php_header.php";
  /*if(empty($_GET)){
    exit();
  }*/
  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $sql = "select *
    from user
    where uid = :uid";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$dataset = $stmt->fetchAll(PDO::FETCH_ASSOC);


  $return = array(
    "user" => $dataset,
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
