<?php
	include(dirname(__FILE__) . '/../AbstractImportCsv.php');
	class ImportCsvItemStatusMaster extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// ステータスID
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM_STATUS, 'required|numeric|digit|pnumeric');
			// ステータス名
			$this->manager->validationColumns->setRule(JIS_COLUMN_ITEM_STATUS, 'required');
			// ステータスアイコン
			$this->manager->validationColumns->setRule(NINSYO_TOUROKU_COLUMN_ITEM_STATUS, 'required');

			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);
			// csvヘッダー項目数
			$this->headerCount = HEADER_COUNT_STATUS_MASTER;
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$result = true;
		return $result;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @param	$lineCount	現在のcsvの行数
	 * @return	$result		チェック結果
	 */
	protected function dataPrimaryCheck($checkData, $lineCount) {
		$result = true;
		// キー項目が前にチェックしたデータにあったかチェックする
		if ($this->{$checkData[STATUS_NAME_COLUMN_STATUS_MASTER]} != null) {
			$this->{DUPLICATION_LINE} = $this->{$checkData[STATUS_NAME_COLUMN_STATUS_MASTER]};
			$result = false;
		} else {
			$this->{$checkData[STATUS_NAME_COLUMN_STATUS_MASTER]} = $lineCount;
		}
		return $result;
	}

	/**
	 * DB処理実行
	 * @param	$targetArray	DB処理対象データ
	 * @return	$result			チェック結果
	 */
	protected function runDB($targetArray) {
		$dataArray = array();		// 更新データ格納用の配列
		$dbCheck = "";				// DB動作結果
		$table = "";				// テーブル名
		$where = "";				// SQL実行用のwhere句
		$key = "";					// DB検索用Key

		// 商品ステータスDB登録データ生成
		$dataArray = array(
				COLUMN_NAME_STATUS_ID=>$targetArray[STATUS_ID_COLUMN_STATUS_MASTER],
				COLUMN_NAME_STATUS_NAME=>$targetArray[STATUS_NAME_COLUMN_STATUS_MASTER],
				COLUMN_NAME_ICON=>$targetArray[STATUS_ICON_COLUMN_STATUS_MASTER]
		);
		$table = TABLE_NAME_STATUS_LIST;
		// key項目設定
		$key = $dataArray[COLUMN_NAME_STATUS_ID];
		// where句生成
		$where = COLUMN_NAME_STATUS_ID." = '".$key."'";

		// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
		$dbCheck = $this->manager->db_manager->get($table)->checkData($where);
		if($dbCheck) {
			// DBUpdate処理
			$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
		} else {
			// DBinsert処理
			$dbCheck = $this->manager->db_manager->get($table)->insertDB($dataArray);
		}
		return $dbCheck;
	}

	/**
	 * CSVアップロード実行
	 * @param	$filePath	保存対象ファイルパス
	 * @return	$result		アップロード実行結果
	 */
	protected function csvUpload($filePath) {
		// 処理なし
		$result = true;
		return $result;
	}

	/**
	 * バリデーション実行
	 * @param  Array	$csvLineData	csvの1行データ
	 * @return String	$errorMessage	エラーメッセージ
	 */
	protected function runValidation($csvLineData, $lineCount) {
		$errorMessage = "";

		$this->manager->validationColumns->resetError();
		if(!$this->manager->validationColumns->run($csvLineData)) {
			$errorMessage = $this->manager->validationColumns->getErrorMessageColumn($lineCount, $this->msg_rules);
		}

		return $errorMessage;
	}

	/**
	 * ファイル確認
	 * @param  Array	$csvLineData	csvの1行データ
	 * @return String	$errorMessage	エラーメッセージ
	 */
	protected function fileCheck($csvLineData) {
		$errorMessage = "";

		return $errorMessage;
	}
}

?>
