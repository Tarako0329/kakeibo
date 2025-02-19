const { createApp, ref, onMounted, onBeforeMount, computed,watch } = Vue;
const dataset = (Where_to_use) => createApp({
	setup() {
		const pagename = ref(Where_to_use)
		const readdata = ref([])
		const readdata_summary = ref([])
		const max_pair_no = ref(0)
		
		const daikoumoku_ms = ref([])
		const input_file_btn = () =>{
			document.getElementById('file').click()
		}
		
		const readfilename = ref('')
		const filetype = ref('')

		const clear_data = () =>{
			document.getElementById('file').value = ''
			readdata.value = []
			axios
			.get(`ajax_del_db_zaim_work.php`)
			.then((response) => {
				console_log(response.data)
				console_log('clear_data succsess')
			})
			.catch((error) => console.log(error));
		}

		const read_html_moneyforward = () => {//
			axios
			.get(`ajax_read_forward.php?fn=${readfilename.value}`)
			.then((response) => {
				readdata.value = []
				filetype.value = ''
				//console_log(response.data)
				readdata.value = [...response.data.data]
				filetype.value = response.data.type
				document.getElementById('file').value = ''
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
			let return_data
			if (readdata.value.length===0) {return_data = readdata.value}
			else{
				return_data = readdata.value.filter((record) => {
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
			return return_data.sort((a,b)=>{
				return (a.date+a.b_pair_no+a.b_moto+a.meisai+a.memo+a.kin < b.date+b.b_pair_no+b.b_moto+b.meisai+b.memo+b.kin ?1:-1)
			})
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
					
					if(fl_shuppimoto_lst.value.includes(row.shuppimoto)===false){fl_shuppimoto_lst.value.push(row.shuppimoto)}
					if(fl_dai_ko_lst.value.includes(row.daikoumoku)===false){fl_dai_ko_lst.value.push(row.daikoumoku)}
					if(fl_chuu_ko_lst.value.includes(row.chuukoumoku)===false){fl_chuu_ko_lst.value.push(row.chuukoumoku)}
					if(fl_memo_lst.value.includes(row.memo)===false){fl_memo_lst.value.push(row.memo)}
					sum_kingaku.value = Number(sum_kingaku.value) + Number(row.kin)
				}
			)
			
			console_log(`watch readdata_filter sum: ${sum_kingaku.value}`)
		})

		const from = ref('')
		const to = ref('')
		const loader = ref(false)

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
			params.append(`filename`, readfilename.value)     //月度算出対象範囲
			console_log(`ファイル名：${readfilename.value}`)
			
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
			if(meisai_disable.value  !== false){
				alert("「修正モード」ボタンを押してください。")
				return 0
			}
			let val = document.getElementById(id).value
			//console_log(val)
			readdata_filter.value.forEach((list)=>{
				//console_log(list)
				if(id==="cdate"){list.date = val}
				if(id==="cmeisai"){
					list.meisai = val
					list.fl_meisai = val
				}
				if(id==="ckin"){
					list.kin = val
					list.fl_kin = val
				}
				if(id==="cshuppi"){
					list.shuppimoto = val
					list.fl_shuppimoto = val
				}
				if(id==="cdaikou"){
					list.daikoumoku = val
					list.fl_daikoumoku = val
				}
				if(id==="cchukou"){
					list.chuukoumoku = val
					list.fl_chuukoumoku = val
				}
				if(id==="cmemo"){
					list.memo = val
					list.fl_memo = val
				}
			})
		}

		const read_db_meisai = () => {
			console_log('dataset の read_db_meisai start')
			axios
			.get(`ajax_read_db_meisai.php?fm=${from.value}&to=${to.value}`)
			.then((response) => {
				readdata.value = []
				console_log(response.data)
				readdata.value = response.data.meisai
				daikoumoku_ms.value = response.data.daikou_ms
				max_pair_no.value = response.data.max_pair_no.max_pair_no
				
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
				console_log('read_db_summary succsess')
			})
			.catch((error) => console.log(error));

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
			search_disable.value = true			//期間検索をOFFにする
			meisai_disable.value  = false		//明細変更をONにする
		}

		const ymlist = ref([])

		const upd = (index,colom) =>{
			console_log(`upd -> ${index}:${colom}`)
			readdata_filter.value[index][colom] = readdata_filter.value[index][`fl_${colom}`]
		}

		const import_log = ref([])
		const get_import_log = () =>{
			axios
			.get(`ajax_read_db_importlog.php`)
			.then((response) => {
				console_log(response.data.import_log)
				import_log.value = response.data.import_log
				console_log('ajax_read_db_importlog succsess')
			})
			.catch((error) => console.log(error));
		}

		//明細分割
		const bunkatu_index = ref(0)
		const bunkatu_data = ref([])
		const bunkatu = (index) =>{
			if(meisai_disable.value  !== false){
				alert("「修正モード」ボタンを押してください。")
				return 0
			}else{
				document.getElementById('meisai_bunkatu_modal_open').click()
			}
			bunkatu_index.value = index
			bunkatu_data.value=[]
			bunkatu_data.value.push({...readdata_filter.value[index]})
			bunkatu_data.value.push({...readdata_filter.value[index]})
			console_log(bunkatu_index.value)
		}
		const bunkatu_add = (index) =>{
			bunkatu_data.value.push({...readdata_filter.value[index]})
		}

		const split_kingaku_chk = ()=>{
			console_log(bunkatu_index.value)
			//console_log(readdata_filter.value[bunkatu_index.value])
			let goukei = 0
			bunkatu_data.value.forEach((list)=>{
				goukei = Number(goukei) + Number(list.fl_kin)
			})
			//console_log(readdata_filter.value[bunkatu_index.value].fl_kin)
			
			
			if(Number(goukei)!==Number(readdata_filter.value[bunkatu_index.value].fl_kin)){
				alert("分割後の合計金額が不足してます")
				return 0
			}
			if(confirm("明細を分割してよいですか？")){
				console_log("いいよ")
				//ソート前データを編集
				let shori_seq = readdata_filter.value[bunkatu_index.value].SEQ
				//let shori_index = 0
				let moto_aka_data = []
				readdata.value.forEach((list,index)=>{
					if(list.SEQ === shori_seq){
						list.b_moto = 2
						list.b_pair_no = Number(max_pair_no.value) + Number(1)
						//shori_index = index
						console_log(list)
						moto_aka_data = {...list}
					}
				})
				moto_aka_data.b_moto = 1
				moto_aka_data.SEQ = -1
				moto_aka_data.fl_kin = Number(moto_aka_data.fl_kin) * Number(-1)
				moto_aka_data.kin = Number(moto_aka_data.kin) * Number(-1)
				readdata.value.push({...moto_aka_data})

				let local_seq = -2
				bunkatu_data.value.forEach((list)=>{
					list.b_pair_no = Number(max_pair_no.value) + Number(1)
					list.meisai = list.fl_meisai
					list.kin = list.fl_kin
					list.daikoumoku = list.fl_daikoumoku
					list.chuukoumoku = list.fl_chuukoumoku
					list.memo = list.fl_memo
					list.SEQ = local_seq
					readdata.value.push({...list})

					local_seq = Number(local_seq) - Number(1)
				})
				max_pair_no.value = Number(max_pair_no.value) + Number(1)
				document.getElementById('modal_close').click()
			}
		}

		const bunkatu_modosu = (index) =>{
			if(meisai_disable.value  !== false){
				alert("「修正モード」ボタンを押してください。")
				return 0
			}
			if(confirm("分割した明細を元に戻しますか？")){
				console_log("いいよ")
				let p_b_pair_no = readdata_filter.value[index].b_pair_no
				console_log(p_b_pair_no)
				readdata.value.forEach((list,index)=>{
					if(list.b_pair_no === p_b_pair_no){
						if(list.b_moto===2){
							list.b_moto = 0
							list.b_pair_no = 0
						}else{
							console_log(`SEQ:${list.SEQ} 削除`)
							//list.toSpliced(0)
							console_log(list)
							delete readdata.value[index]
							console_log(list)
						}
					}
				})

			}else{

			}

		}

		onMounted(()=>{
			console_log("onMounted")
			axios
			.get(`ajax_read_db_daikoumoku_ms.php`)
			.then((response) => {
				console_log(response.data)
				daikoumoku_ms.value = response.data.daikou_ms
				console_log('ajax_read_db_daikoumoku_ms succsess')
			})
			.catch((error) => {
				console_log('ajax_read_db_daikoumoku_ms error')
				console.log(error)
			});

		})
		onBeforeMount(()=>{
			console_log("onBeforeMount:"+pagename.value)
			if(pagename.value!=="data_custmer.php"){
				axios
				.get(`ajax_read_db_meisai_ymlist.php`)
				.then((response) => {
					ymlist.value = []
					//console_log(response.data)
					ymlist.value = response.data
					from.value = ymlist.value.max_min[0].max_getudo
					console_log('ajax_read_db_meisai_ymlist succsess')
					if(pagename.value==="index.php"){
						read_db_meisai()
						
					}
				})
				.catch((error) => console.log(error));

				get_import_log()
			}
		})

		return{
			upd,
			readdata,
			cp_readdata_summary,
			input_file_btn,
			readfilename,
			clear_data,
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
			pagename,
			read_db_meisai,
			read_db_summary,
			from,
			to,
			setfilter,
			search_disable,
			meisai_disable,
			cgmode,
			loader,
			import_log,
			daikoumoku_ms,
			bunkatu_index,
			bunkatu,
			bunkatu_add,
			bunkatu_data,
			split_kingaku_chk,
			max_pair_no,
			bunkatu_modosu
		}
	}
});

const summary_bunseki = (Where_to_use) => createApp({
	setup() {
		const pagename = ref(Where_to_use)
		const readdata_summary = ref([])
		const readdata_monthly_summary = ref([])

		const from = ref('')
		const to = ref('')

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
			//read_db_meisai()
		}

		//データ編集モードの切り替え
		const search_disable = ref(false)

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
						//read_db_meisai()
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

		const popup_meisai = ref([])
		const modal_label = ref('')
		const get_meisai = (p_ymd,dk,ck) =>{
			modal_label.value = `${p_ymd} ${dk} > ${ck}`

			let ymd = p_ymd.replace("/","")
			let ymd_from
			if(hanni.value==='5y'){
				let y = (Number(String(p_ymd).slice(0,4)) - 1) * 100
				let m = Number(String(p_ymd).slice(-2)) + 1
				if(m===13){
					m=Number(1)
					y = (Number(String(p_ymd).slice(0,4))) * 100
				}else{

				}
				ymd_from = y + m
			}else{
				ymd_from = ymd
			}
			console_log(`select * from kakeibo where getudo between ${ymd_from} and ${ymd} and daikoumoku=${dk} and chuukoumoku=${ck}`)
			console_log('get_meisai start')
			axios
			.get(`ajax_read_db_meisai.php?fm=${ymd_from}&to=${ymd}&daikoumoku=${dk}&chuukoumoku=${ck}`)
			.then((response) => {
				console_log(response.data.meisai)
				console_log('get_meisai succsess')
				popup_meisai.value = response.data.meisai
				document.getElementById('meisai_modal_btn').click()
			})
			.catch((error) => {
				console_log('get_meisai error')
				console.log(error)
			});

		}
		return{
			readdata_summary,
			readdata_monthly_summary,
			read_db_comparison,
			read_db_summary_long,
			hanni,
			comparison_sum_val,
			read_db_meisai_and_summary,
			from,
			to,
			from_next,
			from_back,
			search_disable,
			open_fil,
			open_utiwake,
			get_meisai,
			popup_meisai,
			modal_label,
		}
	}
});

