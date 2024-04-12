<?php
  require "php_header.php";
  $return_satas = "success";
  //log_writer("\$POST",$_POST);

  //$dataset = $_POST["csv"];
  $dataset = json_decode($_POST["csv"], true);
  $start = $_POST["start"];
  $end = $_POST["end"];

  try{
    $filename = 'money_forward['.$start.'～'.$end.'].csv';
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=".$filename);

    echo '"日付","明細","金額","出費元","大項目","中項目","メモ"'."\r\n";
    foreach($dataset as $value){
      echo $value["date"].",".$value["meisai"].",".$value["kin"].",".$value["shuppimoto"].",".$value["daikou"].",".$value["chuukou"].",".$value["memo"]."\r\n";
    }
  
  }catch(Exception $e){
    log_writer("Exception \$e",$e);
  }


?>
