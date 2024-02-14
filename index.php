<?php
	require "php_header.php";
	
	if(empty($_SESSION["uid"])){
		//$_SESSION["MSG"]=empty($_SESSION["MSG"])?"ログインIDとパスワードを入力し、新規登録をお願いします。":$_SESSION["MSG"];
	  header("HTTP/1.1 301 Moved Permanently");
	  header("Location: login.php");
	  exit();
	}
	
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
	<?php 
	//共通部分、bootstrap設定、フォントCND、ファビコン等
	include "head_bs5.php" 
	?>
	<script src="./script/flow.js"></script>
	<TITLE>Video Uploader</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
	<div id = 'app' style='width:100%;height:100%;'>
	<HEADER class='text-center' style='color:#FFA400;height:130px;'>
		<h1><?php echo $title;?></h1>
		<div class='youkoso'><?php echo "ID:".$_SESSION["uid"];?></div>
		<div class='logoff'><a href="logoff.php" class='a_none'>logoff</a></div>
	</HEADER>
	<MAIN class='container' style='color:#fff;padding-top:130px;padding-bottom:10px;'>
		<div class='row'>
			<form method='get' action='data_custmer.php'><button type='submit' class='btn btn-outline-primary' name='m' value='imp'>データインポート</button></form>
			<form method='get' action='data_summary.php'><button type='submit' class='btn btn-outline-primary'>入出金明細</button></form>
			<form method='get' action='data_comparison.php'><button type='submit' class='btn btn-outline-primary'>期間推移</button></form>
			<form method='get' action='data_summury12m.php'><button type='submit' class='btn btn-outline-primary'>期間推移2</button></form>
		</div>
	</MAIN>
	<!--<FOOTER>
	</FOOTER>-->
	</div>
	<script>
		const { createApp, ref, onMounted, computed } = Vue;
		createApp({
			setup() {
				const message = ref('clear');
				
				onMounted(() => {
					console.log("onMounted")
				});

				return {
					message,
				};
			}
		}).mount('#app');
	</script><!--vue-->
	<script>
 
	</script>


</BODY>
</html>









