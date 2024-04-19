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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
    									<a class='nav-link active' href='data_comparison.php'>前月比・前年比</a>
		    						</li>
								    <li class='nav-item'>
								    	<a class='nav-link' href='data_summary12m.php'>推移(月間・年間)</a>
								    </li>
                  </ul>
                  <div class="d-flex" style='padding:5px;'>
                    <input v-model='from' :disabled='search_disable' @change='read_db_comparison' type="number" class='form-control' placeholder="期間FROM YYYYMM">
                  </div>
                  <div class="d-flex" style='padding:5px;'>
                    <button class='btn btn-outline-primary lbtn' style='margin-right:5px;' @click='from_back'>前月</button>
                    <button class='btn btn-outline-primary rbtn' style='margin-right:50px;' @click='from_next'>次月</button>
                    <a href="user_setting.php" class='a_none'><h2><i class="bi bi-person-circle"></i></h2></a>
                  </div>
                </div>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </HEADER>
    <MAIN class='container' style='color:#fff;padding-left:20px;padding-right:20px;'>
      <div class='row' style='height:100%;'>
        <div class='col-lg-6 col-xl-5' style='height:100%;'>
          <div class="table-responsive table-h-full" style='width:100%;'>
            <table class="table table-hover table-sm table-bordered">
              <thead class='sticky-top'>
                <tr class="table-info">
                  <th class='text-center' scope="col">大中項目</th>
                  <th class='text-center' scope="col">前年金額</th>
                  <th class='text-center' scope="col">前月金額</th>
                  <th class='text-center border-primary'>　</th>
                  <th class='text-center table-primary border-primary' scope="col">{{from}}</th>
                  <th class='text-center'>　</th>
                  <th class='text-center' scope="col">前年比</th>
                  <th class='text-center' scope="col">前月比</th>
                </tr>
              </thead>
              <tbody>
                <template v-for='(list,index) in readdata_summary' :key="list.sort+list.chuukoumoku">
                  <template v-if='index===0 || (index!==0 && list["daikoumoku"]!==readdata_summary[index -1]["daikoumoku"])'>
                    <tr class="table-info">
                      <td>{{list.daikoumoku}}</td>
                      <td class='text-end'>{{Number(list.y_ago_daikei).toLocaleString()}}</td>
                      <td class='text-end'>{{Number(list.m_ago_daikei).toLocaleString()}}</td>
                      <th class='text-center border-primary'>　</th>
                      <td class='text-end table-primary border-primary'>{{Number(list.moto_daikei).toLocaleString()}}</td>
                      <th class='text-center'>　</th>
                      <td v-if='Number(list.moto_daikei) > Number(list.y_ago_daikei)' style='color:blue;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.y_ago_daikei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.y_ago_daikei)).toLocaleString()}}</td>
                      <td v-if='Number(list.moto_daikei) > Number(list.m_ago_daikei)' style='color:blue;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.m_ago_daikei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.m_ago_daikei)).toLocaleString()}}</td>
                    </tr>
                    <tr>
                      <td>　{{list["chuukoumoku"]}}</td>
                      <td class='text-end'>{{Number(list.y_ago_chuukei).toLocaleString()}}</td>
                      <td class='text-end'>{{Number(list.m_ago_chuukei).toLocaleString()}}</td>
                      <th class='text-center border-primary'>　</th>
                      <td class='text-end border-primary'>{{Number(list.moto_chuukei).toLocaleString()}}</td>
                      <th class='text-center'>　</th>
                      <td v-if='Number(list.moto_chuukei) > Number(list.y_ago_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.y_ago_chuukei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.y_ago_chuukei)).toLocaleString()}}</td>
                      <td v-if='Number(list.moto_chuukei) > Number(list.m_ago_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.m_ago_chuukei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.m_ago_chuukei)).toLocaleString()}}</td>
                    </tr>
                  </template>
                  <template v-if='index!==0 && list["daikoumoku"]===readdata_summary[index -1]["daikoumoku"]'>
                    <tr>
                      <td>　{{list["chuukoumoku"]}}</td>
                      <td class='text-end'>{{Number(list.y_ago_chuukei).toLocaleString()}}</td>
                      <td class='text-end'>{{Number(list.m_ago_chuukei).toLocaleString()}}</td>
                      <th class='text-center border-primary'>　</th>
                      <td class='text-end border-primary'>{{Number(list.moto_chuukei).toLocaleString()}}</td>
                      <th class='text-center'>　</th>
                      <td v-if='Number(list.moto_chuukei) > Number(list.y_ago_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.y_ago_chuukei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.y_ago_chuukei)).toLocaleString()}}</td>
                      <td v-if='Number(list.moto_chuukei) > Number(list.m_ago_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.m_ago_chuukei)).toLocaleString()}}</td>
                      <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.m_ago_chuukei)).toLocaleString()}}</td>
                    </tr>
                  </template>
                </template>
              </tbody>
              <tfoot class='sticky-bottom'>
                <tr class='table-success'>
                  <td class='text-center'>入出金計</td>
                  <td class='text-end'>{{Number(comparison_sum_val["zen_y"]).toLocaleString()}}</td>
                  <td class='text-end'>{{Number(comparison_sum_val["zen_m"]).toLocaleString()}}</td>
                  <th class='text-center border-primary'>　</th>
                  <td class='text-end border-primary'>{{Number(comparison_sum_val["tou"]).toLocaleString()}}</td>
                  <th class='text-center'>　</th>
                  <td class='text-end'>{{Number(comparison_sum_val["sa_y"]).toLocaleString()}}</td>
                  <td class='text-end'>{{Number(comparison_sum_val["sa_m"]).toLocaleString()}}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class='col-lg-6 col-xl-7' style='height:100%;'>
          <canvas id="myChart2" style='height:100%;'></canvas>
        </div>
      </div>
      <div class='logoff'><a href="logoff.php" class='a_none'><h1><i class="bi bi-box-arrow-right"></i></h1></a></div>
    </MAIN>
  </div>
  
  <script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
  <script>
    summary_bunseki('<?php echo basename(__FILE__);?>').mount('#app');
  </script>
</BODY>
</html>