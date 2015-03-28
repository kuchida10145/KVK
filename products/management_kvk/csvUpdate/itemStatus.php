<?php
	include_once('../../system/page/importcsv/ImportCsvItemStatus.php');

	// セッション
	session_start();

	$resultMessage	= "";	// 実行結果
	$errorMessage	= "";	// エラーメッセージ
	$viewFilePath	= "";	// 画面表示用csvファイルパス

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		// csv取込処理インスタンス化
		$importCsv = new ImportCsvItemStatus();
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
		$result = $importCsv->executeImport($filePath, $fileName, $testFlg);
		// メッセージ取得
		$resultMessage	= $importCsv->getResultMessage($result);
		$errorMessage	= $importCsv->getErrorMessage();
		// 画面表示用csvファイルパス設定
		$viewFilePath = $_POST['filepath'];
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Navs</title>

		<!-- Bootstrap -->
		<link href="../../system/style_code/css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div  class="container">
		<!-- Tabs -->
			<section>
				<h1>KVK 管理画面（商品ステータス）</h1>
				<div>
					<ul class="nav nav-tabs">
						<li><a href="itemCSV.php">商品データ</a></li>
						<li><a href="itemStatusMaster.php">商品ステータスマスタ</a></li>
						<li class="active"><a href="#">商品ステータス</a></li>
						<li><a href="category.php">カテゴリデータ</a></li>
						<li><a href="parts.php">部品データ</a></li>
					</ul>
				</div>
				<div>
					<form class="form-horizontal well" action="#" method="post" name="form" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="step1" />
						<div class="form-group">
							<label for="upload" class="col-sm-2 control-label">商品ステータスCSVファイル</label>
							<div class="col-sm-10">
								<!-- input[type=file] を非表示にする -->
								<input type="file" class="hidden" id="upload" name="file" value="<?php echo $viewFilePath ?>">
								<!-- 代わりに input[type=text] と ボタンを表示 -->
								<div class="input-group">
									<input type="text" id="filepath" name="filepath" class="form-control" value="<?php echo $viewFilePath ?>">
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
							<!-- <button type="button" class="btn btn-warning">CSV ダウンロード</button> -->
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
			</section>
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