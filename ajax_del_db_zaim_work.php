<?php
	require "php_header.php";
	/*if(empty($_GET)){
		exit();
	}*/
	//log_writer('\$_SESSION["uid"]',++$a);
	log_writer('\$_SESSION["uid"]',$_SESSION["uid"]);

	$stmt = $pdo_h->prepare("delete from zaim_work where uid = :uid");
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
