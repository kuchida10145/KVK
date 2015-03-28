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
			$this->{DUPLICATION_LINE} = $this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]};
			$result = false;
		} else {
			$this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]} = $lineCount;
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

		// 親カテゴリor子カテゴリの設定をする。
		if($targetArray[PARENT_ID_COLUMN_CATEGORY] == 0) {
			// 親カテゴリDB登録データ生成
			$dataArray = array(	COLUMN_NAME_PARENT_ID=>$targetArray[CATEGORY_ID_COLUMN_CATEGORY],
					COLUMN_NAME_PARENT_NAME=>$targetArray[CATEGORY_NAME_COLUMN_CATEGORY],
					COLUMN_NAME_PARENT_IMAGE=>$targetArray[IMAGE_COLUMN_CATEGORY],
					COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_CATEGORY] );
			// 親カテゴリテーブル
			$table = TABLE_NAME_PARENT_CATEGORY;
			// key項目設定
			$key = $dataArray[COLUMN_NAME_PARENT_ID];
			// where句生成
			$where = "parent_id = {$key}";
		} else {
			// 子カテゴリDB登録データ生成
			$dataArray = array(
					COLUMN_NAME_CATEGORY_ID=>$targetArray[CATEGORY_ID_COLUMN_CATEGORY],
					COLUMN_NAME_CATEGORY_NAME=>$targetArray[CATEGORY_NAME_COLUMN_CATEGORY],
					COLUMN_NAME_CATEGORY_IMAGE=>$targetArray[IMAGE_COLUMN_CATEGORY],
					COLUMN_NAME_PARENT_ID=>$targetArray[PARENT_ID_COLUMN_CATEGORY],
					COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_CATEGORY] );
			// 子カテゴリテーブル
			$table = TABLE_NAME_CHILD_CATEGORY;
			// key項目設定
			$key = $dataArray[COLUMN_NAME_CATEGORY_ID];
			// where句生成
			$where = "category_id = {$key}";
		}

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($dataArray[COLUMN_NAME_VIEW_STATUS]);
		//削除フラグチェック
		if($deleteFlg){
			// DB削除処理(表示フラグ更新)
			$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
		} else {
			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbCheck = $this->manager->db_manager->get($table)->checkData($where);
			if($dbCheck) {
				// DBUpdate処理
				$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dbCheck = $this->manager->db_manager->get($table)->insertDB($dataArray);
			}
		}
		return $dbCheck;
	}
}

?>
