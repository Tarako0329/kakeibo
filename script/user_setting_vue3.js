const { createApp, ref, onMounted, onBeforeMount, computed,watch } = Vue;

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
