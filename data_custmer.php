<?php
  require "php_header.php";
  if(empty($_GET)){
    //一般公開・マネーフォワードCSV変換モード
    $title="MoneyFoward csv convert";
    $mode = "ippan";
  }else if($_GET["m"]==="imp"){
    //データインポートモード
    $title="MoneyFoward IMPORT";
    $mode = "import";
  }else if($_GET["m"]==="upd"){
    //登録済みデータ更新モード
    $title="データ修正モード";
    $mode = "update";
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
  <div id='app'>
    <HEADER class='text-center' style='color:#FFA400'>
        <h1><?php echo $title;?></h1>
    </HEADER>
    <!--<MAIN class='container-fluid' style='color:#fff;'>-->
    <MAIN class='container' style='color:#fff;'>
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
          <td><input v-model='list[0]' class="form-control form-control-sm input_date" type="date" placeholder=""></td>
          <td><input v-model='list[2]' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list[3]' class="form-control form-control-sm" type="number" placeholder=""></td>
          <td><input v-model='list[4]' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list[5]' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list[6]' class="form-control form-control-sm" type="text" placeholder=""></td>
          <td><input v-model='list[7]' class="form-control form-control-sm" type="text" placeholder=""></td>
          </tr>
        </template>
      </tbody>
      </table>
    </MAIN>
    <FOOTER class='text-center'>
      <input type="file" class="" name="user_file_name" style='width:25%;' id='file'>
      <button class='btn btn-primary' type='button' @click='uploadfile'>ファイル読込</button>
      <button v-if='mode!=="ippan"' class='btn btn-primary' type='button' @click='savedata'>システム登録</button>
      <button class='btn btn-primary' type='button' @click='savecsv'>CSV出力</button>
    </FOOTER>
  </div>
    <script>
      const { createApp, ref, onMounted, computed,watch } = Vue;
      createApp({
        setup() {
          const mode = ref('<?php echo $mode;?>')
          const readdata = ref([])
          const readfilename = ref('')
          const filetype = ref('')
          const read_html_moneyforward = () => {//アップロード後の分類等未設定の動画一覧を取得
            axios
            .get(`ajax_read_forward.php?fn=${readfilename.value}`)
            .then((response) => {
              readdata.value = []
              filetype.value = ''
              console_log(response.data)
              readdata.value = [...response.data.data]
              filetype.value = response.data.type
              console_log('read_html_moneyforward succsess')
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
              if(response.data.status==="success"){
                readfilename.value = response.data.filename
                read_html_moneyforward()
              }else{

              }
              
            })
            .catch((response)=>{
              console_log(response.data)
            })
            console_log(file)
          }
          
          //フィルターワード
            const fl_date       = ref('')
            const fl_meisai     = ref('')
            const fl_kin        = ref('')
            const fl_shuppimoto = ref('')
            const fl_dai_ko     = ref('')
            const fl_chuu_ko    = ref('')
            const fl_memo       = ref('')
          //フィルターのセレクトリスト
            const fl_date_lst   = ref([])
            const fl_meisai_lst = ref([])
            const fl_shuppimoto_lst = ref([])
            const fl_dai_ko_lst     = ref([])
            const fl_chuu_ko_lst    = ref([])
            const fl_memo_lst       = ref([])
          
          const sum_kingaku = ref(0)
          const readdata_filter = computed(() => {
			    	//let searchWord = over_cate.value.toString().trim();
            /*
			    	shouhinMS.value.sort((a,b) => {
			    		return (a.category > b.category?1:-1)
			    		return (a.shouhinNM > b.shouhinNM?1:-1)
			    		return 0
			    	})
            */
            //console_log(readdata.value.length)
			    	if (readdata.value.length===0) {return readdata.value}
            else{
			    	  return readdata.value.filter((record) => {
			    	    return (
                  record[0].includes(fl_date.value.toString().trim())
                  &&
                  record[2].includes(fl_meisai.value.toString().trim())
                  &&
                  record[3].toString().includes(fl_kin.value.toString().trim())
                  &&
                  record[4].includes(fl_shuppimoto.value.toString().trim())
                  &&
                  record[5].includes(fl_dai_ko.value.toString().trim())
                  &&
                  record[6].includes(fl_chuu_ko.value.toString().trim())
                  &&
                  record[7].includes(fl_memo.value.toString().trim()) 
                  );
                //return ( );
			    	  });
            }
            response.data.forEach((row)=>{
              if(fl_date_lst.value.includes(row[0])===false){fl_date_lst.value.push(row[0])}
              if(fl_meisai_lst.value.includes(row[2])===false){fl_meisai_lst.value.push(row[2])}
              if(fl_shuppimoto_lst.value.includes(row[4])===false){fl_shuppimoto_lst.value.push(row[4])}
              if(fl_dai_ko_lst.value.includes(row[5])===false){fl_dai_ko_lst.value.push(row[5])}
              if(fl_chuu_ko_lst.value.includes(row[6])===false){fl_chuu_ko_lst.value.push(row[6])}
              if(fl_memo_lst.value.includes(row[7])===false){fl_memo_lst.value.push(row[7])}
            })
			    })

          watch(readdata_filter,()=>{
            console_log('watch')
            fl_date_lst.value=[]
            fl_meisai_lst.value=[]
            fl_shuppimoto_lst.value=[]
            fl_dai_ko_lst.value=[]
            fl_chuu_ko_lst.value=[]
            fl_memo_lst.value=[]
            sum_kingaku.value = Number(0)
            readdata_filter.value.forEach((row)=>{
                if(fl_date_lst.value.includes(row[0])===false){fl_date_lst.value.push(row[0])}
                if(fl_meisai_lst.value.includes(row[2])===false){fl_meisai_lst.value.push(row[2])}
                if(fl_shuppimoto_lst.value.includes(row[4])===false){fl_shuppimoto_lst.value.push(row[4])}
                if(fl_dai_ko_lst.value.includes(row[5])===false){fl_dai_ko_lst.value.push(row[5])}
                if(fl_chuu_ko_lst.value.includes(row[6])===false){fl_chuu_ko_lst.value.push(row[6])}
                if(fl_memo_lst.value.includes(row[7])===false){fl_memo_lst.value.push(row[7])}
                sum_kingaku.value = Number(sum_kingaku.value) + Number(row[3])
              }
            )
            console_log(sum_kingaku.value)
          })

          onMounted(()=>{
            //read_html_moneyforward()
          })

          const savedata = () =>{//データベース登録
            const csv = readdata.value
            const params = new FormData();
            let start = '2099-12-31'
            let end = '2000-01-01'
            //params.append('csv', csv);

            readdata.value.forEach((row,index)=>{
              //console_log(row)
              if(start > row[0]){
                start = row[0]
              }
              if(end < row[0]){
                end = row[0]
              }
              params.append(`csv[${index}][date]`, row[0])
              params.append(`csv[${index}][meisai]`, row[2])
              params.append(`csv[${index}][kin]`, row[3])
              params.append(`csv[${index}][shuppimoto]`, row[4])
              params.append(`csv[${index}][daikou]`, row[5])
              params.append(`csv[${index}][chuukou]`, row[6])
              params.append(`csv[${index}][memo]`, row[7])
            })
            params.append(`start`, start)
            params.append(`end`, end)

            let url="ajax_ins_db.php"

            axios.post(url,params, {headers: {'Content-Type': 'application/json'}})
            .then((response)=>{
              console_log(response.data)
              if(response.data==="success"){
                readdata.value = []
              }
            })
            .catch((response)=>{
              console_log(response.data)
            })
            //console_log(csv)

          }


          const savecsv = () =>{//CSVしゅつりょく
            //type (databese or csv)
            const csv = readdata.value
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'ajax_csvoutput.php';

            let start = '2099-12-31'
            let end = '2000-01-01'
            //params.append('csv', csv);

            readdata.value.forEach((row,index)=>{
              //console_log(row)
              if(start > row[0]){
                start = row[0]
              }
              if(end < row[0]){
                end = row[0]
              }
              const hiddenField1 = document.createElement('input');
              hiddenField1.type = 'hidden';
              hiddenField1.name = `csv[${index}][date]`;
              hiddenField1.value = row[0];
              form.appendChild(hiddenField1);
              
              const hiddenField2 = document.createElement('input');
              hiddenField2.type = 'hidden';
              hiddenField2.name = `csv[${index}][meisai]`;
              hiddenField2.value = row[2];
              form.appendChild(hiddenField2);
              
              const hiddenField3 = document.createElement('input');
              hiddenField3.type = 'hidden';
              hiddenField3.name = `csv[${index}][kin]`;
              hiddenField3.value = row[3];
              form.appendChild(hiddenField3);
              
              const hiddenField4 = document.createElement('input');
              hiddenField4.type = 'hidden';
              hiddenField4.name = `csv[${index}][shuppimoto]`;
              hiddenField4.value = row[4];
              form.appendChild(hiddenField4);
              
              const hiddenField5 = document.createElement('input');
              hiddenField5.type = 'hidden';
              hiddenField5.name = `csv[${index}][daikou]`;
              hiddenField5.value = row[5];
              form.appendChild(hiddenField5);
              
              const hiddenField6 = document.createElement('input');
              hiddenField6.type = 'hidden';
              hiddenField6.name = `csv[${index}][chuukou]`;
              hiddenField6.value = row[6];
              form.appendChild(hiddenField6);
              
              const hiddenField7 = document.createElement('input');
              hiddenField7.type = 'hidden';
              hiddenField7.name = `csv[${index}][memo]`;
              hiddenField7.value = row[7];
              form.appendChild(hiddenField7);
            })
            const hiddenField8 = document.createElement('input');
            hiddenField8.type = 'hidden';
            hiddenField8.name = `start`;
            hiddenField8.value = start;
            form.appendChild(hiddenField8);

            const hiddenField9 = document.createElement('input');
            hiddenField9.type = 'hidden';
            hiddenField9.name = `end`;
            hiddenField9.value = end;
            form.appendChild(hiddenField9);

            document.body.appendChild(form);
            form.submit();
          }

          const hanei = (id) => {//一括変更
            console_log('hanei start')
            let val = document.getElementById(id).value
            console_log(val)
            readdata_filter.value.forEach((list)=>{
              console_log(list)
              if(id==="cdate"){list[0] = val}
              if(id==="cmeisai"){list[2] = val}
              if(id==="ckin"){list[3] = val}
              if(id==="cshuppi"){list[4] = val}
              if(id==="cdaikou"){list[5] = val}
              if(id==="cchukou"){list[6] = val}
              if(id==="cmemo"){list[7] = val}
            })
          }

          return{
            readdata,
            readfilename,
            uploadfile,
            fl_date,      
            fl_meisai,    
            fl_kin,       
            fl_shuppimoto,
            fl_dai_ko,    
            fl_chuu_ko,   
            fl_memo,      
            readdata_filter,
            fl_date_lst,
            fl_meisai_lst,
            fl_shuppimoto_lst,
            fl_dai_ko_lst,
            fl_chuu_ko_lst,
            fl_memo_lst,
            sum_kingaku,
            savedata,
            savecsv,
            hanei,
            filetype,
            mode,
          }
        }
      }).mount('#app');
    </script>
</BODY>
</html>