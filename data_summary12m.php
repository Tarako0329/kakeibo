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


		<!--<MAIN class='container-fluid' style='color:#fff;padding-left:60px;padding-right:60px;'>-->
		<MAIN class='container' style='color:#fff;'>
		<div class='row' style='min-height:240px;'>
			<div class='col-xl-12' style='height:100%;display:flex;justify-content: center;'>
				<div style="position:relative;max-width:900px;width:90%;height:100%;">
					<canvas id="myChart"></canvas>
				</div>
			</div>
		</div>
		<div class='row' style='height:60%;'>
			<div class='col-xl-12' style='height:100%;'>
				<div class="table-responsive " style='width:100%;height:100%;'>
					<table class="table table-hover table-sm" style='min-width:800px;'>
						<thead class='sticky-top'>
							<tr class="table-info">
								<th scope="col" class='text-center sticky-left'>大中項目</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[0]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[1]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[2]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[3]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[4]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[5]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[6]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[7]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[8]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[9]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[10]}}</th>
								<th scope="col" class='text-center'>{{readdata_summary.label[11]}}</th>
							</tr>
						</thead>
						<tbody>
							<template v-for='(list,index) in readdata_summary.data' :key="list.sort+list.chuukoumoku">
								<template v-if='index===0 || (index!==0 && list["daikoumoku"]!==readdata_summary.data[index -1]["daikoumoku"])'>
									<tr class="table-info" role='button' @click='open_utiwake(list.daikoumoku)'>
										<td class='sticky-left'>{{list["daikoumoku"]}}</td>
										<td class='text-end'>{{Number(list.m12d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m11d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m10d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m9d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m8d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m7d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m6d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m5d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m4d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m3d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m2d).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m1d).toLocaleString()}}</td>
									</tr>
									<!--<tr v-if='list.daikoumoku===open_fil' class='fadein'>
										<td class='sticky-left'>　{{list["chuukoumoku"]}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[0],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m12c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m11c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m10c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m9c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m8c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m7c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m6c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m5c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m4c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m3c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m2c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m1c).toLocaleString()}}</td>
									</tr>-->
								</template>
								<!--<template v-if='index!==0 && list["daikoumoku"]===readdata_summary.data[index -1]["daikoumoku"]'>
									<tr v-if='list.daikoumoku===open_fil' class='fadein'>
										<td class='sticky-left'>　{{list["chuukoumoku"]}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[0],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m12c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m11c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m10c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m9c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m8c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m7c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m6c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m5c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m4c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m3c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m2c).toLocaleString()}}</td>
										<td class='text-end'>{{Number(list.m1c).toLocaleString()}}</td>
									</tr>
								</template>-->
								<tr v-if='list.daikoumoku===open_fil' class='fadein'>
										<td class='sticky-left'>　{{list["chuukoumoku"]}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[0],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m12c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[1],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m11c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[2],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m10c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[3],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m9c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[4],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m8c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[5],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m7c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[6],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m6c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[7],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m5c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[8],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m4c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[9],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m3c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[10],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m2c).toLocaleString()}}</td>
										<td class='text-end' @click='get_meisai(readdata_summary.label[11],list["daikoumoku"],list["chuukoumoku"])'>{{Number(list.m1c).toLocaleString()}}</td>
									</tr>
							</template>
						</tbody>
						<!--
						<tfoot class='sticky-bottom'>
							<tr class='table-success'>
								<td class='text-center'>入出金計</td>
								<td class='text-end'>{{Number(comparison_sum_val["zen"]).toLocaleString()}}</td>
								<td class='text-end'>{{Number(comparison_sum_val["tou"]).toLocaleString()}}</td>
								<td class='text-end'>{{Number(comparison_sum_val["sa"]).toLocaleString()}}</td>
							</tr>
						</tfoot>
						-->
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
                <table class="table">
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
                    <td>{{list.kin}}</td>
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