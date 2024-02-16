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
	<TITLE><?php echo $title;?></TITLE>
</head>
<BODY id = 'body' style='background:black;' >
	<div id = 'app' style='width:100%;height:100%;'>

	<HEADER style='color:#FFA400;padding-top:0;'>
      <div  class='container'>
      <div class='row'>
        <div class='col-12'>
        <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
          <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><h3 style='color:#FFA400;'><?php echo $title;?></h3></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
								<li class='nav-item'>
									<a class='nav-link' href='data_custmer.php?m=imp'>データ取込</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link' href='data_summary.php'>入出金</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link' href='data_comparison.php'>前月比・前年比</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link' href='data_summary12m.php'>推移(月間・年間)</a>
								</li>
              </ul>
            </div>
          </div>
        </nav>
        </div>
      </div>
      </div>
			<div class='youkoso'><?php echo "ID:".$_SESSION["uid"];?></div>
			<div class='logoff'><a href="logoff.php" class='a_none'>logoff</a></div>
    </HEADER>




	<MAIN class='container' style='color:#fff;padding-top:130px;padding-bottom:10px;'>
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









