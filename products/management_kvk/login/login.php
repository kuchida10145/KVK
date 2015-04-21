<?php
	include(dirname(__FILE__) . '/../../system/page/login/PageLogin.php');
	// セッション
	session_start();

	$pageLogin = new PageLogin();

	$errorMessage = "";

	if (!empty($_SESSION['message'])) {
		$errorMessage = $_SESSION['message'];
	}

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		$result = "";
		$result = $pageLogin->passWordCheck($_POST['username'], $_POST['password']);

		if($result){
			// セッション保存
			if (empty($_SESSION['login'])) {
				$_SESSION['login'] = $_POST['username'];
			}
			header('location: ../csvUpdate/itemCsv.php');

		} else {
			// エラーメッセージ取得
			$errorMessage	= $pageLogin->getErrorMessage();
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>管理画面｜ログイン</title>
		<!-- Bootstrap -->
			<link href="../../system/style_code/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>

	<body>
		<div class="container">
			<div class="row-fluid">
				<div class="col-md-12">
					<div class="well login-box">
						<form action="#" method="post" name="form">
						<input type="hidden" name="mode" value="step1" />
							<legend>ログイン</legend>
							<div class="form-group">
								<label for="username">ユーザ名</label>
								<input id="username" name="username" placeholder="ユーザ名" type="text" class="form-control" />
							</div>
							<div class="form-group">
								<label for="password">パスワード</label>
								<input id="password" name="password" placeholder="パスワード" type="password" class="form-control" />
							</div>
							<div class="form-group text-center">
								<input type="submit" class="btn btn-success btn-login-submit" onclick="document.form.submit();" value="ログイン" />
							</div>
							<div>
								[メッセージ]<br>
								<FONT color="red"><label><?php echo $errorMessage ?></label></FONT>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</body>
</html>