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
									<input v-model='from' :disabled='search_disable' @change='read_db_summary_long' type="number" class='form-control' placeholder="期間FROM YYYYMM" style='margin-right:0px;'>
								</div>
								<div class="d-flex" style='padding:5px;'>
									<button class='btn btn-outline-primary lbtn' style='margin-right:5px;' @click='from_back'>前月</button>
									<button class='btn btn-outline-primary rbtn' style='margin-right:30px;' @click='from_next'>次月</button>
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
			<div class='row'>
				<div class='col-12 text-center'>
					<input type='checkbox' id='special_flg' class='form-checkbox' v-model='special_flg' :true-value="1" :false-value="0" @change='read_db_summary_long'>
					<label for='special_flg' class='form-label ms-1 orange'>臨時収入・臨時出費を含む</label>
				</div>
			</div>
			<div class='row' style=''>
				<div :class='[(hanni==="5y")?"col-xl-6":"col-xl-5"]' style='height:auto;display:flex;justify-content: center;'>
					<div style="position:relative;max-width:900px;width:100%;min-height:420px;">
						<canvas id="myChart"></canvas>
					</div>
				</div>
				<div :class='[(hanni==="5y")?"col-xl-6":"col-xl-7","pe-4"]'>
					<div style='height:calc(100vh - 120px);overflow: auto;'>
					<table class="table table-hover table-sm" style='table-layout: auto;min-width:800px;' id='meisai_table'>
							<thead class='sticky-top'>
									<tr class="table-info">
											<th scope="col" class='text-center sticky-left' style='width:150px;'>大中項目</th>
											<template v-for="(label,index) in readdata_summary.label" >
												<th v-if='index>=5 && hanni==="5y"' scope="col" class='text-center table-primary'>{{label}}</th>
												<th v-else-if='index>=12 && hanni==="12m"' scope="col" class='text-center table-primary'>{{label}}</th>
												<th v-else scope="col" class='text-center'>{{label}}</th>
											</template>
									</tr>
							</thead>
							<tbody class="table-group-divider">
									<template v-for='(list,index) in readdata_summary.data' :key="list.sort+list.chuukoumoku">
											<template v-if='index===0 || (index!==0 && list["daikoumoku"]!==readdata_summary.data[index -1]["daikoumoku"])'>
													<tr :class="['table-info']" role='button' @click='open_utiwake(list.daikoumoku)'>
															<td class='sticky-left' style='width:100px;'>{{list["daikoumoku"]}}</td>
															<td v-for="(label, i) in readdata_summary.label" :class="['text-end','pe-2',{ 'table-primary': ((i >= 5 ) && hanni == '5y') || ((i >= 12 ) && hanni == '12m') }]" style='position:relative;'>
															<template v-if="i == 5 && hanni == '5y'">
																	<i v-if="list.d_color == 'Red'" class="bi bi-arrow-down-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color == 'Blue'" class="bi bi-arrow-up-left pe-2 text-success" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color == 'uRed'" class="bi bi-arrow-up-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color == 'dBlue'" class="bi bi-arrow-down-left pe-2 text-success" style='position:absolute;left:20px;'></i>
																</template>
																<template v-if="i == 7 && hanni == '5y'">
																	<i v-if="list.d_color2 == 'Red'" class="bi bi-arrow-down-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color2 == 'Blue'" class="bi bi-arrow-up-left pe-2 text-success" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color2 == 'uRed'" class="bi bi-arrow-up-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
																	<i v-if="list.d_color2 == 'dBlue'" class="bi bi-arrow-down-left pe-2 text-success" style='position:absolute;left:20px;'></i>
																</template>
																<template v-if="i <= 11">
																	<span v-if="list['m'+(12-i)+'dflg']==0" >{{Number(list['m'+(12-i)+'d']).toLocaleString()}}</span>
																	<span v-else class='orange'><strong>{{Number(list['m'+(12-i)+'d']).toLocaleString()}}</strong></span>
																	<template v-if="(i == 5 ||i == 7) && hanni == '5y'">
																		<span v-if="list['m'+(12-i)+'dflg']==0"><small> 万</small></span>
																		<span v-else class='orange'><small> 万</small></span>
																	</template>
																</template>
																<template v-else-if="i === 12">
																	<span v-if="list['average_dflg']==0" >{{Number(list['average_d']).toLocaleString()}}</span>
																	<span v-else class='orange'><strong>{{Number(list['average_d']).toLocaleString()}}</strong></span>
																</template>
																<template v-else-if="i === 13">
																	<span v-if="list['total_dflg']==0" >{{Number(list['total_d']).toLocaleString()}}</span>
																	<span v-else class='orange'><strong>{{Number(list['total_d']).toLocaleString()}}</strong></span>
																</template>
															</td>
													</tr>
											</template>
											<tr v-if='list.daikoumoku===open_fil' class='fadein'>
												<td class='sticky-left' style='width:100px;'>　{{list["chuukoumoku"]}}</td>
												<td v-for="(label, i) in readdata_summary.label" class='text-end pe-2' role='button' @click='get_meisai(label,list["daikoumoku"],list["chuukoumoku"])' style='position:relative;'>
													<template v-if="i == 5 && hanni == '5y'">
														<i v-if="list.c_color == 'Red'" class="bi bi-arrow-down-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color == 'Blue'" class="bi bi-arrow-up-left pe-2 text-success" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color == 'uRed'" class="bi bi-arrow-up-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color == 'dBlue'" class="bi bi-arrow-down-left pe-2 text-success" style='position:absolute;left:20px;'></i>
													</template>
													<template v-if="i == 7 && hanni == '5y'">
														<i v-if="list.c_color2 == 'Red'" class="bi bi-arrow-down-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color2 == 'Blue'" class="bi bi-arrow-up-left pe-2 text-success" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color2 == 'uRed'" class="bi bi-arrow-up-left pe-2 text-danger" style='position:absolute;left:20px;'></i>
														<i v-if="list.c_color2 == 'dBlue'" class="bi bi-arrow-down-left pe-2 text-success" style='position:absolute;left:20px;'></i>
													</template>
													<template v-if="i <= 11">
														<span v-if="list['m'+(12-i)+'sflg']==0" >{{Number(list['m'+(12-i)+'c']).toLocaleString()}}</span>
														<span v-else class='orange'><strong>{{Number(list['m'+(12-i)+'c']).toLocaleString()}}</strong></span>
														<template v-if="(i == 5 ||i == 7) && hanni == '5y'">
															<span v-if="list['m'+(12-i)+'sflg']==0"><small> 万</small></span>
															<span v-else class='orange'><small> 万</small></span>
														</template>
													</template>
													<template v-else-if="i === 12">
														<span v-if="list['average_sflg']==0" >{{Number(list['average']).toLocaleString()}}</span>
														<span v-else class='orange'><strong>{{Number(list['average']).toLocaleString()}}</strong></span>
													</template>
													<template v-else-if="i === 13">
														<span v-if="list['total_sflg']==0" >{{Number(list['total']).toLocaleString()}}</span>
														<span v-else class='orange'><strong>{{Number(list['total']).toLocaleString()}}</strong></span>
													</template>
												</td>
											</tr>
									</template>
							</tbody>
							<tfoot class='sticky-bottom'>
									<tr class='table-success'>
											<td class='text-center sticky-left' style='width:100px;'>合計</td>
											<template v-if='hanni==="5y"'>
												<template v-for="(label, i) in readdata_summary.label">
													<td v-if="i !== 5 && i !== 7" :class="['text-end','pe-2',{'table-primary':(i===6)}]" :style="{color: Number(summary_totals['m'+(12-i)]) > 0 ? 'blue' : (Number(summary_totals['m'+(12-i)]) < 0 ? 'red' : '')}">
														{{Number(summary_totals['m'+(12-i)]).toLocaleString()}}
													</td>
													<td v-else-if="i === 5" class='text-end pe-2 table-primary' :style="{color: Number(summary_totals['m8'])-Number(summary_totals['m9']) > 0 ? 'blue' : (Number(summary_totals['m8'])-Number(summary_totals['m9'])  < 0 ? 'red' : '')}">
														{{(Number(summary_totals['m8']) - Number(summary_totals['m9'])).toLocaleString()}}
													</td>
													<td v-else class='text-end pe-2 table-primary' :style="{color: Number(summary_totals['m8'])-Number(summary_totals['m6']) > 0 ? 'blue' : (Number(summary_totals['m8'])-Number(summary_totals['m6'])  < 0 ? 'red' : '')}">
														{{(Number(summary_totals['m8']) - Number(summary_totals['m6'])).toLocaleString()}}
													</td>
												</template>
											</template>
											<template v-if='hanni==="12m"'>
												<td v-for="(label, i) in readdata_summary.label" class='text-end pe-2' :style="{color: Number(summary_totals['m'+(14-i)]) > 0 ? 'blue' : (Number(summary_totals['m'+(14-i)]) < 0 ? 'red' : '')}">
													{{Number(summary_totals['m'+(14-i)]).toLocaleString()}}
												</td>
											</template>
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
												<th>特</th>
												<th>日付</th>
												<th>入出金元</th>
												<th>明細</th>
												<th>金額</th>
												<th>メモ</th>
											</tr>
										</thead>
										<tbody>
											<tr v-for='(list,index) in popup_meisai' :key='list.date+list.guid'>
												<td><input type='checkbox' class='form-check' v-model='list.Special' :true-value="1" :false-value="0" @click='upd_special(list.SEQ)'></td>
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
		
		<!--<script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>-->
		<script src="script/summary_bunseki_vue3.js?<?php echo $time; ?>"></script>
		<script>
			summary_bunseki('<?php echo basename(__FILE__);?>').mount('#app');
		</script>
	</BODY>
	</html>