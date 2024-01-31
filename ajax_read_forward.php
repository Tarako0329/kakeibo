<?php
require "functions.php";
$contents = file("upload/マネーフォワード ME.html");
$write = false;
$next=false;//次行の書き出し
$create=false;
$x = 0;
$rcount = 1;
foreach ($contents as $number => $content) {
    $fileNumber = $number + 1;
    $content = str_replace(["\r","\n","\r\n"], '', $content);
    //
    if($content==='<tbody class="list_body">'){
      $content="start";
      $create=true;
      continue;
      $write=true;
    }else if($content==='<h3 class="heading-normal">家計簿データの出力（Excel、CSV形式対応）</h3>'){
      $content = "end";
      $create=false;
      break;
      $write=true;
    }else if(preg_match( '/^<span>[0-9]*/', $content)){
      $write=true;
    }else if(preg_match( '/^<td class="date" data-table-sortable-value="*/', $content)){
      $content = substr($content,44,10);// 日付：yyyy/mm/dd（カードなどの自動計上）
      $content = str_replace(["/"], '-', $content);
      $write=true;
    }else if(preg_match( '/^<td class="date form-switch-td" data-table-sortable-value="*/', $content)){
      $content = substr($content,59,10);// 日付：yyyy/mm/dd（手入力）
      $content = str_replace(["/"], '-', $content);
      $write=true;
    }else if(substr($content,0,21)==='<span class="offset">'){
      $write=true;//金額
    }else if(substr($content,0,21)==='<td class="note calc"'){
      $write=true;//出費元
    }else if(substr($content,0,72)==='<a class="btn btn-small dropdown-toggle v_l_ctg" data-toggle="dropdown">'){
      $next = true;//大項目
    }else if(substr($content,0,72)==='<a class="btn btn-small dropdown-toggle v_m_ctg" data-toggle="dropdown">'){
      $next = true;//中項目
    }else if(substr($content,0,20)==='<div class="offset">'){
      $content = "振替";
      $write=true;
    }else if(substr($content,0,49)==='<td class="calc" style="text-align: left;" title='){
      $next = true;//振替元
    }else if(substr($content,0,34)==='<div class="transfer_account_box">'){
      $write=true;//振替先
    }else if($next===true){
      $write=true;
      $next=false;
    }
    
    //二次元配列の作成
    if($write===true && $create===true){
      $row[] = tagClear($content);

      if($x>=7){
        $row["No"] = $rcount;
        $data[] = $row;
        $row=[];
        $rcount = $rcount + 1;
      }
      $x = ($x>=7)?0:$x+1;
    }

    $write = false;
}
//log_writer("",$data);

//jsonとして出力
header('Content-type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);


function tagClear($str){
  $str = substr($str,strpos($str,">"));

  if(strpos($str,"<")<>false){
    $str = substr($str,1,strpos($str,"<")-1);
  }
  $str = str_replace(["<",">",","], '', $str);
  //log_writer("",$str.",");
  //echo $str."<br>"

  return $str;
}
?>