const user_setting = () => createApp({
	setup() {
		const kisanbi = ref('')
		const shukuzitu = ref('')
		const nendomatu = ref('')
		const uid = ref('')
		const mail = ref('')
		const pass = ref('')
		const pass2 = ref('')
		const pass_hen = ref(false)
		const loader = ref(false)

		const page = ref('setting') //or master
		const page_changer = (p_page) =>{
			page.value = p_page
			if(p_page==="setting"){
				document.getElementById("setting").className = "nav-link active"
				document.getElementById("master").className = "nav-link "
			}else if(p_page==="master"){
				document.getElementById("setting").className = "nav-link"
				document.getElementById("master").className = "nav-link active"
			}
		}

		const bunrui_ms = ref([])
		const bunrui_ms_sort = computed(()=>{
			return bunrui_ms.value.sort((a,b)=>{
				return a.sort > b.sort?1:-1
			})
		})
		const bunrui_ms_new = ref('')
		const bunrui_ms_add = () =>{
			if(!bunrui_ms_new.value){return 0}
			let new_record = {
				"uid" :uid
				,"sort":10000
				,"daikoumoku":bunrui_ms_new.value}
			bunrui_ms.value.push(new_record)

			bunrui_ms_new.value=""
		}
		const bunrui_ms_del = (index) =>{
			bunrui_ms_sort.value.splice(index,1)
		}

		const onsubmit_ms =()=>{
			loader.value = true
			const params = new FormData();
			//params.append('bunrui_ms', bunrui_ms.value);
			params.append('mail', mail.value);
			params.append('uid', uid.value);
			let i = 0
      bunrui_ms.value.forEach((row)=>{
        params.append(`bunrui_ms[${i}][uid]`,row.uid)
        params.append(`bunrui_ms[${i}][sort]`,row.sort)
        params.append(`bunrui_ms[${i}][daikoumoku]`,row.daikoumoku)
        i=i+1
      })
      

			axios
			.post(`ajax_upd_db_bunrui_ms.php`,params, {headers: {'Content-Type': 'multipart/form-data'}})
			.then((response) => {
				console_log(response.data)
				alert("登録しました")
				console_log('ajax_upd_db_bunrui_ms succsess')
			})
			.catch((error) => {
				alert("登録失敗")
				console_log('ajax_upd_db_bunrui_ms error')
				console.log(error)
			})
			.finally(()=>{
				loader.value = false
			})
		}

		const get_user = () =>{
			axios
			.get(`ajax_read_db_user.php`)
			.then((response) => {
				console_log(response.data)
				kisanbi.value = response.data.user[0].kisanbi
				shukuzitu.value = response.data.user[0].shukuzitu
				nendomatu.value = response.data.user[0].nendomatu
				uid.value = response.data.user[0].uid
				mail.value = response.data.user[0].mail
				bunrui_ms.value = response.data.bunrui_ms
				console_log('ajax_read_db_user succsess')
			})
			.catch((error) => {
				console_log('ajax_read_db_user error')
				console.log(error)
			});
		}

		watch(pass_hen,()=>{
			if(pass_hen.value){
				document.getElementById("pass").disabled = false
				document.getElementById("pass2").disabled = false
			}else{
				document.getElementById("pass").disabled = true
				document.getElementById("pass2").disabled = true
			}
		})
		
		const onsubmit =()=>{
			if(pass_hen.value){
				if(pass.value!==pass2.value){
					alert("パスワードが不一致です。")
					return
				}
			}
			loader.value = true
			const params = new FormData();
			params.append('kisanbi', kisanbi.value);
			params.append('shukuzitu', shukuzitu.value);
			params.append('nendomatu', nendomatu.value);
			params.append('mail', mail.value);
			params.append('uid', uid.value);
			params.append('pass', pass.value);
			params.append('pass_hen', pass_hen.value);

			axios
			.post(`ajax_upd_db_user.php`,params, {headers: {'Content-Type': 'application/json'}})
			.then((response) => {
				console_log(response.data)
				alert("登録しました")
				console_log('ajax_upd_db_user succsess')
			})
			.catch((error) => {
				alert("登録失敗")
				console_log('ajax_upd_db_user error')
				console.log(error)
			})
			.finally(()=>{
				loader.value = false
			})
		}

		const move_recorde = (e,index) =>{
			//console_log(e.currentTarget)
			//console_log(index)
			// 出力テスト
			e.dataTransfer.setData( "index" , index);
		}
		const moving_in = (e) =>{
			console_log("moving_in")
			//console_log(e.currentTarget)
			e.currentTarget.classList.toggle("dragging")
		}
		const moving_out = (e) =>{
			console_log("moving_out")
			//console_log(e.currentTarget)
			e.currentTarget.classList.toggle("dragging")
		}
		const dorpping = (e,p_sort) =>{
			console_log("dorpping")
			let index = e.dataTransfer.getData("index")
			//console_log(bunrui_ms_sort.value[index])
			if(bunrui_ms_sort.value[index].sort == Number(p_sort)){
				//なにもしない
			}else if(bunrui_ms_sort.value[index].sort > Number(p_sort)){
				bunrui_ms_sort.value[index].sort = Number(p_sort) - 1
			}else{
				bunrui_ms_sort.value[index].sort = Number(p_sort) + 1
			}
			bunrui_ms_sort.value.forEach((list,index)=>{
				list.sort = Number(index) * 10
			})
			console_log(bunrui_ms_sort.value)
			e.currentTarget.classList.remove("dragging")
			e.dataTransfer.clearData()
		}
		

		onMounted(()=>{
			get_user()
			document.getElementById("pass").disabled = true
			document.getElementById("pass2").disabled = true
		})
		return{
			kisanbi,
			shukuzitu,
			nendomatu,
			uid,
			mail,
			pass,
			pass2,
			pass_hen,
			loader,
			onsubmit,
			bunrui_ms,
			bunrui_ms_sort,
			bunrui_ms_add,
			bunrui_ms_new,
			move_recorde,
			moving_in,
			moving_out,
			dorpping,
			page_changer,
			page,
			onsubmit_ms,
			get_user,
			bunrui_ms_del,
		}
	}
});
