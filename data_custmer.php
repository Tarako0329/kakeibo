<?php
  require "php_header.php";
  if(empty($_GET)){
    //一般公開・マネーフォワードCSV変換モード
    $title="MoneyFoward csv convert";
    $mode = "ippan";
  }else if($_GET["m"]==="imp"){
    //データインポートモード
    $title="IMPORT";
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
</head>
<BODY id = 'body' style='background:black;' >
  <div id='app' style='height:100%;'>
    <HEADER class='text-center' style='color:#FFA400'>
      <a href='index.php'><h1><?php echo $title;?></h1></a>
    </HEADER>
    <!--<MAIN class='container-fluid' style='color:#fff;'> class="table-responsive" -->
    <MAIN class='container table-responsive' style='color:#fff;'>
    <div class="table-responsive table-h-full" style='width:100%;'>
      <table class="table table-striped table-hover table-sm">
      <thead class='sticky-top'>
        <tr>
          <th scope="col">#</th>
          <th scope="col">日付</th>
          <th scope="col">明細</th>
          <th scope="col">入出金額</th>
          <th scope="col">出費元</th>
          <th scope="col">大項目</th>
          <th scope="col">中項目</th>
          <th scope="col">メモ</th>
        </tr>
        <tr>
          <td></td>
          <td>
            <select v-model='fl_date' class="form-select form-select-sm input_date" placeholder="フィルタ">
              <option value=''>ﾌｨﾙﾀ解除</option>
              <template v-for='(list,index) in fl_date_lst' :key='list'>
                <option :value='list'>{{list}}</option>
              </template>
            </select>
          </td>
          <td>
            <input v-model='fl_meisai' type="text" class='form-control form-control-sm' placeholder="フィルタ">
          </td>
          <td><input v-model="fl_kin" class="form-control form-control-sm" type="number" placeholder="フィルタ"></td>
          <td>
            <select v-model='fl_shuppimoto' class="form-select form-select-sm" placeholder="フィルタ">
              <option value=''>ﾌｨﾙﾀ解除</option>
              <template v-for='(list,index) in fl_shuppimoto_lst' :key='list'>
                <option :value='list'>{{list}}</option>
              </template>
            </select>
          </td>
          <td>
            <select v-model='fl_dai_ko' class="form-select form-select-sm" placeholder="フィルタ">
              <option value=''>ﾌｨﾙﾀ解除</option>
              <template v-for='(list,index) in fl_dai_ko_lst' :key='list'>
                <option :value='list'>{{list}}</option>
              </template>
            </select>
          </td>
          <td>
            <select v-model='fl_chuu_ko' class="form-select form-select-sm" placeholder="フィルタ">
              <option value=''>ﾌｨﾙﾀ解除</option>
              <template v-for='(list,index) in fl_chuu_ko_lst' :key='list'>
                <option :value='list'>{{list}}</option>
              </template>
            </select>
          </td>
          <td>
            <select v-model='fl_memo' class="form-select form-select-sm" placeholder="フィルタ">
              <option value=''>ﾌｨﾙﾀ解除</option>
              <template v-for='(list,index) in fl_memo_lst' :key='list'>
                <option :value='list'>{{list}}</option>
              </template>
            </select>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="date" id='cdate' class='form-control form-control-sm input_date' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm input_date' @click='hanei("cdate")'>↓反映</button>
          </td>
          <td>
            <input type="text" id='cmeisai' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("cmeisai")'>↓反映</button>
          </td>
          <td>
            <input type="number" id='ckin' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("ckin")'>↓反映</button>
          </td>
          <td>
            <input type="text" id='cshuppi' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("cshuppi")'>↓反映</button>
          </td>
          <td>
            <input type="text" id='cdaikou' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("cdaikou")'>↓反映</button>
          </td>
          <td>
            <input type="text" id='cchukou' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("cchukou")'>↓反映</button>
          </td>
          <td>
            <input type="text" id='cmemo' class='form-control form-control-sm' placeholder="一括変更" style='margin-bottom:3px;'>
            <button type='button' class='btn btn-success btn-sm' @click='hanei("cmemo")'>↓反映</button>
          </td>
        </tr>
      </thead>
      <tbody>
        <template v-for='(list,index) in readdata_filter' :key="list.No">
          <tr>
          <td>{{list.No}}</td>
          <td><input v-model='list.date' class="form-control form-control-sm input_date" type="date" placeholder=""></td>
          <td><input v-model='list.meisai' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list.kin' class="form-control form-control-sm text-end" type="number" placeholder=""></td>
          <td><input v-model='list.shuppimoto' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list.daikoumoku' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list.chuukoumoku' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list.memo' class="form-control form-control-sm" type="text" placeholder=""></td>
          </tr>
        </template>
      </tbody>
      <tfoot class='sticky-bottom'>
        <tr class='table-success'>
          <td></td>
          <td></td>
          <td class='text-center'>合計</td>
          <td class='text-end'>{{Number(sum_kingaku).toLocaleString()}}</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tfoot>
      </table>
    </div>
    </MAIN>
    <FOOTER class='text-center'>
      <input type="file" class="" name="user_file_name" style='width:25%;' id='file' @change='uploadfile'>
      <button class='btn btn-primary' type='button' @click='uploadfile'>ファイル読込</button>
      <button v-if='mode!=="ippan"' class='btn btn-primary' type='button' @click='savedata'>システム登録</button>
      <button class='btn btn-primary' type='button' @click='savecsv'>CSV出力</button>
    </FOOTER>
    <div class="loader-wrap" v-show='loader'>
		  <div class="loader">Loading...</div>
	  </div>

  </div>
  <script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
  <script>
    dataset('<?php echo $mode;?>').mount('#app');
  </script>
</BODY>
</html>