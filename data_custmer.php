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
    <TITLE>家計簿</TITLE>
</head>
<BODY id = 'body' style='background:black;' >
  <div id='app'>
    <HEADER class='text-center' style='color:#FFA400'>
        <h1>KAKEI</h1>
    </HEADER>
    <MAIN class='container' style='color:#fff;'>
      <table class="table table-striped table-hover table-sm">
      <thead class='sticky-top'>
        <tr>
          <th scope="col">#</th>
          <th scope="col">日付</th>
          <th scope="col">明細</th>
          <th scope="col">金額</th>
          <th scope="col">出費元</th>
          <th scope="col">大項目</th>
          <th scope="col">中項目</th>
          <th scope="col">メモ</th>
        </tr>
        <tr>
          <td></td>
          <td><input class="form-control" type="date" placeholder="フィルタ"></td>
          <td><input class="form-control" type="text" placeholder="フィルタ"></td>
          <td><input class="form-control" type="number" placeholder="フィルタ"></td>
          <td><input class="form-control" type="text" placeholder="フィルタ"></td>
          <td><input class="form-control" type="text" placeholder="フィルタ"></td>
          <td><input class="form-control" type="text" placeholder="フィルタ"></td>
          <td><input class="form-control" type="text" placeholder="フィルタ"></td>
        </tr>
      </thead>
      <tbody>
        <template v-for='(list,index) in readdata' :key="list.No">
          <tr>
          <td>{{list.No}}</td>
          <td><input v-model='list[0]' class="form-control" type="date" placeholder=""></td>
          <td><input v-model='list[2]' class="form-control" type="text" placeholder=""></td>
          <td><input v-model='list[3]' class="form-control" type="number" placeholder=""></td>
          <td><input v-model='list[4]' class="form-control" type="text" placeholder=""></td>
          <td><input v-model='list[5]' class="form-control" type="text" placeholder=""></td>
          <td><input v-model='list[6]' class="form-control" type="text" placeholder=""></td>
          <td><input v-model='list[7]' class="form-control" type="text" placeholder=""></td>
          </tr>
        </template>
      </tbody>
      </table>
    </MAIN>
    <FOOTER class='text-center'>
      <input type="file" class="" name="user_file_name" style='width:25%;' id='file'>
      <button class='btn btn-primary' type='button' @click='uploadfile'>ファイル読込</button>
      <button class='btn btn-primary' type='button'>システム登録</button>
      <button class='btn btn-primary' type='button'>CSV出力</button>
    </FOOTER>
  </div>
    <script>
      const { createApp, ref, onMounted, computed } = Vue;
      createApp({
        setup() {
          const readdata = ref([''])
          const readfilename = ref('')
          const read_html_moneyforward = () => {//アップロード後の分類等未設定の動画一覧を取得
            axios
            .get(`ajax_read_forward.php?lv=${readfilename.value}`)
            .then((response) => {
              readdata.value = [...response.data],
              console_log('read_html_moneyforward succsess')
              //console_log(files.value)
            })
            .catch((error) => console.log(error));
          }
          const uploadfile = () =>{
            const file = document.getElementById('file').files[0];
            const params = new FormData();
            params.append('user_file_name', file);
            axios.post("ajax_loader.php",params, {headers: {'Content-Type': 'multipart/form-data'}})
            .then((response)=>{
              console_log(response.data)
              read_html_moneyforward()
            })
            .catch((response)=>{
              console_log(response.data)
            })
            console_log(file)
          }

          onMounted(()=>{
            read_html_moneyforward()
          })

          return{
            readdata,
            readfilename,
            uploadfile,
          }
        }
      }).mount('#app');
    </script>
</BODY>
</html>