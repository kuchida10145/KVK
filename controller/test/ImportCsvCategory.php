<?php
	// セッション
	session_start();

	include_once('../AbstractImportCsv.php');
	class ImportCsvCategory extends AbstractImportCsv {

		function __construct() {
			$this->headerCount = 3;	//CSVのカラム数
		}
		/**
		 * CSVデータチェック
		 * @param  $checkData	csvデータ
		 * @return $result		チェック結果
		 */
		public function dataFormCheck($checkData, $line_count) {
			$data_check = new csv_data_check();
			$row = array('parent_id'=>$checkData[0], 'parent_name'=>$checkData[1], 'parent_id'=>$checkData[2]);
			$check_result = $data_check->categoryDataCheck($row);
			$result = true;

			if(!$check_result['required']){
				$this->dataFormCheckMessage = "未入力項目があります。 '$line_count+1'行目";
				$result = false;
			}

			if(!$check_result['resultNum']){
				$this->dataFormCheckMessage = $this->dataFormCheckMessage." 数値を入力してください。 '$line_count+1'行目";
				$result = false;
			}

			return $result;
		}

		/**
		 * DBチェック
		 * @param  $checkData	csvデータ
		 * @return $result		チェック結果
		 */
		public function dataDBCheck($checkData, $line_count) {
			$result = true;

			// 削除フラグ
			if($checkData[3]){
				$result = $this->manager->db_manager->get('parent_category')->checkData($checkData[0]);
			}

			if(!$result) {
				$this->dataDBCheckMessage = "対象のデータが存在しません。 '$line_count+1'行目";
			}

			return $result;
		}

		/**
		 * csv取込結果メッセージ生成
		 * @param $errorMessage	ｴﾗｰﾒｯｾｰｼﾞ
		 * @return $viewMessage	画面表示メッセージ
		 */
		public function dispResult($errorMsg) {
			$importResult = '';
			$viewMessage = '';

			// ｴﾗｰﾒｯｾｰｼﾞが無い場合DB追加処理を実行。
			if(sizeof($errorMsg) == 0){
				$importResult = $this->manager->db_manager->get('parent_category')->insertParentCategory($csv);

				if($importResult) {
					$viewMessage = 'DB追加成功';
				} else {
					$viewMessage = 'DB追加失敗';
				}
			} else {
				foreach ($errorMsg as $row) {
					$viewMessage = $viewError."'$row'<br>";
				}
			}
			return $viewMessage;
		}
	}

	// csv取込処理実行
	if(isset($_POST['mode']) && $_POST['mode'] == "step1"){
		$importCsv = new ImportCsvCategory();
		$csvFile = $_FILES["file"]["tmp_name"];

		// 拡張子チェック
		$error = $importCsv->checkExtension($csvFile);
		if(!$error) {
			$importCsv->Message = 'CSVファイルを取り込んでください。';
			exit;
		}
		// CSVデータ取得
		$csvData = $importCsv->getCsvData($csvFile);
		// CSVデータ数チェック
		$error = $importCsv->csvDataCountCheck(sizeof($csvData));
		if(!$error) {
			$importCsv->Message = 'CSVファイルにデータがありません。';
			exit;
		}
		// CSVカラム数チェック
		$error = $importCsv->csvColumnCheck(sizeof($csvData[0]));
		if(!$error) {
			$importCsv->Message = 'CSVファイルの項目数が足りません。';
			exit;
		}
		// CSVデータチェック取得
		$error = $importCsv->csvDataCheck(sizeof($csvData));
		if(!$error) {
			exit;
		}
		// シュミレーションモードでなければDB更新。
		if($dbFlg) {
			//削除フラグチェック
			if($checkData[3]){
				// DB削除処理
			} else {
				$dbCheck = $this->manager->db_manager->get('parent_category')->checkData($checkData[0]);
				if(!$dbCheck) {
					// DBinsert処理
				} else {
					// Update処理
				}
			}
		}

		// エラーなし
		$importCsv->Message = '成功';
		// エラーあり
		$importCsv->Message = '失敗';
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
		<link href="sample_code/css/bootstrap.min.css" rel="stylesheet">

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