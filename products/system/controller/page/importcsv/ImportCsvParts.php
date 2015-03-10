<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvParts extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 番号（部品表示順）
			$this->manager->validationColumns->setRule(NO_COLUMN_PARTS, 'required|numeric|digit|pnumeric');
			// 品番（部品）
			$this->manager->validationColumns->setRule(PARTS_ID_COLUMN_PARTS, 'required');
			// 希望小売価格（税抜き）
			$this->manager->validationColumns->setRule(PRICE_COLUMN_PARTS, 'required|numeric|digit|pnumeric');
			// 希望小売価格（税込み）
			$this->manager->validationColumns->setRule(PRICE_ZEI_COLUMN_PARTS, 'required|numeric|digit|pnumeric');
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
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$table = TABLE_NAME_PARTS_LIST;
		$result = true;

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_PARTS]);

		// 削除フラグ
		if($deleteFlg){
			$result = $this->manager->db_manager->get($table)->checkData($checkData[NO_COLUMN_PARTS], $checkData[FILE_COLUMN_PARTS]);
		}

		if(!$result) {
			$this->{KEY_DB_CHECK_MESSAGE} = "対象のデータが存在しません。 {$line_count}行目<br>";
		}
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
		if ($this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]} != null) {
			$result = false;
		} else {
			$this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]} = $lineCount;
		}
		return $result;
	}

}

?>
