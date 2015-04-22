<?php
	include(dirname(__FILE__) . '/../AbstractImportCsv.php');
	class ImportCsvItemStatus extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM_STATUS, 'required');

			$dbArray = $this->manager->db_manager->get('item_icon')->getAll();
			foreach ($dbArray as $key=>$value) {
				$this->manager->validationColumns->setRule($key + 1, 'numeric|digit|pnumeric');
			}

			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);
			// csvヘッダー項目数（品番があるので+1）
			$this->headerCount = count($dbArray) + 1;
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$getItemArray = "";
		$table = TABLE_NAME_ITEM;
		$itemArray = array();
		$result = true;
		$limit = "";
		$order = "";

		// 行番号追加
		$line_count = $line_count + 1;

		// 商品IDから商品データを取得
		$getItemArray = COLUMN_NAME_ITEM_ID.' = "'.$checkData[ITEM_ID_COLUMN_ITEM_STATUS].'"';
		$itemArray = $this->manager->db_manager->get($table)->search($getItemArray, $limit, $order);
		if(count($itemArray) == 0) {
			$result = false;
		}

		if(!$result) {
			$this->{KEY_DB_CHECK_MESSAGE} = "対象の商品データが存在しません。 {$line_count}行目<br>";
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
		if ($this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]} != null) {
			$this->{DUPLICATION_LINE} = $this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]};
			$result = false;
		} else {
			$this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]} = $lineCount;
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
		$itemArray = array();		// 商品データ検索結果
		$itemRow = array();			// 商品データ（1行）
		$dbCheck = "";				// DB動作結果
		$table = TABLE_NAME_ITEM;	// テーブル名
		$where = "";				// SQL実行用のwhere句
		$key = "";					// DB検索用Key
		$arrayCount = 0;			// 配列要素カウント用
		$itemStatus = "";			// 商品テーブル.商品ステータス
		$limit = "";
		$order = "";

		// DB更新データ（商品ステータスデータ作成）
		foreach ($targetArray as $status) {
			if($status === "1") {
				$itemStatus = $itemStatus.$arrayCount.",";
			}
			$arrayCount = $arrayCount + 1;
		}
		$itemStatus = substr($itemStatus, 0, -1);   //最後の「,」を削除

		// 商品IDから商品データを取得
		$getItemArray = COLUMN_NAME_ITEM_ID.' = "'.$targetArray[ITEM_ID_COLUMN_ITEM_STATUS].'"';
		$itemArray = $this->manager->db_manager->get($table)->search($getItemArray, $limit, $order);
		$itemRow = $itemArray[0];

		// 商品データ更新
		$itemRow[COLUMN_NAME_ITEM_STATUS] = $itemStatus;			// 商品ステータス
		$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日

		// where句生成
		$where = COLUMN_NAME_ITEM_ID." = '".$itemRow[COLUMN_NAME_ITEM_ID]."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$itemRow[COLUMN_NAME_CATEGORY_ID]."'";

		$dbCheck = $this->manager->db_manager->get($table)->update($itemRow, $where);

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
}

?>
