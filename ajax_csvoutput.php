<?php
  require "php_header.php";
  $return_satas = "success";
  //log_writer("\$POST",$_POST);

  $dataset = $_POST["csv"];
  $start = $_POST["start"];
  $end = $_POST["end"];

  try{
    /*
    $fp = fopen('./money_forward['.$start.'～'.$end.'].csv', 'w');
    fputcsv($fp, ["日付","明細","金額","出費元","大項目","中項目","メモ"]);
    // foreach文の繰り返し処理で連想配列を書き込む
    foreach($dataset as $value){
      fputcsv($fp, $value);
    }
     
    // ファイルを閉じる
    fclose($hp);
  
    // ダウンロードするサーバのファイルパス
    $filepath = './money_forward['.$start.'～'.$end.'].csv';
    
    // HTTPヘッダを設定
    header('Content-Type: application/octet-stream');
    header('Content-Length: '.filesize($filepath));
    header('Content-Disposition: attachment; filename=download.csv');
    
    // ファイル出力
    readfile($filepath);
    */
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
