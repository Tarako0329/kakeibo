<?php
  require "php_header.php";
  if(empty($_GET)){
    //一般公開・マネーフォワードCSV変換モード
    $title="MFoward";
    $mode = "ippan";
  }else if($_GET["m"]==="imp"){
    //データインポートモード
    $title="MFoward";
    $mode = "import";
  }else{
    $mode = "ippan";
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
    <style>
      button{
        width: 95%;
      }
    </style>
</head>
<BODY id = 'body' style='background:black;' >
  <div id='app' style='height:100%;'>
    <HEADER class=' row' style='color:#FFA400;'>
        <div class='col-4 text-center'><a href='index.php'><h3><?php echo $title;?></h3></a></div>
        <div class='col-1'>
          <input v-model='from' :disabled='search_disable' @change='read_db_comparison' type="number" class='form-control form-control-sm' placeholder="期間FROM YYYYMM">
        </div>
        <div class='col-1'>
          <!--<input v-model='to' :disabled='search_disable' @change='read_db_meisai' type="number" class='form-control form-control-sm' placeholder="期間TO YYYYMM">-->
          <select v-model='to' @change='read_db_comparison' class="form-select form-select-sm">
            <option value = "m">前月比</option>
            <option value = "y">前年比</option>
          </select>
        </div>

    </HEADER>
    
    <MAIN class='container-fluid' style='color:#fff;padding-left:20px;padding-right:20px;'>
    <div class='row' style='height:100%;'>
      <div class='col-xl-4'>
        <div class="table-responsive" style='width:100%;height:800px;'>
          <table class="table table-hover table-sm">
            <thead class='sticky-top'>
              <tr class="table-info">
                <th scope="col">大中項目</th>
                <th v-if='to==="m"' scope="col">前月金額</th>
                <th v-else-if='to==="y"' scope="col">前年金額</th>
                <th v-else></th>
                <th scope="col">[{{from}}]金額</th>
                <th scope="col">増減額</th>
              </tr>
            </thead>
            <tbody>
              <template v-for='(list,index) in readdata_summary' :key="list.sort+list.chuukoumoku">
                <template v-if='index===0 || (index!==0 && list["daikoumoku"]!==readdata_summary[index -1]["daikoumoku"])'>
                  <tr class="table-info" roll='button' @click='setfilter(list["daikoumoku"],"")'>
                    <td>{{list["daikoumoku"]}}</td>
                    <td class='text-end'>{{Number(list.hikaku_daikei).toLocaleString()}}</td>
                    <td class='text-end'>{{Number(list.moto_daikei).toLocaleString()}}</td>
                    <td v-if='Number(list.moto_daikei) > Number(list.hikaku_daikei)' style='color:blue;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.hikaku_daikei)).toLocaleString()}}</td>
                    <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_daikei) - Number(list.hikaku_daikei)).toLocaleString()}}</td>
                  </tr>
                  <tr roll='button' @click='setfilter(list["daikoumoku"],list["chuukoumoku"])'>
                    <td>　{{list["chuukoumoku"]}}</td>
                    <td class='text-end'>{{Number(list.hikaku_chuukei).toLocaleString()}}</td>
                    <td class='text-end'>{{Number(list.moto_chuukei).toLocaleString()}}</td>
                    <td v-if='Number(list.moto_chuukei) > Number(list.hikaku_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.hikaku_chuukei)).toLocaleString()}}</td>
                    <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.hikaku_chuukei)).toLocaleString()}}</td>
                  </tr>
                </template>
                <template v-if='index!==0 && list["daikoumoku"]===readdata_summary[index -1]["daikoumoku"]'>
                  <tr roll='button' @click='setfilter(list["daikoumoku"],list["chuukoumoku"])'>
                    <td>　{{list["chuukoumoku"]}}</td>
                    <td class='text-end'>{{Number(list.hikaku_chuukei).toLocaleString()}}</td>
                    <td class='text-end'>{{Number(list.moto_chuukei).toLocaleString()}}</td>
                    <td v-if='Number(list.moto_chuukei) > Number(list.hikaku_chuukei)' style='color:blue;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.hikaku_chuukei)).toLocaleString()}}</td>
                    <td v-else style='color:red;' class='text-end'>{{(Number(list.moto_chuukei) - Number(list.hikaku_chuukei)).toLocaleString()}}</td>
                  </tr>
                </template>
              </template>
            </tbody>
            <tfoot class='sticky-bottom'>
              <tr class='table-success'>
                <td class='text-center'>入出金計</td>
                <td class='text-end'>{{Number(comparison_sum_val["zen"]).toLocaleString()}}</td>
                <td class='text-end'>{{Number(comparison_sum_val["tou"]).toLocaleString()}}</td>
                <td class='text-end'>{{Number(comparison_sum_val["sa"]).toLocaleString()}}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class='col-xl-8'>
      </div>
    </div>
    </MAIN>
  </div>
  
  <script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
  <script>
    dataset('<?php echo $mode;?>').mount('#app');
  </script>
</BODY>
</html>