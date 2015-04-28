<?php
	include(dirname(__FILE__) . '/../AbstractImportCsv.php');
	class ImportCsvParts extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番（部品）
			$this->manager->validationColumns->setRule(PARTS_ID_COLUMN_PARTS, 'required');
			// 品名（部品）
			$this->manager->validationColumns->setRule(PARTS_NAME_COLUMN_PARTS, 'required');
			// 希望小売価格（税抜）
			$this->manager->validationColumns->setRule(PRICE_COLUMN_PARTS, 'required|numeric|digit|pnumeric');
			// 希望小売価格（税込）
			$this->manager->validationColumns->setRule(PRICE_ZEI_COLUMN_PARTS, 'required|numeric|digit|pnumeric');
			// 品番（対象商品）
			$this->manager->validationColumns->setRule(ITEM_COLUMN_PARTS, 'required');
			// ファイル名
			$this->manager->validationColumns->setRule(FILE_COLUMN_PARTS, 'required');
			// 表示フラグ
			$this->manager->validationColumns->setRule(DELETE_COLUMN_PARTS, 'numeric|digit|pnumeric');

			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);

			// csvヘッダー項目数
			$this->headerCount = HEADER_COUNT_PARTS;

			// csvアップロード情報
			$this->uploadInfo = CSV_FOLDER.MAKING_PDF_CSV;

			// 出力csvファイルのヘッダー行（商品）
			$this->csvHeaderItem = array(
					'品番',
					'品名',
					'写真',
					'図面',
					'取説',
					'施工',
					'分解図本体',
					'分解図_シャワー',
					'購入',
					'価格',
					'価格（税込み）',
					'備考',
					'商品イメージ',
					'バリエーション親品番',
					'バリエーション順序',
					'カタログ年度',
					'カタログページ',
					'検索ワード',
					'分岐金具',
					'分岐金具',
					'分岐金具',
					'発売時期',
					'代替品',
					'本体取付穴',
					'ピッチ',
					'シャワーS取付穴',
					'削除',
					'カテゴリID'
			);

			// 出力csvファイルのヘッダー行（パーツ）
			$this->csvHeader = array(
					'番号',
					'品番',
					'品名',
					'希望小売価格',
					'税込',
					'品番',
					'分解図',
					'備考',
					'削除',
			);
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		// 処理なし
		return true;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @param	$lineCount	現在のcsvの行数
	 * @return	$result		チェック結果
	 */
	protected function dataPrimaryCheck($checkData, $lineCount) {
		$result = true;
		$setVal = $checkData[NO_COLUMN_PARTS].','.$checkData[PARTS_ID_COLUMN_PARTS].','.$checkData[ITEM_COLUMN_PARTS];

		// キー項目が前にチェックしたデータにあったかチェックする(表示順空白はチェックしない)
		if($checkData[NO_COLUMN_PARTS] != "") {
			if ($this->{$setVal} != null) {
				$this->{DUPLICATION_LINE} = $this->{$setVal};
				$result = false;
			} else {
				$this->{$setVal} = $lineCount;
			}
		}
		return $result;
	}

	/**
	 * DB処理実行
	 * @param	$targetArray	DB処理対象データ
	 * @return	$result			チェック結果
	 */
	protected function runDB($targetArray) {
		// 処理なし
		return true;
 	}

	/**
	 * CSVアップロード実行
	 * @param	$filePath	保存対象ファイルパス
	 * @return	$result		アップロード実行結果
	 */
	protected function csvUpload($filePath) {
		$result = true;
		$updateArray = array();

		// ファイルアップロード
		$result = move_uploaded_file($filePath, $this->uploadInfo);

		// 更新データのみでcsvファイル作成
		$updateArray = $this->makeCsvData();
		$result = $this->setExport($updateArray);

		if($result) {
			// システムステータス更新
			$result = $this->systemUpdate(SYSTEM_STATUS_PDF_WAIT, $this->pdfTime);
		} else {
			$this->{KEY_DB_CHECK_MESSAGE} = "部品CSVファイルのアップロードに失敗しました。<br>";
		}

		return $result;
	}

	/**
	 * 更新用csvデータ取得
	 *
	 * @return array 更新データ
	 */
	protected function makeCsvData() {
		$updateArray = array();	// 更新対象配列
		$makePDF = array();
		$updateCheck = false;
		$flg = false;
		$fileName = "";
		$oldFileName = "";
		$nowArray = $this->getCsvData($this->uploadInfo);

		foreach($nowArray as $key=>$value) {
			if($key == "0") {
				continue;
			}

			$fileName = $value['5'];

			if(empty($oldFileName)) {
				$makePDF[] = $value;
			} else {
				if($fileName == $oldFileName){
					$makePDF[] = $value;
				} else {
					$flg = $this->makePdfCheck($makePDF);
					$updateCheck = true;
				}
			}

			$oldFileName = $fileName;

			if($updateCheck) {
				if($flg) {
					// 更新対象データのみ保持する
					foreach ($makePDF as $check) {
						$updateArray[] = $check;
					}
					$makePDF = array();
					$makePDF[] = $value;
				} else {
					$makePDF = array();
					$makePDF[] = $value;
				}
				$updateCheck = false;
			}
		}

		return $updateArray;
	}

	/**
	 * PDF作成チェック
	 * @param	array	$makePDF	PDF作成予定データ
	 * @return	boolean				PDF作成フラグ（TRUE:PDF作成/FALSE:PDF作成しない）
	 */
	protected function makePdfCheck($makePDF) {
		$checkArray = array();
		$targetArray = array();
		$partsList = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->findByItemId($makePDF[0][ITEM_COLUMN_PARTS]);

		if(empty($partsList)){
			// データが無ければPDF作成
			return true;
		}

		if(count($makePDF) != count($partsList)) {
			return true;
		}

		foreach ($partsList as $key => $value) {
			$targetArray = $this->convertDBdata($value);
			$checkArray = array_diff($targetArray, $makePDF[$key]);
			$count = count($checkArray);
			if($count != 0){
				return true;
			}
		}
		return false;
	}

	/**
	 * 部品データ変換（DB⇒CSV）
	 * @param	array	$dbParts	DB部品データ
	 * @return	array	$checkArray	変換後配列
	 */
	protected function convertDBdata($dbParts) {
		$checkArray = array(
				// 番号（部品表示順）
				"0"=>$dbParts[COLUMN_NAME_NO],
				// 品番（パーツ）
				"1"=>$dbParts[COLUMN_NAME_PARTS_ID],
				// 品名（パーツ）
				"2"=>$dbParts[COLUMN_NAME_PARTS_NAME],
				// 希望小売価格
				"3"=>$dbParts[COLUMN_NAME_PRICE],
				// 希望小売価格（税込み）
				"4"=>$dbParts[COLUMN_NAME_PRICE_ZEI],
				// 品番（商品）
				"5"=>$dbParts[COLUMN_NAME_ITEM_ID],
				// ファイル名
				"6"=>$dbParts[COLUMN_NAME_FILE_NAME],
				// 表示フラグ
				"7"=>$dbParts[COLUMN_NAME_NOTE],
				// 備考
				"8"=>$dbParts[COLUMN_NAME_VIEW_STATUS],
		);
		return $checkArray;
	}

	/**
	 * csvファイル出力メイン処理
	 * @param	array()		csv保存対象データ
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExport($updateArray) {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;
		$makeFilePath = $this->uploadInfo;

		// csvファイル書き込み
		$filePointer = fopen($makeFilePath, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
		fputcsv($filePointer, $headerArray);

		foreach ($updateArray as $itemDataRow){
			$csvDataArray = array(
					// 番号
					$itemDataRow[NO_COLUMN_PARTS],
					// 品番
					$itemDataRow[PARTS_ID_COLUMN_PARTS],
					// 品名
					$itemDataRow[PARTS_NAME_COLUMN_PARTS],
					// 希望小売価格
					$itemDataRow[PRICE_COLUMN_PARTS],
					// 税込
					$itemDataRow[PRICE_ZEI_COLUMN_PARTS],
					// 品番
					$itemDataRow[ITEM_COLUMN_PARTS],
					// 分解図
					$itemDataRow[FILE_COLUMN_PARTS],
					// 備考
					$itemDataRow[NOTE_COLUMN_PARTS],
					// 削除
					$itemDataRow[DELETE_COLUMN_PARTS],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		return $result;
	}

	/**
	 * CSVデータ取得
	 * @param  $csvFile	csvファイルパス
	 * @return $csv		UTF-8変換後のcsvデータ
	 */
	protected function getCsvData($csvFile) {
		$csv  = array();
		// csvから取り込んだデータをUTF-8に変換する
		$data = file_get_contents($csvFile);
		$data = mb_convert_encoding($data, SYSTEM_CODE, CSV_CODE);
		$temp = tmpfile();

		fwrite($temp, $data);
		rewind($temp);

		while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
			$csv[] = $data;
		}
		fclose($temp);

		return $csv;
	}

	/**
	 * csvファイル出力メイン処理(商品)
	 * @param	array()		csv保存対象データ
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExportItem($updateArray) {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;
		$makeFilePath = $this->uploadInfo;

		// csvファイル書き込み
		$filePointer = fopen($makeFilePath, 'w');
		$headerArray = $this->csvHeaderItem;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
		fputcsv($filePointer, $headerArray);

		foreach ($updateArray as $itemDataRow){
			$csvDataArray = array(
					// 品番
					$itemDataRow[ITEM_ID_COLUMN_ITEM],
					// 品名
					$itemDataRow[ITEM_NAME_COLUMN_ITEM],
					// 写真
					$itemDataRow[ITEM_PHOTO_COLUMN_ITEM],
					// 図面
					$itemDataRow[MAP_COLUMN_ITEM],
					// 取説
					$itemDataRow[TORISETSU_COLUMN_ITEM],
					// 施工
					$itemDataRow[SEKOU_COLUMN_ITEM],
					// 分解図本体
					$itemDataRow[BUNKAI_COLUMN_ITEM],
					// 分解図_シャワー
					$itemDataRow[SHOWER_COLUMN_ITEM],
					// 購入
					$itemDataRow[BUY_STATUS_COLUMN_ITEM],
					// 価格
					$itemDataRow[PRICE_COLUMN_ITEM],
					// 価格（税込み）
					$itemDataRow[PRICE_ZEI_COLUMN_ITEM],
					// 備考
					$itemDataRow[NOTE_COLUMN_ITEM],
					// 商品イメージ
					$itemDataRow[ITEM_IMAGE_COLUMN_ITEM],
					// バリエーション親品番
					$itemDataRow[VARIATION_NAME_COLUMN_ITEM],
					// バリエーション順序
					$itemDataRow[VARIATION_NO_COLUMN_ITEM],
					// カタログ年度
					$itemDataRow[CATALOG_YEAR_COLUMN_ITEM],
					// カタログページ
					$itemDataRow[CATALOG_PAGE_COLUMN_ITEM],
					// 検索ワード
					$itemDataRow[SEARCH_WORD_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_1_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_2_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_3_COLUMN_ITEM],
					// 発売時期
					$itemDataRow[SELL_KIKAN_COLUMN_ITEM],
					// 代替品
					$itemDataRow[DAIGAE_COLUMN_ITEM],
					// 本体取付穴
					$itemDataRow[SUNPOU_COLUMN_ITEM],
					// ピッチ
					$itemDataRow[PITCH_COLUMN_ITEM],
					// シャワーS取付穴
					$itemDataRow[SHOWER_SUNPOU_COLUMN_ITEM],
					// 削除
					$itemDataRow[DELETE_COLUMN_ITEM],
					// カテゴリID
					$itemDataRow[CATEGORY_ID_COLUMN_ITEM],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		return $result;
	}

	/**
	 * ファイル確認
	 * @param  Array	$csvLineData	csvの1行データ
	 * @return String	$errorMessage	エラーメッセージ
	 */
	protected function fileCheck($csvLineData) {
		$errorMessage = "";
		$fileName = $csvLineData[FILE_COLUMN_PARTS];

		if(!file_exists(CSV_FOLDER.$fileName)) {
			$errorMessage = "`$fileName`がアップロードフォルダにありません。<br>";
		}

		if ($this->{$errorMessage} == null) {
			$this->{$errorMessage} = $errorMessage;
		} else {
			$errorMessage = "";
		}

		return $errorMessage;
	}

	/**
	 * バリデーション実行
	 * @param  Array	$csvLineData	csvの1行データ
	 * @return String	$errorMessage	エラーメッセージ
	 */
	protected function runValidation($csvLineData, $lineCount) {
		$errorMessage = "";

		if(empty($csvLineData[NO_COLUMN_PARTS])) {
			$errorMessage = $this->titleLineCheck($csvLineData, $lineCount);
		} else {
			$this->manager->validationColumns->resetError();
			if(!$this->manager->validationColumns->run($csvLineData)) {
				$errorMessage = $this->manager->validationColumns->getErrorMessageColumn($lineCount, $this->msg_rules);
			}
		}

		return $errorMessage;
	}


	/**
	 * タイトル行チェック
	 * @param  Array	$csvLineData	csvの1行データ
	 * @return String	$errorMessage	エラーメッセージ
	 */
	protected function titleLineCheck($csvLineData, $lineCount) {
		$errorMessage = "";
		// 品名
		if(!empty($csvLineData[PARTS_NAME_COLUMN_PARTS])) {
			$errorMessage = $errorMessage."番号が未入力のデータに、品名が入力されています。'$lineCount'行目<br>";
		}
		// 価格（税抜）
		if(!empty($csvLineData[PRICE_COLUMN_PARTS])) {
			$errorMessage = $errorMessage."番号が未入力のデータに、希望小売価格が入力されています。'$lineCount'行目<br>";
		}
		// 価格（税込）
		if(!empty($csvLineData[PRICE_COLUMN_PARTS])) {
			$errorMessage = $errorMessage."番号が未入力のデータに、税込が入力されています。'$lineCount'行目<br>";
		}

		return $errorMessage;
	}
}

?>
