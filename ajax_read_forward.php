<?php
require "php_header.php";
if(empty($_GET["fn"])){
	exit;
}else{
	$filepass = $_GET["fn"];
}
log_writer("\$filepass",$filepass);


$type="";
if(substr($filepass,-3)==="csv"){
	$type="csv";
	$contents = fopen($filepass, 'r');
	//log_writer("\$contents csv",$contents);
	$i=0;
	$j=0;
	while($content = fgetcsv($contents)){
		if($i===0){
			$i=$i+1;
			continue;
		}
		//log_writer("\$content =>",mb_convert_encoding($content, "UTF-8", "SJIS"));
		//log_writer("\$content 文字CD =>",mb_detect_encoding($content[4],['JIS','SJIS','ASCII', 'UTF-8'], false));
		//$data[] = mb_convert_encoding($content, "UTF-8", "SJIS");
		/*
		if(mb_detect_encoding($content[4])==="SJIS"){
			$data[] = array(
				"date" => mb_convert_encoding($content[1], "UTF-8", "SJIS")
				,"meisai" => mb_convert_encoding($content[2], "UTF-8", "SJIS")
				,"kin" => mb_convert_encoding($content[3], "UTF-8", "SJIS")
				,"shuppimoto" => mb_convert_encoding($content[4], "UTF-8", "SJIS")
				,"daikoumoku" => mb_convert_encoding($content[5], "UTF-8", "SJIS")
				,"chuukoumoku" => mb_convert_encoding($content[6], "UTF-8", "SJIS")
				,"memo" => mb_convert_encoding($content[7], "UTF-8", "SJIS")
				,"No" => $j
			);
		}else{
			$data[] = array(
				"date" => $content[1]
				,"meisai" => $content[2]
				,"kin" => $content[3]
				,"shuppimoto" => $content[4]
				,"daikoumoku" => $content[5]
				,"chuukoumoku" => $content[6]
				,"memo" => $content[7]
				,"No" => $j
			);
		}
		*/
		$basecd = mb_detect_encoding($content[4],['JIS','SJIS','ASCII', 'UTF-8'], false);
		$data[] = array(
			"date" => mb_convert_encoding($content[1], "UTF-8", $basecd)
			,"meisai" => mb_convert_encoding($content[2], "UTF-8", $basecd)
			,"kin" => mb_convert_encoding($content[3], "UTF-8", $basecd)
			,"shuppimoto" => mb_convert_encoding($content[4], "UTF-8", $basecd)
			,"daikoumoku" => mb_convert_encoding($content[5], "UTF-8", $basecd)
			,"chuukoumoku" => mb_convert_encoding($content[6], "UTF-8", $basecd)
			,"memo" => mb_convert_encoding($content[7], "UTF-8", $basecd)
			,"No" => $j
		);

		$data[$j]["date"] = str_replace("/", '-', $data[$j]["date"]);
		$j=$j+1;
	}
	
}else if(substr($filepass,-4)==="html"){
	$type="html";
	$contents = file($filepass);
	$write = false;
	$next=false;//次行の書き出し
	$create=false;
	$x = 0;
	$rcount = 1;
	$system = "";
	$row = null;
	$data = null;
	foreach ($contents as $number => $content) {
		//$fileNumber = $number + 1;
		
		$content = str_replace(["\r","\n","\r\n"], '', $content);
		//対象システムの判定
		if($system <> ""){
			//no action
		}else if(preg_match( '/moneyforward/', $content)){
			$system = "moneyforward";
		}else if(preg_match( '/zaim/', $content)){
			$system = "zaim";
		}
		//log_writer("\$system",$system);
		//
		if($system === "moneyforward"){
			if($content==='<tbody class="list_body">'){//明細エリア開始
				$content="start";
				$create=true;
				continue;
				//$write=true;
			}else if($content==='<h3 class="heading-normal">家計簿データの出力（Excel、CSV形式対応）</h3>'){//明細エリア終了
				$content = "end";
				$create=false;
				break;
				$write=true;
			}else if(preg_match( '/^<span>[0-9]*/', $content) ){//日付（全角曜日）
				$write=true;

				//log_writer("\$x ",$x);
				//log_writer("\$content ",$content);
			}else if(preg_match( '/^<td class="date" data-table-sortable-value="*/', $content) && $x===0){// 日付：yyyy/mm/dd（カードなどの自動計上）
				$content = substr($content,44,10);
				$content = str_replace(["/"], '-', $content);
				$write=true;
			}else if(preg_match( '/^<td class="date form-switch-td" data-table-sortable-value="*/', $content) && $x===0){// 日付：yyyy/mm/dd（手入力）
				$content = substr($content,59,10);
				$content = str_replace(["/"], '-', $content);
				$write=true;
			}else if(substr($content,0,21)==='<span class="offset">'  && $x===3){//金額
				$write=true;
			}else if(substr($content,0,21)==='<td class="note calc"'  && $x===4){//出費元
				$write=true;
			}else if(substr($content,0,72)==='<a class="btn btn-small dropdown-toggle v_l_ctg" data-toggle="dropdown">' && $x===5){//大項目
				$next = true;
			}else if(substr($content,0,83)==='<a class="btn btn-small dropdown-toggle v_l_ctg btn-danger" data-toggle="dropdown">' && $x===5){//大項目(未分類)
				$next = true;
			}else if(substr($content,0,72)==='<a class="btn btn-small dropdown-toggle v_m_ctg" data-toggle="dropdown">' && $x===6){//中項目
				$next = true;
			}else if(substr($content,0,20)==='<div class="offset">' && $x===4){//振替
				$content = "振替";
				$write=true;
			}else if(substr($content,0,49)==='<td class="calc" style="text-align: left;" title=' && $x===5){//振替元
				$next = true;
			}else if(substr($content,0,34)==='<div class="transfer_account_box">' && $x===6){//振替先
				$write=true;
				//$x=7;
			}else if($next===true){
				$write=true;
				$next=false;
			}
			//二次元配列の作成
			if($write===true && $create===true){
				$row[] = tagClear($content);
	
				if($x>=7){
					//log_writer("\$row html",$row);
					if($row[4]==="振替"){
						log_writer("\$row html",$row);
						$data[] = array(
							"date" => $row[0]
							,"meisai" => $row[2]
							,"kin" => $row[3]
							,"shuppimoto" => $row[5]
							,"daikoumoku" => ""
							,"chuukoumoku" => ""
							,"memo" => "振替"
							,"No" => $rcount
						);
						$rcount = $rcount + 1;
						$data[] = array(
							"date" => $row[0]
							,"meisai" => $row[2]
							,"kin" => $row[3] * (-1)
							,"shuppimoto" => $row[6]
							,"daikoumoku" => ""
							,"chuukoumoku" => ""
							,"memo" => "振替"
							,"No" => $rcount
						);
					}else{
						$data[] = array(
							"date" => $row[0]
							,"meisai" => $row[2]
							,"kin" => $row[3]
							,"shuppimoto" => $row[4]
							,"daikoumoku" => $row[5]
							,"chuukoumoku" => $row[6]
							,"memo" => $row[7]
							,"No" => $rcount
						);
	
					}
					
					$row=[];
					$rcount = $rcount + 1;
				}
				$x = ($x>=7)?0:$x+1;
			}
		
			$write = false;
		}else if($system === "zaim"){
			if(preg_match( '/<div id="root">/', $content)){
				
				$content = strstr($content,'https://zaim.net/money?month=');
				$content = strstr($content,'=');
				$Ybefore = substr($content,1,4);
				if(substr($content,5,2)==="12"){
					$Y = $Ybefore + 1;
				}else{
					$Y = $Ybefore;
				}
				//log_writer("\$Y",$Y);
				//log_writer("\$Ybefore",$Ybefore);

				$content = strstr($content,'<div class="SearchResult-module__list_');
				while(true){
					//データUNIQキー取得
					$content = strstr($content,'money');
					$id = strstr(substr($content,6),'edit',true);
					$row["id"] = $id;
					//log_writer("\$id",$id);

					//日付の検索(日付クラスから閉じタグ２個)
					$content = strstr($content,"SearchResult-module__date");
					$content = strstr($content,'">');
					$content = strstr(substr($content,2),'">');
					
					$ymd = strstr(substr($content,2),"<",true); //12月15日（月）
					$m = substr("0".strstr($ymd,"月",true),-2);
					$d = substr("0".strstr(substr(strstr($ymd,"月"),3),"日",true),-2);
					
					if($m==12){
						$row["date"] = $Ybefore."-".$m."-".$d;
					}else{
						$row["date"] = $Y."-".$m."-".$d;
					}
					
					//log_writer("\$ymd",$ymd);
					//log_writer("\$m",$m);
					//log_writer("\$d",$d);
					
					//$row[] = strstr(substr($content,2),"<",true);
					//大カテゴリ
					$content = strstr($content,"SearchResult-module__category");
					$content = strstr($content,'data-title="');
					$row["daikoumoku"] = strstr(substr($content,12),'" ',true);
					//中カテゴリ
					$content = strstr($content,'edit">');
					$row["chuukoumoku"] = strstr(substr($content,6),'<',true);
					//金額 SearchResult-module__fromAccount
					$content = strstr($content,'edit">¥');
					$row["kin"] = str_replace(",","",(strstr(substr($content,8),'<',true)));
					//出金元
					$content = strstr($content,'SearchResult-module__fromAccount');
					$content = substr($content,43);
					if(substr($content,0,4)==="span"){
						$content = strstr($content,'data-title="');
						$content = strstr($content,'"');
						$row["shuppimoto"] = strstr(substr($content,1),'"',true);
						$row["kin"] = $row["kin"] * (-1);
					}else{
						//$row[] = "-";
					}
					//入金元
					$content = strstr($content,'SearchResult-module__toAccount');
					$content = substr($content,41);
					if(substr($content,0,4)==="span"){
						$content = strstr($content,'data-title="');
						$content = strstr($content,'"');
						$row["shuppimoto"] = strstr(substr($content,1),'"',true);
					}else{
						//$row[] = "-";
					}
					//お店
					$content = strstr($content,'edit">');
					$row["meisai"] = strstr(substr($content,6),'<',true);
					$content = strstr($content,'<');
					//品目
					$content = strstr($content,'edit">');
					$row["memo"] = strstr(substr($content,6),'<',true);
					$content = strstr($content,'<');
					//メモ
					$content = strstr($content,'edit">');
					$row["memo"] .= strstr(substr($content,6),'<',true);
					$content = strstr($content,'<');

					$row["NO"] = $rcount;
					$pdo_h->beginTransaction();
					$sql = "insert IGNORE into zaim_work(uid,id,date,meisai,kin,shuppimoto,daikoumoku,chuukoumoku,memo,NO) values(:uid,:id,:date,:meisai,:kin,:shuppimoto,:daikoumoku,:chuukoumoku,:memo,:NO)";
					$stmt = $pdo_h->prepare($sql);
					$stmt->bindValue("uid", $_SESSION["uid"], PDO::PARAM_STR);
					$stmt->bindValue("id", $row["id"], PDO::PARAM_STR);
					$stmt->bindValue("date", $row["date"], PDO::PARAM_STR);
					$stmt->bindValue("meisai", $row["meisai"], PDO::PARAM_STR);
					$stmt->bindValue("kin", $row["kin"], PDO::PARAM_STR);
					$stmt->bindValue("shuppimoto", $row["shuppimoto"], PDO::PARAM_STR);
					$stmt->bindValue("daikoumoku", $row["daikoumoku"], PDO::PARAM_STR);
					$stmt->bindValue("chuukoumoku", $row["chuukoumoku"], PDO::PARAM_STR);
					$stmt->bindValue("memo", $row["memo"], PDO::PARAM_STR);
					$stmt->bindValue("NO", $row["NO"], PDO::PARAM_STR);
					$stmt->execute();
					$pdo_h->commit();

					/*$data[] = array(
						"id" => $row[0]
						,"date" => $row[1]
						,"meisai" => $row[6]
						,"kin" => $row[4]
						,"shuppimoto" => $row[5]
						,"daikoumoku" => $row[2]
						,"chuukoumoku" => $row[3]
						,"memo" => $row[7].$row[8]
						,"No" => $rcount
					);*/

					//$data[]=$row;
					
					//log_writer("\$row",$row);
					//log_writer("\$data",$data);
					
					$row = [];
					//EOFチェック
					$content = strstr($content,'</span>');
					//log_writer("\$content",substr($content,0,81));
					if(substr($content,0,82)==='</span></div></div></div></div></div><table class="SearchResultTable-module__table'){
						break;
					}
					$rcount = $rcount + 1;
					/*
					if($rcount > 300){
						break;
					}
					
					break;
					*/
				}
				$stmt = $pdo_h->prepare("select * from zaim_work order by date desc");
				$stmt->execute();
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			}
		}
	}
	log_writer("\$data",$data);
}else{

}
$return = array(
	"data" => $data
	,"type" => $type
);
//log_writer("\$data",$data);

//jsonとして出力
header('Content-type: application/json');
echo json_encode($return, JSON_UNESCAPED_UNICODE);


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