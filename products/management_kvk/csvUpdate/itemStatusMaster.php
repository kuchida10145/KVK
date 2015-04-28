<?php
	include(dirname(__FILE__) . '/../../system/page/importcsv/ImportCsvItemStatusMaster.php');
	include(dirname(__FILE__) . '/../../system/page/exportcsv/ExportCsvItemStatusMaster.php');

	// セッション
	session_start();

	$importCsv = new ImportCsvItemStatusMaster();

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

	$resultMessage	= "";	// 実行結果
	$errorMessage	= "";	// エラーメッセージ
	$viewFilePath	= "";	// 画面表示用csvファイルパス

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		if(isset($_POST['download_button']) && $_POST['download_button'] == "download") {
			// csv出力処理インスタンス化
			$exportCsv = new ExportCsvItemStatusMaster();
			if($exportCsv->executeExport() && !$importCsv->viewInitial(COLUMN_NAME_STATUS_MASTER_DISP_STATUS, CSV_DOWNLOAD)) {
				$errorMessage	= $importCsv->getErrorMessage();
			}
		} else {
			// csv取込処理インスタンス化
			$filePath		= "";	// csvファイルパス
			$fileName		= "";	// csvファイル名
			$testFlg = false;		// 取込処理フラグ
			// 取込テスト判定（true：取込テスト、false：csv取込）
			if(isset($_POST['test_button']) && $_POST['test_button'] == "test") {
				$testFlg = true;
			}

			$filePath = $_FILES["file"]["tmp_name"];
			$fileName = $_FILES["file"]["name"];

			// csv取込処理実行
			$result = $importCsv->executeImport($filePath, $fileName, $testFlg, $updateFlg = DB_UPDATE);
			// メッセージ取得
			$resultMessage	= $importCsv->getResultMessage($result);
			$errorMessage	= $importCsv->getErrorMessage();
			// 画面表示用csvファイルパス設定
//			$viewFilePath = $_POST['filepath'];
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>管理画面｜アイコンマスタ更新</title>
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
					<small>マスターデータ更新</small>
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
						<li class="active"><a href="#">マスタデータ更新</a></li>
						<li><a href="makePdf.php">データ更新情報</a></li>
						<li><a href="topics.php">トピックス更新</a></li>
					</ul>
				</div>
				<div class="col-md-10">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#">アイコンマスタ</a></li>
						<li><a href="category.php">商品カテゴリマスタ</a></li>
					</ul>
					<form class="form-horizontal well" action="#" method="post" name="form" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="step1" />
						<div class="form-group">
							<label for="upload" class="col-sm-2 control-label">CSVファイル</label>
							<div class="col-sm-10">
								<!-- input[type=file] を非表示にする -->
								<input type="file" class="hidden" id="upload" name="file" value="<?php echo $viewFilePath ?>">
								<!-- 代わりに input[type=text] と ボタンを表示 -->
								<div class="input-group">
									<input type="text" id="filepath" name="filepath" class="form-control" value="<?php echo $viewFilePath ?>" placeholder="item_status_master.csv">
									<span class="input-group-btn">
										<!-- ボタンを押したときに input[type=file] を押したことにする -->
										<a class="btn btn-default" onclick="document.getElementById('upload').click()">ファイル選択</a>
									</span>
								</div>
							</div>
						</div>
						<div align="center">
							<button type="submit" class="btn btn-default" onclick="document.form.submit();" name="test_button" value="test">取込テスト</button>
							<button type="submit" class="btn btn-success"  onclick="document.form.submit();" name="run_button" value="run">CSV 取込</button>
							<button type="submit" class="btn btn-warning"  onclick="document.form.submit();" name="download_button" value="download">CSV ダウンロード</button>
						</div>
						<div>
							実行結果：<?php echo $resultMessage ?>
						</div>
						<div>
							[メッセージ]<br>
							<?php echo $errorMessage ?>
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