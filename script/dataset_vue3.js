const { createApp, ref, onMounted, computed,watch } = Vue;
const dataset = (test) => createApp({
  setup() {
    const mode = ref(test)
    const readdata = ref([])
    const readdata_summary = ref([])
    const readdata_monthly_summary = ref([])
    
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
    const sum_kingaku2 = ref(0)
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
            record.date.includes(fl_date.value.toString().trim())
            &&
            record.meisai.includes(fl_meisai.value.toString().trim())
            &&
            record.kin.toString().includes(fl_kin.value.toString().trim())
            &&
            record.shuppimoto.includes(fl_shuppimoto.value.toString().trim())
            &&
            record.daikoumoku.includes(fl_dai_ko.value.toString().trim())
            &&
            record.chuukoumoku.includes(fl_chuu_ko.value.toString().trim())
            &&
            record.memo.includes(fl_memo.value.toString().trim()) 
            );
        });
      }
    })


    watch(readdata_filter,()=>{//明細のフィルタリングに合わせて、フィルタ選択肢を絞る
      console_log('watch readdata_filter')
      fl_date_lst.value=[]
      fl_meisai_lst.value=[]
      fl_shuppimoto_lst.value=[]
      fl_dai_ko_lst.value=[]
      fl_chuu_ko_lst.value=[]
      fl_memo_lst.value=[]
      sum_kingaku.value = Number(0)
      
      readdata_filter.value.forEach((row)=>{
          if(fl_date_lst.value.includes(row.date)===false){fl_date_lst.value.push(row.date)}
          //if(fl_meisai_lst.value.includes(row[2])===false){fl_meisai_lst.value.push(row[2])}
          if(fl_shuppimoto_lst.value.includes(row.shuppimoto)===false){fl_shuppimoto_lst.value.push(row.shuppimoto)}
          if(fl_dai_ko_lst.value.includes(row.daikoumoku)===false){fl_dai_ko_lst.value.push(row.daikoumoku)}
          if(fl_chuu_ko_lst.value.includes(row.chuukoumoku)===false){fl_chuu_ko_lst.value.push(row.chuukoumoku)}
          if(fl_memo_lst.value.includes(row.memo)===false){fl_memo_lst.value.push(row.memo)}
          sum_kingaku.value = Number(sum_kingaku.value) + Number(row[3])
        }
      )
      
      //console_log(`watch readdata_filter sum: ${sum_kingaku.value}`)
      //readdata_summary.value = []
      //readdata_summary.value = read_db_meisai_summury(readdata.value)
    })

    onMounted(()=>{
      //read_html_moneyforward()
    })
    const from = ref('')
    const to = ref('')

    const savedata = () =>{//データベース登録
      const params = new FormData();
      const csv = readdata.value
      params.append('csv', JSON.stringify(csv));

      let start = ''
      let end = ''

      if(from.value===''){
        start = '2099-12-31'
        end = '2000-01-01'
        readdata.value.forEach((row,index)=>{
          if(start > row.date){
            start = row.date
          }
          if(end < row.date){
            end = row.date
          }
        })
      }else{
        start = from.value
        end = to.value
      }
      params.append(`start`, start)
      params.append(`end`, end)
      
      console_log(`${start} ～ ${end}`)
      //console_log(csv)
      
      let url="ajax_ins_db.php"

      axios.post(url,params, {headers: {'Content-Type': 'application/json'}})
      .then((response)=>{
        console_log(response.data)
        if(response.data==="success"){
          search_disable.value = false
          meisai_disable.value  = true
          readdata.value = []
          alert('システムに登録しました')
        }else{
          alert('登録できませんでした')
        }
      })
      .catch((response)=>{
        console_log(response.data)
        alert('登録できませんでした')
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
        if(id==="cdate"){list.date = val}
        if(id==="cmeisai"){list.meisai = val}
        if(id==="ckin"){list.kin = val}
        if(id==="cshuppi"){list.shuppimoto = val}
        if(id==="cdaikou"){list.daikoumoku = val}
        if(id==="cchukou"){list.chuukoumoku = val}
        if(id==="cmemo"){list.memo = val}
      })
    }

    const read_db_meisai = () => {
      axios
      .get(`ajax_read_db_meisai.php?fm=${from.value}&to=${to.value}`)
      .then((response) => {
        readdata.value = []
        //console_log(response.data)
        readdata.value = [...response.data]
        search_disable.value = false
        meisai_disable.value  = true
        console_log('read_db_meisai succsess')
      })
      .catch((error) => console.log(error));
    }
    const read_db_summury = () => {
      axios
      .get(`ajax_read_db_summury.php?fm=${from.value}&to=${to.value}`)
      .then((response) => {
        readdata_summary.value = []
        //console_log(response.data)
        readdata_summary.value = [...response.data]
        console_log('read_db_meisai succsess')
      })
      .catch((error) => console.log(error));

    }
    const read_db_meisai_and_summury = () => {
      console_log('read_db_meisai_and_summury start')
      read_db_summury()
      read_db_meisai()
    }
    const setfilter = (dai,chuu) =>{
      console_log(dai)
      fl_dai_ko.value = dai
      fl_chuu_ko.value = chuu

    }
    const cp_readdata_summary = computed(() => {
      console_log('start computed read_db_meisai_summury')  
      return readdata.value.reduce((result, current) => {
        const element_c = result.find((p) => p.daikoumoku === current.daikoumoku && p.chuukoumoku === current.chuukoumoku);
        if (element_c) {
          element_c.chuukei += current.kin; // sum
        }else{
          result.push({
            sort:("0" + current.sort).substr(-2),
            daikoumoku: current.daikoumoku,
            chuukoumoku: current.chuukoumoku,
            daichuukou:current.daikoumoku + ">" + current.chuukoumoku,
            chuukei: current.kin,
            daikei: 0
          });
        }
        
        const element_d = result.find((p) => p.daichuukou === current.daikoumoku);
        if(element_d){
          element_d.daikei += current.kin; // sum
        }else{
          result.push({
            sort:("0" + current.sort).substr(-2),
            daikoumoku: current.daikoumoku,
            chuukoumoku: "",
            daichuukou:current.daikoumoku,
            chuukei: 0,
            daikei: current.kin
          });
        }
        
        return result;
      }, []).sort((a,b)=>{return (a.sort+a.daichuukou > b.sort+b.daichuukou ?1:-1)})
    })
    /*
    watch(readdata,()=>{
      console_log('watch readdata')
      //read_db_meisai_summury()
    })
    */
    //データ編集モードの切り替え
    const search_disable = ref(false)
    const meisai_disable = ref(true)
    const cgmode =()=>{
      search_disable.value = true
      meisai_disable.value  = false
    }

    return{
      readdata,
      cp_readdata_summary,
      readdata_summary,
      readdata_monthly_summary,
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
      sum_kingaku2,
      savedata,
      savecsv,
      hanei,
      filetype,
      mode,
      read_db_meisai,
      read_db_summury,
      read_db_meisai_and_summury,
      //read_db_meisai_summury,
      from,
      to,
      setfilter,
      search_disable,
      meisai_disable,
      cgmode,
    }
  }
});

const dataset2 = () => createApp({
  setup() {
    const test = ref('test')
    return{
      test,
    }
  }
});