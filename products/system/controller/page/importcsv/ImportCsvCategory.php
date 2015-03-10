<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvCategory extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// カテゴリID
			$this->manager->validationColumns->setRule(CATEGORY_ID_COLUMN_CATEGORY, 'required|numeric|digit|pnumeric');
			// カテゴリ名
			$this->manager->validationColumns->setRule(CATEGORY_NAME_COLUMN_CATEGORY, 'required');
			// 親カテゴリID
			$this->manager->validationColumns->setRule(PARENT_ID_COLUMN_CATEGORY, 'required|numeric|digit|pnumeric');
			// イメージ画像
			$this->manager->validationColumns->setRule(IMAGE_COLUMN_CATEGORY, 'required');
			// 削除フラグ
			$this->manager->validationColumns->setRule(DELETE_COLUMN_CATEGORY, 'numeric|digit|pnumeric');
			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);
			// csvヘッダー項目数
			$this->headerCount = HEADER_COUNT_CATEGORY;
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$table = "";
		$result = true;

		// テーブル判定
		if($checkData[PARENT_ID_COLUMN_CATEGORY] == 0) {
			$table = TABLE_NAME_PARENT_CATEGORY;
		} else {
			$table = TABLE_NAME_CHILD_CATEGORY;
		}

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_CATEGORY]);

		// 削除フラグ
		if($deleteFlg){
			$result = $this->manager->db_manager->get($table)->checkData($checkData[CATEGORY_ID_COLUMN_CATEGORY]);
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
