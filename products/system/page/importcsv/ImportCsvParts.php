<?php
	include(dirname(__FILE__) . '/../AbstractImportCsv.php');
	class ImportCsvParts extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番（部品）
			$this->manager->validationColumns->setRule(PARTS_ID_COLUMN_PARTS, 'required');
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

		// ファイルアップロード
		$result = move_uploaded_file($filePath, $this->uploadInfo);

		if($result) {
			// システムステータス更新
			$result = $this->systemUpdate(SYSTEM_STATUS_PDF_WAIT, $this->pdfTime);
		} else {
			$this->{KEY_DB_CHECK_MESSAGE} = "部品CSVファイルのアップロードに失敗しました。<br>";
		}

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
}

?>
