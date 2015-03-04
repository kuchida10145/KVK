<?php
	include_once('../../system/controller/page/importcsv/ImportCsvCategory.php');

	// セッション
	session_start();

	$message = "";

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		$importCsv = new ImportCsvCategory();
		$csvFile = $_FILES["file"]["tmp_name"];
		$result = $importCsv->executeImport($csvFile);
		if (!result) {
			$message = $importCsv->getHtmlErrorMessage();
		}
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
				<h1>KVK 管理画面</h1>
				<div>
					<ul class="nav nav-tabs">
						<li><a href="admin_item.php">商品データ</a></li>
						<li class="active"><a href="#">カテゴリデータ</a></li>
						<li><a href="admin_parts.php">部品データ</a></li>
					</ul>
				</div>
				<div>
					<form class="form-horizontal well" action="admin_category.php" method="post" name="form" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="step1" />
						<div class="form-group">
							<label for="upload" class="col-sm-2 control-label">CSVファイル</label>
							<div class="col-sm-10">
								<!-- input[type=file] を非表示にする -->
								<input type="file" class="hidden" id="upload" name="file">
								<!-- 代わりに input[type=text] と ボタンを表示 -->
								<div class="input-group">
									<input type="text" id="filepath" name="filepath" class="form-control">
									<span class="input-group-btn">
										<!-- ボタンを押したときに input[type=file] を押したことにする -->
										<a class="btn btn-default" onclick="document.getElementById('upload').click()">ファイル選択</a>
									</span>
								</div>
							</div>
						</div>
						<div align="center">
							<button type="button" class="btn btn-default" onclick="document.form.submit();">CSV 取込</button>
							<!-- <button type="button" class="btn btn-success">Success</button> -->
							<!-- <button type="button" class="btn btn-warning">CSV ダウンロード</button> -->
						</div>
						<div>
							実行結果：<?php echo $result_view ?>
						</div>
						<div>
							[メッセージ]<br>
							<?php echo $importCsv->Message ?>
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