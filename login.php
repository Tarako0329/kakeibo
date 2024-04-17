<?php
	require "php_header.php";
	if(empty($_SESSION["MSG"])){
		$_SESSION["MSG"]="初めて利用する方はメールアドレスとパスワードを設定し、新規登録ボタンを押してください";
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
		<HEADER class='container text-center' style='color:#FFA400'>
				<h1 class='potta-one-regular' ><?php echo $title;?></h1>
		</HEADER>
		<MAIN class='container' style='color:#fff;' id='app'>
			<div class='row'>
				<div v-if="msg!==''" class="alert alert-warning" role="alert">
					{{msg}}
				</div>

				<form action='login_sql.php' method="POST">
					<div class='col-lg-6 col-10'>
						<label for='mail' class='form-label'>メールアドレス</label>
						<input type='mail' class='form-control' name='mail' id='mail' value='<?php echo $id;?>' maxlength='200' required>
					</div>
					<div class='col-lg-6 col-10'>
						<label for='pass' class='form-label'>パスワード</label>
						<input type='password' class='form-control' name='pass' id='pass' value='<?php echo $pass;?>' maxlength='10' required>
					</div>
					<div class='row mt-5'>
						<div class='col-lg-3 col-5'><button type='submit' class='btn btn-primary btn-lg' name='login' value='login'>ログイン</button></div>
						<div class='col-lg-3 col-5'><button type='submit' class='btn btn-primary btn-lg' name='login' value='newlogin'>新規登録</button></div>
					</div>
					<div class='row mt-5'>
						<div class='col-lg-3 col-5'><button type='submit' class='btn btn-primary btn-lg' name='login' value='setpass'>パスワード変更</button></div>
						<small>メールアドレスを入力して「パスワード変更」のボタンを押すとパスワード変更のURLがメールで送付されます。</small>
					</div>
				</form>
			</div>
		</MAIN>
		<!--<FOOTER>
		</FOOTER>-->
		<script>
			const { createApp, ref, onMounted, computed } = Vue;
			createApp({
				setup() {
					const msg = ref('<?php echo $_SESSION["MSG"];?>')
					onMounted(()=>{

					})

					return{
						msg,
					}
				}
			}).mount('#app');
		</script>
</BODY>
</html>
<?php
	$_SESSION["MSG"]="";
?>