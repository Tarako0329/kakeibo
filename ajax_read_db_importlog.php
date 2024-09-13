<?php
  require "php_header.php";

  //log_writer('\$_SESSION["uid"]',++$a);
  log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);
  $sql = "select *
    from import_log
    where id = :uid
    order by ins_datetime desc LIMIT 24";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
	$stmt->execute();
	$import_log = $stmt->fetchAll(PDO::FETCH_ASSOC);
  log_writer('\$import_log',$import_log);

  $return = array(
    "import_log" => $import_log
  );

  header('Content-type: application/json');  
  echo json_encode($return, JSON_UNESCAPED_UNICODE);
  exit();
?>
