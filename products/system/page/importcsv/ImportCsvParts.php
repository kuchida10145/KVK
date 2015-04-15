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
			$this->uploadInfo = CSV_FOLDER.CSV_FILE_NAME_ONETIME_PARTS;
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
}

?>
