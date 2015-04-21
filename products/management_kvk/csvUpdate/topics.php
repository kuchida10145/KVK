<?php
	include_once(dirname(__FILE__) . '/../../system/page/importcsv/ImportCsvItemStatus.php');
	include_once(dirname(__FILE__) . '/../../system/page/exportcsv/ExportCsvItemStatus.php');
	include_once(dirname(__FILE__) . '/../../class.topics.php');

	// セッション
	session_start();

	// csv取込処理インスタンス化
	$importCsv = new ImportCsvItemStatus();
	$exportCsv = new ExportCsvItemStatus();
	$cls_topics = new Topics();

	// ログインチェック
	if(!$importCsv->loginCheck()) {
		header('location: ../login/login.php');
		$_SESSION['message'] = "ログインを行ってください。";
	}

	// ログアウトボタン押下時
	if(isset($_POST['logout_button']) && $_POST['logout_button'] == "logout") {
		if($importCsv->logOut()) {
			header('location: ../login/login.php');
		}
	}

	$topicsList = $cls_topics->getTopicsData(); // トピックスデータ

	$resultMessage	= "";	// 実行結果
	$errorMessage	= "";	// エラーメッセージ
	$viewFilePath	= "";	// 画面表示用csvファイルパス

	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		if(isset($_POST['update_button']) && $_POST['update_button'] == "update") {
			$deleteFlg = false;
			foreach($topicsList as $key=>$value) {
				if(isset($_POST[$key]) && $_POST[$key] == 1) {
					$deleteFlg = true;
				} else {
					$deleteFlg = false;
				}
				if($deleteFlg) {
					$result = $cls_topics->reloadTopics($deleteFlg, $value['id']);
				} else {
					$result = $cls_topics->reloadTopics($deleteFlg, $value['id'], $_POST["date_".$key], $_POST["text_".$key], $_POST["link_".$key]);
				}
			}
		}

		if(isset($_POST['regist_button']) && $_POST['regist_button'] == "regist") {
			if(trim($_POST['new_date']) != "" || trim($_POST['new_text']) != "" || trim($_POST['new_link']) != "") {
				// 登録
				$result = $cls_topics->registTopics($_POST['new_date'], $_POST['new_text'], $_POST['new_link']);
			} else {
				$errorMessage	= "登録するデータがありません。";	// エラーメッセージ
			}
		}

		if(!$result) {
			$errorMessage	= "トピックスの更新に失敗しました。<br>";
		}

		header('location: topics.php');
	}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>管理画面｜商品機能アイコン変更</title>
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
		<div  class="container">
			<div class="page-header">
				<h1>WEBサイト管理
					<small>トピックス更新</small>
				</h1>
			</div>
			<div align="right">
				<form action="#" method="post" name="form">
					<button type="submit" class="btn btn-info" onclick="document.form.submit();" name="logout_button" value="logout">ログアウト</button>
				</form>
			</div>
			<div class="row">
				<div class="col-md-2">
					<ul class="nav nav-pills nav-stacked">
						<li><a href="itemCsv.php">商品情報更新</a></li>
						<li><a href="itemStatusMaster.php">マスタデータ更新</a></li>
						<li><a href="makePdf.php">データ更新情報</a></li>
						<li class="active"><a href="#">トピックス更新</a></li>
					</ul>
				</div>
				<div class="col-md-10">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#">トピックス</a></li>
					</ul>
					<form class="form-horizontal well" action="#" method="post" name="form" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="step1" />
						<h4>現在のトピックス</h4>
						<table class="table">
							<thead>
								<tr><th>削除</th><th>日付</th><th>トピックス</th><th>リンク</th></tr>
							</thead>
							<tbody>
							<?php if( isset($topicsList)) : ?>
								<?php foreach ( $topicsList as $key=>$value) : ?>
									<tr class="filters">
										<th width="50" align="center"><input type="checkbox" value="1" name="<?php echo $key ?>" id="<?php echo $key ?>"></th>
										<th width="150"><input type="text" class="form-control" value="<?php echo $value['regist_date'] ?>" name="<?php echo "date_".$key ?>" id="<?php echo "date_".$key ?>"></th>
										<th><input type="text" class="form-control" value="<?php echo $value['text'] ?>" name="<?php echo "text_".$key ?>" id="<?php echo "text_".$key ?>"></th>
										<th width="290"><input type="text" class="form-control" value="<?php echo $value['link'] ?>" name="<?php echo "link_".$key ?>" id="<?php echo "link_".$key ?>"></th>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr><th>なし</th><th></th><th></th><th></th></tr>
							<?php endif; ?>
							</tbody>
						</table>
						<?php if( isset($topicsList)) : ?>
						<div align="center">
							<button type="submit" class="btn btn-warning"  onclick="document.form.submit();" name="update_button" value="update">更新</button>
						</div>
						<?php endif; ?>
						<h4>新規トピックス</h4>
						<table class="table">
							<thead>
								<tr><th>日付</th><th>トピックス</th><th>リンク</th></tr>
							</thead>
							<tbody>
								<tr class="filters">
									<th width="150"><input type="text" class="form-control" value="" name="new_date" id="new_date"></th>
									<th><input type="text" class="form-control" value="" name="new_text" id="new_text"></th>
									<th width="290"><input type="text" class="form-control" value="" name="new_link" id="new_link"></th>
								</tr>
							</tbody>
						</table>

						<div align="center">
							<button type="submit" class="btn btn-success"  onclick="document.form.submit();" name="regist_button" value="regist">登録</button>
						</div>
						<div>
							実行結果：<?php echo $resultMessage ?>
						</div>
						<div>
							[メッセージ]<br>
							<FONT color="red"><label><?php echo $errorMessage ?></label></FONT>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script>
			/* input[type=file] が変更されたら input[type=text] に反映する */
			$('input[id=upload]').on('change', function() {
			$('#filepath').val($(this).val());
			});
		</script>
    </body>
</html>