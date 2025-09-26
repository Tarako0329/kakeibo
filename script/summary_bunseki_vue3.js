const { createApp, ref, onMounted, onBeforeMount, computed,watch } = Vue;

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

		const special_flg = ref(0)	//臨時入出金含む

		const hanni = ref('12m')
		const read_db_summary_long = () => {
			console_log('read_db_summary_long start')
			axios
			.get(`ajax_read_db_summary_${hanni.value}.php?fm=${from.value}&special=${special_flg.value}`)
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

			if(daikoumoku !== ""){
				const data = readdata_summary.value.data.filter((row)=>{
					return (row.daikoumoku.includes(daikoumoku))
				})
				//console_log(data)
				if(hanni.value==='5y'){
					data.forEach((row)=>{
						return_data.push({
							label : row.chuukoumoku
							,data : [row.m12c,row.m11c,row.m10c,row.m9c,row.m8c,row.m7c]
							,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
						})
					})
				}else{
					data.forEach((row)=>{
						return_data.push({
							label : row.chuukoumoku
							,data : [row.m12c,row.m11c,row.m10c,row.m9c,row.m8c,row.m7c,row.m6c,row.m5c,row.m4c,row.m3c,row.m2c,row.m1c]
							,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
						})
					})
				}
			}else{
				readdata_summary.value.data.forEach((row,index)=>{
					console_log(row)
					console_log(index)
					if(index !== 0){
						if(row.daikoumoku === readdata_summary.value.data[index-1].daikoumoku){
							return //前回と大項目が同じ場合はスキップ
						}
					}
					if(hanni.value==='5y'){
						return_data.push({
							label : row.daikoumoku
							,data : [row.m12d,row.m11d,row.m10d,row.m9d,row.m8d,row.m7d]
							,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
						})
					}else{
						return_data.push({
							label : row.daikoumoku
							,data : [row.m12d,row.m11d,row.m10d,row.m9d,row.m8d,row.m7d,row.m6d,row.m5d,row.m4d,row.m3d,row.m2d,row.m1d]
							,backgroundColor: 'rgba('+(~~(256 * Math.random()))+','+(~~(256 * Math.random()))+','+ (~~(256 * Math.random()))+', 0.8)'
						})
					}
				})
			}
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
				labels    : readdata_summary.value.label.slice(0, -3)
				,datasets : get_graph_data(open_fil.value)
			}

			if(graph_obj){
				graph_obj.destroy()
			}

			graph_obj = new Chart(ctx, {
				type : 'bar'
				,data: graph_data
				,options: {
					indexAxis: 'y',
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

			let ymd = p_ymd.replace("-","")
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
		const summary_totals = computed(() => {
				const totals = {
						m1: 0, m2: 0, m3: 0, m4: 0, m5: 0, m6: 0,
						m7: 0, m8: 0, m9: 0, m10: 0, m11: 0, m12: 0
				};
				if (!readdata_summary.value.data || readdata_summary.value.data.length === 0) {
						return totals;
				}
				const processed_daikoumoku = [];
				readdata_summary.value.data.forEach(row => {
						if (!processed_daikoumoku.includes(row.daikoumoku)) {
								/*
								totals.m1  += Number(row.m1d || 0);
								totals.m2  += Number(row.m2d || 0);
								totals.m3  += Number(row.m3d || 0);
								totals.m4  += Number(row.m4d || 0);
								totals.m5  += Number(row.m5d || 0);
								totals.m6  += Number(row.m6d || 0);
								totals.m7  += Number(row.m7d || 0);
								totals.m8  += Number(row.m8d || 0);
								totals.m9  += Number(row.m9d || 0);
								totals.m10 += Number(row.m10d || 0);
								totals.m11 += Number(row.m11d || 0);
								totals.m12 += Number(row.m12d || 0);
								*/
								totals.m1  += Number(row.total_d || 0);
								totals.m2  += Number(row.average_d || 0);
								totals.m3  += Number(row.m1d || 0);
								totals.m4  += Number(row.m2d || 0);
								totals.m5  += Number(row.m3d || 0);
								totals.m6  += Number(row.m4d || 0);
								totals.m7  += Number(row.m5d || 0);
								totals.m8  += Number(row.m6d || 0);
								totals.m9  += Number(row.m7d || 0);
								totals.m10 += Number(row.m8d || 0);
								totals.m11 += Number(row.m9d || 0);
								totals.m12 += Number(row.m10d || 0);
								totals.m13 += Number(row.m11d || 0);
								totals.m14 += Number(row.m12d || 0);
								processed_daikoumoku.push(row.daikoumoku);
						}
				});
				return totals;
		});
		const upd_special = (p_SEQ) =>{
			//ajax_upd_db_meisai_special.phpをPOSTで呼び出し。POST[SEQ]にp_SEQを設定
			const params = new FormData();
			params.append('SEQ', p_SEQ);
			axios.post("ajax_upd_db_meisai_special.php", params)
				.then((response) => {
					console_log(response.data);
					if (response.data.status === 'success') {
						//alert("更新しました");
						// Optionally, refresh the data after update
						if (pagename.value === "data_summary12m.php") {
							read_db_summary_long();
						} else if (pagename.value === "data_comparison.php") {
							read_db_comparison();
						}
					} else {
						alert("更新できませんでした");
					}
				})
				.catch((error) => {
					console_log('upd_special error');
					console_log(error);
					alert("リターンエラー：更新できませんでした");
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
			summary_totals,
			upd_special,
			special_flg,
		}
	}
});
