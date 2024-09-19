<?php
	if(!empty($_GET["val"])){
		session_start();
		$_SESSION = [];
		setCookie("mesp_uid", "", -1, "/", "",true,true);
		$_SESSION["mail"] = openssl_decrypt(hex2bin($_GET["val"]), "AES-128-ECB", "1");
	}
	require "php_header.php";
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
	<?php 
	//共通部分、bootstrap設定、フォントCND、ファビコン等
	include "head_bs5.php" 
	?>
	<TITLE><?php echo $title;?></TITLE>
	<style>
		html,body,main{
			height:auto;
		}
		.dragging{
			background-color:#FFA400;
		  opacity: 0.5;
		}
	</style>
	
</head>
<BODY id = 'body' style='background:black;' >
	<div id='app'>
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
											<a class='nav-link' href='data_summary12m.php'>推移(月間・年間)</a>
										</li>
									</ul>
									<div class="d-flex" style='padding:5px;'>
									</div>
									<div class="d-flex" style='padding:5px;'>
										<a href="user_setting.php" class='a_none'><h2><i class="bi bi-person-circle" style='color:#d9ff00bd'></i></h2></a>
									</div>
								</div>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</HEADER>
		<MAIN class='container' style='color:#fff;'>
			<ul class="nav nav-tabs">
			  <li class="nav-item">
			    <a role='button' class="nav-link active" aria-current="page" @click='page_changer("setting")' id='setting'>設定</a>
			  </li>
			  <li class="nav-item">
			    <a role='button' class="nav-link " @click='page_changer("master")' id='master'>分類マスタ</a>
			  </li>
			</ul>

			<div v-if='page==="setting"'>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<label for='mail' class='form-label'>メールアドレス</label>
						<input type='mail' id='mail' class='form-control' v-model='mail'>
					</div>
				</div>
				<hr>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<input type='checkbox' id='pass_hen' class='form-checkbox' v-model='pass_hen'>
						<label for='pass_hen' class='form-label'>パスワードを変更する</label>
					</div>
					<div class='row mt-1 ps-5'>
						<div class='col-lg-6 col-xl-5'>
							<label for='pass' class='form-label'>パスワード</label>
							<input type='text' id='pass' class='form-control' v-model='pass'>
						</div>
					</div>
					<div class='row mt-1 ps-5'>
						<div class='col-lg-6 col-xl-5'>
							<label for='pass2' class='form-label'>パスワード(再)</label>
							<input type='password' id='pass2' class='form-control' v-model='pass2'>
							<small v-show="pass!==pass2" style='color:red;'>パスワード不一致</small>
						</div>
					</div>
				</div>
				<hr>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<label for='kisanbi' class='form-label'>集計期間の設定</label>
						<input type='number' id='kisanbi' class='form-control' v-model='kisanbi'>
						<small>日から1ヵ月を集計する。</small>
					</div>
				</div>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<label for='shukuzitu' class='form-label'>月度起算日が土日祝の場合</label>
						<select id='shukuzitu' class='form-select' v-model='shukuzitu'>
							<option value="0">そのまま適用する</option>
							<option value="1">直前の平日を起算日とする（金曜日等）</option>
							<option value="2">直後の平日を起算日とする（月曜日等）</option>
						</select>
					</div>
				</div>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<label for='nendomatu' class='form-label'>年度末月</label>
						<input type='number' id='nendomatu' class='form-control' v-model='nendomatu'>
					</div>
				</div>
				<div class='row mt-3'>
					<div class='col-lg-6 col-xl-5'>
						<button type='button' class='btn btn-outline-primary' @click='onsubmit'>登　録</button>
					</div>
				</div>
			</div><!--setting-->

			<div v-if='page==="master"'>
				<div class='row mt-3 ps-5 pe-5'>
					<table class='table table-sm table-hover'>
						<thead>
							<tr>
								<th>#</th>
								<th>項目名</th>
								<th>-</th>
							</tr>
						</thead>
						<tbody>
							<template v-for='(list,index) in bunrui_ms_sort' :key='list.daikoumoku'>
								<tr role='button' draggable="true" @dragstart='move_recorde($event,index)' @dragenter='moving_in' @dragleave='moving_out' @dragover='$event.preventDefault()' @drop='dorpping($event,list.sort)'>
									<td>{{index}}</td>
									<td>{{list.daikoumoku}}</td>
									<td><i class="bi bi-trash3" role='button' @click='bunrui_ms_del(index)'></i></td>
								</tr>
							</template>
							<tr>
								<td>追加</td>
								<td>
									<div class="input-group">
										<input type='text' v-model='bunrui_ms_new' class='form-control form-control-sm' aria-describedby="button-addon2">
										<button class="btn btn-outline-secondary p-0" type="button" id="button-addon2" @click='bunrui_ms_add'>追加</button>
									</div>
								</td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class='row mt-3'>
				<div class='col-lg-4 col-xl-3'>
						<button type='button' class='btn btn-outline-primary' @click='onsubmit_ms'>登　録</button>
					</div>
					<div class='col-lg-4 col-xl-3'>
						<button type='button' class='btn btn-outline-primary' @click='get_user'>元の戻す</button>
					</div>
				</div>

			</div>
			<div class='logoff'><a href="logoff.php" class='a_none'><h1><i class="bi bi-box-arrow-right"></i></h1></a></div>
		</MAIN>
		<div class="loader-wrap" v-show='loader'>
		  <div class="loader">Loading...</div>
	  </div>
	</div>
	
	<script src="script/dataset_vue3.js?<?php echo $time; ?>"></script>
	<script>
		user_setting('<?php echo $_SESSION["mail"];?>').mount('#app');
	</script>

</BODY>
</html>