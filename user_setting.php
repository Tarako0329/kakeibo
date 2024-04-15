<?php
  require "php_header.php";
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
    <style>

    </style>
    
</head>
<BODY id = 'body' style='background:black;' >
  <div id='app' style='height:100%;'>
    <HEADER style='color:#FFA400;padding-top:0;'>
      <div class='container-fluid'>
        <div class='row'>
          <div class='col-12'>
            <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
              <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><h3 class='potta-one-regular' style='color:#FFA400;'><?php echo $title;?></h3></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class='nav-item'>
								    	<a class='nav-link' href='data_custmer.php?m=imp'>データ取込</a>
								    </li>
								    <li class='nav-item'>
								    	<a class='nav-link' href='index.php'>入出金</a>
								    </li>
                    <li class='nav-item'>
    									<a class='nav-link' href='data_comparison.php'>前月比・前年比</a>
		    						</li>
								    <li class='nav-item'>
								    	<a class='nav-link' href='data_summary12m.php'>推移(月間・年間)</a>
								    </li>
                  </ul>
                  <div class="d-flex" style='padding:5px;'>
                  </div>
                  <div class="d-flex" style='padding:5px;'>
                    <a href="user_setting.php" class='a_none'><h2><i class="bi bi-person-circle" style='color:#d9ff00bd'></i></h2></a>
                  </div>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </HEADER>
    <MAIN class='container' style='color:#fff;padding-left:20px;padding-right:20px;'>
      <div class='row mt-5'>
        <div class='col-lg-6 col-xl-5'>
          <label for='kisanbi' class='form-label'>月度起算日</label>
          <input type='number' id='kisanbi' class='form-control' v-model='kisanbi'>
        </div>
      </div>
      <div class='row mt-5'>
        <div class='col-lg-6 col-xl-5'>
          <label for='shukuzitu' class='form-label'>月度起算日が土日祝の場合</label>
          <select id='shukuzitu' class='form-select' v-model='shukuzitu'>
            <option value="0">無視する</option>
            <option value="1">直前の平日を起算日とする</option>
            <option value="2">直後の平日を起算日とする</option>
          </select>
        </div>
      </div>
      <div class='row mt-5'>
        <div class='col-lg-6 col-xl-5'>
          <label for='nendomatu' class='form-label'>年度末月</label>
          <input type='number' id='nendomatu' class='form-control' v-model='nendomatu'>
        </div>
      </div>
      <div class='row mt-5'>
        <div class='col-lg-6 col-xl-5'>
          <button type='button' class='btn btn-outline-primary' @click='onsubmit'>登　録</button>
        </div>
      </div>
      <div class='logoff'><a href="logoff.php" class='a_none'><h1><i class="bi bi-box-arrow-right"></i></h1></a></div>
    </MAIN>
  </div>
  
  <script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
  <script>
    user_setting().mount('#app');
  </script>
</BODY>
</html>