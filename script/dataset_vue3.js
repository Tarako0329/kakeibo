const { createApp, ref, onMounted, onBeforeMount, computed,watch } = Vue;
const dataset = (Where_to_use) => createApp({
  setup() {
    const pagename = ref(Where_to_use)
    const readdata = ref([])
    const readdata_summary = ref([])
    const readdata_monthly_summary = ref([])
    const daikoumoku_ms = ref([])
    const input_file_btn = () =>{
      document.getElementById('file').click()
    }
    
    const readfilename = ref('')
    const filetype = ref('')
    const read_html_moneyforward = () => {//
      axios
      .get(`ajax_read_forward.php?fn=${readfilename.value}`)
      .then((response) => {
        readdata.value = []
        filetype.value = ''
        //console_log(response.data)
        readdata.value = [...response.data.data]
        filetype.value = response.data.type
        
        console_log('read_html_moneyforward succsess')
      })
      .catch((error) => console.log(error));
    }

    const uploadfile = () =>{
      const file = document.getElementById('file').files[0];
      console_log(file.name)
      const params = new FormData();
      params.append('user_file_name', file);
      axios.post("ajax_loader.php",params, {headers: {'Content-Type': 'multipart/form-data'}})
      .then((response)=>{
        //console_log(response.data)
        if(response.data.status==="success"){
          readfilename.value = response.data.filename
          read_html_moneyforward()
        }else{

        }
      })
      .catch((error)=>{
        console_log(error)
      })
      //console_log(file)
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
    const filter_reset = () =>{
      fl_date.value       = ''
      fl_meisai.value     = ''
      fl_kin.value        = ''
      fl_shuppimoto.value = ''
      fl_dai_ko.value     = ''
      fl_chuu_ko.value    = ''
      fl_memo.value       = ''
    }
    const filseted = computed(()=>{
      if(fl_date.value + fl_meisai.value + fl_kin.value + fl_shuppimoto.value + fl_dai_ko.value + fl_chuu_ko.value + fl_memo.value !== ''){
        return true
      }else{
        return false
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
          sum_kingaku.value = Number(sum_kingaku.value) + Number(row.kin)
        }
      )
      
      console_log(`watch readdata_filter sum: ${sum_kingaku.value}`)
      //readdata_summary.value = []
      //readdata_summary.value = read_db_meisai_summary(readdata.value)
    })

    const from = ref('')
    const to = ref('')
    const loader = ref(false)

    const ym_next = (ym) =>{
      if(ym.toString().length !== 6){
        console_log('なにもしない')
        return //yyyymm以外はなにもしない
      }
      if((ym + 1).toString().substr(-2)=="13"){
        ym = Number(ym.toString().substr(0,4)) + Number(1)
        console_log(ym)
        ym = ym.toString() + "01"
      }else{
        ym = Number(ym) + Number(1)
      }
      return ym
    }
    const ym_back = (ym) =>{
      if(ym.toString().length !== 6){
        console_log('なにもしない')
        return //yyyymm以外はなにもしない
      }
      if((ym - 1).toString().substr(-2)=="00"){
        ym = Number(ym.toString().substr(0,4)) - Number(1)
        console_log(ym)
        ym = ym.toString() + "12"
      }else{
        ym = Number(ym) - Number(1)
      }
      return ym
    }
    const from_next = () =>{
      from.value = ym_next(from.value)
      if(pagename.value==="data_summary12m.php"){
        read_db_summary_long()
      }else if(pagename.value==="data_comparison.php"){
        read_db_comparison()
      }
    }
    const from_back = () =>{
      from.value = ym_back(from.value)
      if(pagename.value==="data_summary12m.php"){
        read_db_summary_long()
      }else if(pagename.value==="data_comparison.php"){
        read_db_comparison()
      }
    }


    const savedata = () =>{//データベース登録
      loader.value = true
      const params = new FormData();
      const csv = readdata.value
      params.append('csv', JSON.stringify(csv));

      let start = ''
      let end = ''
      let mode = ''

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
    
      if(from.value===''){
        mode = 'ins'
      }else{
        mode = 'upd'
        params.append(`startYM`, from.value)  //デリインの月度範囲
        params.append(`endYM`, to.value)      //デリインの月度範囲
      }
      params.append(`start`, start) //月度算出対象範囲
      params.append(`end`, end)     //月度算出対象範囲
      params.append(`mode`, mode)
      
      console_log(`${start} ～ ${end}`)
      //console_log(csv)
      
      let url="ajax_ins_db.php"

      axios.post(url,params, {headers: {'Content-Type': 'application/json'}})
      .then((response)=>{
        console_log(response.data.status)
        if(response.data.status === 'success'){
          search_disable.value = false
          meisai_disable.value  = true
          readdata.value = []
          document.getElementById('file').value = ''
          alert('システムに登録しました')
        }else{
          console_log('else')
          alert('登録できませんでした')
        }
      })
      .catch((error)=>{
        console_log('ajax_ins_db.php ERROR')
        console_log(error)
        alert('リターンエラー：登録できませんでした')
      })
      .finally(()=>{
        loader.value = false
      })
    }

    const upddata = () =>{//データベース登録(デリイン)
      if(from.value===''){
        console_log('期間未指定です')
        return
      }
      loader.value = true
      const params = new FormData();
      //const csv = readdata.value
      params.append('csv', JSON.stringify(readdata.value));

      let start = ''
      let end = ''

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
    
      params.append(`startYM`, from.value)  //デリインの月度範囲
      params.append(`endYM`, to.value)      //デリインの月度範囲
      params.append(`start`, start) //月度算出対象範囲
      params.append(`end`, end)     //月度算出対象範囲
      
      console_log(`${start} ～ ${end}`)

      axios.post("ajax_delins_db.php",params, {headers: {'Content-Type': 'application/json'}})
      .then((response)=>{
        console_log(response.data.status)
        if(response.data.status === 'success'){
          search_disable.value = false
          meisai_disable.value  = true
          //readdata.value = []
          alert('システムに登録しました')
        }else{
          console_log('else')
          alert('登録できませんでした')
        }
      })
      .catch((error)=>{
        console_log('ajax_ins_db.php ERROR')
        console_log(error)
        alert('リターンエラー：登録できませんでした')
      })
      .finally(()=>{
        loader.value = false
      })
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
        /*
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
        */
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

      const hiddenField1 = document.createElement('input');
      hiddenField1.type = 'hidden';
      hiddenField1.name = `csv`;
      hiddenField1.value = JSON.stringify(csv);
      form.appendChild(hiddenField1);

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
      console_log('read_db_meisai start')
      axios
      .get(`ajax_read_db_meisai.php?fm=${from.value}&to=${to.value}`)
      .then((response) => {
        readdata.value = []
        //console_log(response.data)
        readdata.value = [...response.data.meisai]
        daikoumoku_ms.value = [...response.data.daikou_ms]
        search_disable.value = false
        meisai_disable.value  = true
        //console_log(get_sortNO("収入"))
        console_log('read_db_meisai succsess')
      })
      .catch((error) => {
        console_log('read_db_meisai error')
        console.log(error)
      });
    }
    const read_db_summary = () => {
      axios
      .get(`ajax_read_db_summary.php?fm=${from.value}&to=${to.value}`)
      .then((response) => {
        readdata_summary.value = []
        console_log(response.data)
        readdata_summary.value = [...response.data]
        console_log('read_db_meisai succsess')
      })
      .catch((error) => console.log(error));

    }
    const comparison_sum_val = ref([])
    const comparison_sum = () =>{
      console_log('comparison_sum start')
      comparison_sum_val.value["zen_m"]=0
      comparison_sum_val.value["zen_y"]=0
      comparison_sum_val.value["tou"]=0
      comparison_sum_val.value["sa_m"]=0
      comparison_sum_val.value["sa_y"]=0
      readdata_summary.value.forEach((row)=>{
        comparison_sum_val.value["zen_m"] = comparison_sum_val.value["zen_m"] + row.m_ago_chuukei
        comparison_sum_val.value["zen_y"] = comparison_sum_val.value["zen_y"] + row.y_ago_chuukei
        comparison_sum_val.value["tou"] = comparison_sum_val.value["tou"] + row.moto_chuukei
      })
      comparison_sum_val.value["sa_m"] = comparison_sum_val.value["tou"] - comparison_sum_val.value["zen_m"]
      comparison_sum_val.value["sa_y"] = comparison_sum_val.value["tou"] - comparison_sum_val.value["zen_y"]
      console_log(comparison_sum_val.value)
    }
    const read_db_comparison = () => {
      if(to.value===''){
        to.value='m'
      }
      axios
      .get(`ajax_read_db_summary_comparison.php?fm=${from.value}&to=${to.value}`)
      .then((response) => {
        readdata_summary.value = []
        //console_log(response.data)
        readdata_summary.value = [...response.data]
        comparison_sum()
        create_graph2(document.getElementById('myChart2'))
        console_log('read_db_comparison succsess')
      })
      .catch((error) => console.log(error));
    }
    const hanni = ref('12m')
    const read_db_summary_long = () => {
      console_log('read_db_summary_long start')
      axios
      .get(`ajax_read_db_summary_${hanni.value}.php?fm=${from.value}`)
      .then((response) => {
        readdata_summary.value = []
        console_log(response.data)
        readdata_summary.value = response.data
        if(open_fil.value === ''){
          open_fil.value="収入"
        }
        create_graph(document.getElementById('myChart'))
        console_log('read_db_summary_long succsess')
      })
      .catch((error) => console.log(error));
    }
    const open_fil = ref('')
    const open_utiwake =(daikoumoku) =>{
      if(open_fil.value === daikoumoku){
        open_fil.value = ''
      }else{
        open_fil.value = daikoumoku
      }
      create_graph(document.getElementById('myChart'))
    }
    const read_db_meisai_and_summary = () => {
      console_log('read_db_meisai_and_summary start')
      read_db_summary()
      read_db_meisai()
    }
    const setfilter = (dai,chuu) =>{
      console_log(dai)
      fl_dai_ko.value = dai
      fl_chuu_ko.value = chuu
    }
    const get_sortNO = (name) =>{
      //console_log('start get_sortNO')
      result = daikoumoku_ms.value.filter((row)=>row.daikoumoku.includes(name))
      //console_log(result)
      if(result.length===0){
        return 999
      }else{
        return result[0].sort
      }
    }
    const cp_readdata_summary = computed(() => {
      console_log('start computed read_db_meisai_summary')
      return readdata.value.reduce((result, current) => {
        const element_c = result.find((p) => p.daikoumoku === current.daikoumoku && p.chuukoumoku === current.chuukoumoku);
        if (element_c) {
          element_c.chuukei += current.kin; // sum
        }else if(current.daikoumoku!==""){
          result.push({
            //sort:("00" + current.sort).substr(-3),
            sort:("00" + get_sortNO(current.daikoumoku)).substr(-3),
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
        }else if(current.daikoumoku!==""){
          result.push({
            //sort:("00" + current.sort).substr(-3),
            sort:("00" + get_sortNO(current.daikoumoku)).substr(-3),
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
    
    watch(readdata,()=>{
      console_log('watch readdata')
      sum_kingaku2.value = 0
      readdata.value.forEach((row)=>{
        sum_kingaku2.value = Number(sum_kingaku2.value) + Number(row.kin)
      })
    })
    
    //データ編集モードの切り替え
    const search_disable = ref(false)
    const meisai_disable = ref(true)
    const cgmode =()=>{
      search_disable.value = true
      meisai_disable.value  = false
    }

    const ymlist = ref([])
    onMounted(()=>{
      console_log("onMounted")
      comparison_sum_val.value["zen"]=0
      comparison_sum_val.value["tou"]=0
      comparison_sum_val.value["sa"]=0
      
      if(pagename.value!=="data_custmer.php"){
        axios
        .get(`ajax_read_db_meisai_ymlist.php`)
        .then((response) => {
          ymlist.value = []
          //console_log(response.data)
          ymlist.value = response.data
          from.value = ymlist.value.max_min[0].max_getudo
          console_log('ajax_read_db_meisai_ymlist succsess')
          if(pagename.value==="data_summary12m.php"){
            read_db_summary_long()
          }
          if(pagename.value==="data_summary.php"){
            read_db_meisai()
          }
          if(pagename.value==="data_comparison.php"){
            console_log('data_comparison root')
            to.value="m"
            read_db_comparison()
          }

        })
        .catch((error) => console.log(error));
      }

    })
    onBeforeMount(()=>{
      console_log("onBeforeMount:"+pagename.value)
      if(pagename.value==="data_summary12m.php"){
        readdata_summary.value = {
          label:['----/--','----/--','----/--','----/--','----/--','----/--','----/--','----/--','----/--','----/--','----/--','----/--']
          ,data:[]
        }
        //console_log(readdata_summary.value)
      }
    })

    //chartjs
    const get_graph_data = (daikoumoku) => {
      console_log("get_graph_data : daikoumoku")
      console_log(daikoumoku)
      let return_data = []
      const data = readdata_summary.value.data.filter((row)=>{
        return (row.daikoumoku.includes(daikoumoku))
      })
      //console_log(data)
      data.forEach((row)=>{
        return_data.push({
          label : row.chuukoumoku
          ,data : [row.m12c,row.m11c,row.m10c,row.m9c,row.m8c,row.m7c,row.m6c,row.m5c,row.m4c,row.m3c,row.m2c,row.m1c]
          ,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
        })
      })
      return return_data
    }

    const get_graph_data2 = () => {
      console_log("get_graph_data2")
      let d_list = []
      let c_list = []
      let nowitem = ''
      readdata_summary.value.forEach((row)=>{
        if(nowitem !== row.daikoumoku){
          d_list.push({
            label : row.daikoumoku
            ,data : [row.y_ago_daikei,row.m_ago_daikei,row.moto_daikei]
            ,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
          })
          nowitem = row.daikoumoku
        }
        c_list.push({
          label : row.chuukoumoku
          ,data : [row.y_ago_chuukei,row.m_ago_chuukei,row.moto_chuukei]
          ,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
        })
      })
      return d_list
    }

    var graph_obj
    const create_graph = (ctx) =>{
      console_log("create_graph : graph_data")
      
      const graph_data = {
        labels    : readdata_summary.value.label
        ,datasets : get_graph_data(open_fil.value)
      }

      if(graph_obj){
        graph_obj.destroy()
      }

      graph_obj = new Chart(ctx, {
        type : 'bar'
        ,data: graph_data
        ,options: {
          plugins: {
            title: {
              display: true,
              text: open_fil.value
            },
          },
          responsive: true,
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true
            }
          }
        }
      })      
    }

    const create_graph2 = (ctx) =>{
      console_log("create_graph2 : graph_data")
      
      const graph_data = {
        labels    : ['前年','前月','当月']
        ,datasets : get_graph_data2()
      }

      if(graph_obj){
        graph_obj.destroy()
      }

      graph_obj = new Chart(ctx, {
        type : 'bar'
        ,data: graph_data
        ,options: {
          plugins: {
            title: {
              display: true,
              text: '比較グラフ'
            },
          },
          responsive: true,
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true
            }
          },
          aspectRatio:'1'
        }
      })      
    }



    return{
      readdata,
      cp_readdata_summary,
      readdata_summary,
      readdata_monthly_summary,
      daikoumoku_ms,
      input_file_btn,
      readfilename,
      uploadfile,
      fl_date,      
      fl_meisai,    
      fl_kin,       
      fl_shuppimoto,
      fl_dai_ko,    
      fl_chuu_ko,   
      fl_memo,      
      filseted,
      filter_reset,
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
      upddata,
      savecsv,
      hanei,
      filetype,
      pagename,
      read_db_meisai,
      read_db_summary,
      read_db_comparison,
      read_db_summary_long,
      hanni,
      comparison_sum_val,
      read_db_meisai_and_summary,
      //read_db_meisai_summary,
      from,
      to,
      from_next,
      from_back,
      setfilter,
      search_disable,
      meisai_disable,
      cgmode,
      loader,
      open_fil,
      open_utiwake,
      ymlist
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