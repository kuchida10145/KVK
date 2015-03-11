<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvItem extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM, 'required');
			// 品名
			$this->manager->validationColumns->setRule(ITEM_NAME_COLUMN_ITEM, 'required');
			// 写真
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM, 'required');
			// 購入
			$this->manager->validationColumns->setRule(BUY_STATUS_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// 代替
			$this->manager->validationColumns->setRule(DAIGAE_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// 価格
			$this->manager->validationColumns->setRule(PRICE_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// 価格（税込）
			$this->manager->validationColumns->setRule(PRICE_ZEI_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// バリエーション順序
			$this->manager->validationColumns->setRule(VARIATION_NO_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// カタログ年度
			$this->manager->validationColumns->setRule(CATALOG_YEAR_COLUMN_ITEM, 'required|numeric|digit|pnumeric');
			// カタログページ
			$this->manager->validationColumns->setRule(CATALOG_PAGE_COLUMN_ITEM, 'required|numeric|digit|pnumeric');
			// 削除
			$this->manager->validationColumns->setRule(DELETE_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// カテゴリID
			$this->manager->validationColumns->setRule(CATEGORY_ID_COLUMN_ITEM, 'required|numeric|digit|pnumeric');

			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);
			// csvヘッダー項目数
			$this->headerCount = HEADER_COUNT_ITEM;
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$table = TABLE_NAME_ITEM;
		$result = true;

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_ITEM]);

		// 削除フラグ
		if($deleteFlg){
			$result = $this->manager->db_manager->get($table)->checkData($checkData[ITEM_ID_COLUMN_ITEM]);
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
		if ($this->{$checkData[ITEM_ID_COLUMN_ITEM]} != null) {
			$this->{DUPLICATION_LINE} = $this->{$checkData[ITEM_ID_COLUMN_ITEM]};
			$result = false;
		} else {
			$this->{$checkData[ITEM_ID_COLUMN_ITEM]} = $lineCount;
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
			$dbCheck = $this->manager->db_manager->get($table)->checkData($key);
			if($dbCheck) {
				// DBUpdate処理
				$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dbCheck = $this->manager->db_manager->get($table)->insertCategory($dataArray);
			}
		}
		return $dbCheck;
	}
}

?>
