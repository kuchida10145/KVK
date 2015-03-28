<?php
	include_once('../../system/page/makepdf/GetMakePDFData.php');

	// セッション
	session_start();

	$today			= date("Y/m/d");
	$resultMessage	= "";	// 実行結果
	$errorMessage	= "";	// エラーメッセージ

	// インスタンス化
	$makePdf = new CommonMakePDF();
	$viewArray = $makePdf->getViewData();

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		$result = false;

		// pdf作成時間解除（true：時間設定解除）
		if(isset($_POST['unset_button']) && $_POST['unset_button'] == "unset") {
			$result = $makePdf->unsetSystemStatus($viewArray[COLUMN_NAME_SYSTEM_STATUS]);
		}

		// pdf作成時間設定（true：時間設定）
		if(isset($_POST['set_button']) && $_POST['set_button'] == "set") {
			$result = $makePdf->setPdfTime($_POST['dropdownDay'], $_POST['hour'], $_POST['min']);
			if($result) {
				$result = $makePdf->setSystemStatus($viewArray[COLUMN_NAME_SYSTEM_STATUS]);
			}
		}

		// pdf作成中止（true：pdf作成中止）
		if(isset($_POST['stop_button']) && $_POST['stop_button'] == "stop") {
			$result = $makePdf->stopSystemStatus($viewArray[COLUMN_NAME_SYSTEM_STATUS]);
		}

		// メッセージ取得
		$resultMessage	= $makePdf->getResultMessage($result);
		$errorMessage	= $makePdf->getErrorMessage();
	}

	// 画面表示データ取得
	$viewArray = $makePdf->getViewData();
	$systemStatus = $makePdf->convertSystemStatus($viewArray[COLUMN_NAME_SYSTEM_STATUS]);
	$startTime = $viewArray[COLUMN_NAME_PDF_TIME];
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
				<h1>KVK 管理画面（PDF設定）</h1>
				<div>
					<ul class="nav nav-tabs">
						<li><a href="itemCsv.php">商品データ</a></li>
						<li><a href="itemStatusMaster.php">商品ステータスマスタ</a></li>
						<li><a href="itemStatus.php">商品ステータス</a></li>
						<li><a href="category.php">カテゴリデータ</a></li>
						<li><a href="parts.php">部品データ</a></li>
						<li class="active"><a href="#">PDF設定</a></li>
					</ul>
				</div>
				<div>
					<form class="form-horizontal well" action="#" method="post" name="form" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="step1" />
						<div class="form-group">
							<label class="col-sm-2 control-label">状態</label>
							<div class="col-sm-10" Align="left">
								<label class="col-sm-0 control-label"><?php echo $systemStatus ?></label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">PDF作成開始時間</label>
							<div class="col-sm-10">
								<label class="col-sm-0 control-label"><?php echo $startTime ?></label>
							</div>
						</div>
												<div class="form-group">
							<label for="upload" class="col-sm-2 control-label">PDF作成日変更</label>
							<div class="col-sm-10">
								<div class="btn-group">
									<div class="dropdown">
										<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" onClick="changeDay(this.form)">
											日付
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li><a href="#" data-val="0"><?php echo $today ?></a></li>
											<li><a href="#" data-val="1"><?php echo date("Y/m/d",strtotime("+1 day")) ?></a></li>
											<li><a href="#" data-val="2"><?php echo date("Y/m/d",strtotime("+2 day")) ?></a></li>
											<li><a href="#" data-val="3"><?php echo date("Y/m/d",strtotime("+3 day")) ?></a></li>
											<li><a href="#" data-val="4"><?php echo date("Y/m/d",strtotime("+4 day")) ?></a></li>
											<li><a href="#" data-val="5"><?php echo date("Y/m/d",strtotime("+5 day")) ?></a></li>
											<li><a href="#" data-val="6"><?php echo date("Y/m/d",strtotime("+6 day")) ?></a></li>
										</ul>
										<input type="hidden" id="dropdownDay" name="dropdownDay" value="">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="upload" class="col-sm-2 control-label">PDF作成開始時間変更</label>
							<div class="col-sm-10">
								<div class="input-group">
									<input style="width:50px;" type="text" id="hour" name="hour" class="form-control"  maxlength="2" pattern="^[0-9]+$">
									<label for="upload" class="col-sm-2 control-label">:</label>
									<input style="width:50px;" type="text" id="min" name="min" class="form-control"  maxlength="2">
								</div>
							</div>
						</div>
						<div align="center">
						<button type="submit" class="btn btn-default" onclick="document.form.submit();" name="unset_button" value="unset">設定解除</button>
							<button type="submit" class="btn btn-default" onclick="document.form.submit();" name="set_button" value="set">設定変更</button>
							<button type="submit" class="btn btn-success"  onclick="document.form.submit();" name="stop_button" value="stop">PDF作成中止</button>
							<button type="button" class="btn btn-warning">商品データ更新</button>
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

			function changeDay(form) {
				$(".dropdown-menu li a").click(function(){
					$(this).parents('.dropdown').find('.dropdown-toggle').html($(this).text() + ' <span class="caret"></span>');
					form.dropdownDay.value = $(this).text();
				});
			}
		</script>
    </body>
</html>