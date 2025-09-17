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
			.form-control,.form-select{
				display:inline-block;
				width: 150px;
			}
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
									<a class='nav-link' href='data_comparison.php'>前月比・前年比</a>
								</li>
								<li class='nav-item'>
									<a class='nav-link active' href='data_summary12m.php'>推移(月間・年間)</a>
								</li>
							</ul>
							<div class="d-flex" style='padding:5px;'>
								<input v-model='from' :disabled='search_disable' @change='read_db_summary_long' type="number" class='form-control' placeholder="期間FROM YYYYMM" style='margin-right:10px;'>
							</div>
							<div class="d-flex" style='padding:5px;'>
								<button class='btn btn-outline-primary lbtn' style='margin-right:5px;' @click='from_back'>前月</button>
								<button class='btn btn-outline-primary rbtn' style='margin-right:50px;' @click='from_next'>次月</button>
							</div>
							<div class="d-flex" style='padding:5px;'>
								<select v-model='hanni' class='form-select' @change='read_db_summary_long'>
									<option value='12m'>過去１２ヵ月</option>
									<option value='5y'>過去５年</option>
								</select>
							</div>
							<div class="d-flex" style='padding:5px;'>
								<a href="user_setting.php" class='a_none'><h2><i class="bi bi-person-circle"></i></h2></a>
							</div>
						</div>
					</div>
				</nav>
				</div>
			</div>
			</div>
		</HEADER>


		<MAIN class='container-fluid' style='color:#fff;'>
		<div class='row' style=''>
			<div class='col-xl-6' style='height:calc(100vh - 110px);display:flex;justify-content: center;'>
				<div style="position:relative;max-width:900px;width:90%;height:420px;">
					<canvas id="myChart"></canvas>
				</div>
			</div>
			<div class='col-xl-6'>
				<div style='height:calc(100vh - 110px);overflow: auto;'>
				<table class="table table-hover table-sm" style='table-layout: auto;min-width:800px;' id='meisai_table'>
						<thead class='sticky-top'>
								<tr class="table-info">
										<th scope="col" class='text-center sticky-left' style='width:150px;'>大中項目</th>
										<th v-for="label in readdata_summary.label" scope="col" class='text-center'>{{label}}</th>
								</tr>
						</thead>
						<tbody>
								<template v-for='(list,index) in readdata_summary.data' :key="list.sort+list.chuukoumoku">
										<template v-if='index===0 || (index!==0 && list["daikoumoku"]!==readdata_summary.data[index -1]["daikoumoku"])'>
												<tr class="table-info" role='button' @click='open_utiwake(list.daikoumoku)'>
														<td class='sticky-left' style='width:100px;'>{{list["daikoumoku"]}}</td>
														<td v-for="(label, i) in readdata_summary.label" class='text-end'>{{Number(list['m'+(12-i)+'d']).toLocaleString()}}</td>
												</tr>
										</template>
										<tr v-if='list.daikoumoku===open_fil' class='fadein'>
												<td class='sticky-left' style='width:100px;'>　{{list["chuukoumoku"]}}</td>
												<td v-for="(label, i) in readdata_summary.label" class='text-end' role='button' @click='get_meisai(label,list["daikoumoku"],list["chuukoumoku"])'>{{Number(list['m'+(12-i)+'c']).toLocaleString()}}</td>
										</tr>
								</template>
						</tbody>
						<tfoot class='sticky-bottom'>
								<tr class='table-success'>
										<td class='text-center sticky-left' style='width:100px;'>合計</td>
										<td v-for="(label, i) in readdata_summary.label" class='text-end'>{{Number(summary_totals['m'+(12-i)]).toLocaleString()}}</td>
								</tr>
						</tfoot>
				</table>
				</div>
			</div>
		</div>
		<div class='logoff'><a href="logoff.php" class='a_none'><h1><i class="bi bi-box-arrow-right"></i></h1></a></div>
		<button type='button' style='display: none;' data-bs-toggle='modal' data-bs-target='#meisai_modal' id="meisai_modal_btn"></button>
		</MAIN>
    <div class='modal fade' id='meisai_modal' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
			<div class='modal-dialog  modal-dialog-centered modal-dialog-scrollable'>
				<div class='modal-content edit' style=''>
					<div class='modal-header'>
	      		<h5 class="modal-title">{{modal_label}}</h5>
  	    		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class='modal-body container'>
              <div class='row'>
                <table class="table table-sm">
                  <thead>
                    <tr>
                    <th>日付</th>
                    <th>入出金元</th>
                    <th>明細</th>
                    <th>金額</th>
                    <th>メモ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for='(list,index) in popup_meisai' :key='list.date+list.guid'>
                    <td>{{list.date}}</td>
                    <td>{{list.shuppimoto}}</td>
                    <td>{{list.meisai}}</td>
                    <td class='text-end'>{{Number(list.kin).toLocaleString()}}</td>
                    <td>{{list.memo}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
					</div>
					<div class='modal-footer'>
					</div>
				</div>
			</div>
		</div>	</div>
	
	<script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
	<script>
		summary_bunseki('<?php echo basename(__FILE__);?>').mount('#app');
	</script>
</BODY>
</html>