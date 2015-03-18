<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvParts extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番（部品）
			$this->manager->validationColumns->setRule(PARTS_ID_COLUMN_PARTS, 'required');
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
		$where = "";
		$table = TABLE_NAME_PDF_PARTS_LIST;
		$result = true;

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_PARTS]);

		// 削除フラグ
		if($deleteFlg){
			// where句生成
			$where = COLUMN_NAME_NO." = '".$checkData[NO_COLUMN_PARTS]."' AND ".COLUMN_NAME_PARTS_ID." = '".$checkData[PARTS_ID_COLUMN_PARTS]."' AND ".COLUMN_NAME_FILE_NAME." = '".$checkData[FILE_COLUMN_PARTS]."'";
			$result = $this->manager->db_manager->get($table)->checkData($where);
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
		$setVal = $checkData[NO_COLUMN_PARTS].','.$checkData[PARTS_ID_COLUMN_PARTS].','.$checkData[FILE_COLUMN_PARTS];

		// キー項目が前にチェックしたデータにあったかチェックする
		if ($this->{$setVal} != null) {
			$this->{DUPLICATION_LINE} = $this->{$setVal};
			$result = false;
		} else {
			$this->{$setVal} = $lineCount;
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
		$itemCodeArray = array();	// 商品ID格納用の配列
		$partsNameArray = array();	// パーツ名格納用の配列
		$dbCheck = "";				// DB動作結果
		$where = "";				// SQL実行用のwhere句
		$keyNo = $targetArray[NO_COLUMN_PARTS];				// 検索Key(商品表示順)
		$keyFileName = $targetArray[FILE_COLUMN_PARTS];		// 検索Key(ファイル名)
		$keyItemID = $targetArray[PARTS_ID_COLUMN_PARTS];	// 検索key(品番：部品)
		$limit = "";
		$order = "";

		// 対象ファイル名から商品IDを取得
		$getItemCodeArray = COLUMN_NAME_BUNKAI_DATA.' = "'.$keyFileName.'"';
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->search($getItemCodeArray, $limit, $order);
		$itemCodeRow = $itemCodeArray[0];

		// 部品リストDB登録データ生成
		$dataArray = array(
			// 番号（部品表示順）
			COLUMN_NAME_NO=>$targetArray[NO_COLUMN_PARTS],
			// 品番（パーツ）
			COLUMN_NAME_PARTS_ID=>$targetArray[PARTS_ID_COLUMN_PARTS],
			// 品名（パーツ）
			COLUMN_NAME_PARTS_NAME=>$targetArray[PARTS_NAME_COLUMN_PARTS],
			// 希望小売価格
			COLUMN_NAME_PRICE=>$targetArray[PRICE_COLUMN_PARTS],
			// 希望小売価格（税込み）
			COLUMN_NAME_PRICE_ZEI=>$targetArray[PRICE_ZEI_COLUMN_PARTS],
			// 品番（商品）
			COLUMN_NAME_ITEM_ID=>$itemCodeRow[COLUMN_NAME_ITEM_ID],
			// ファイル名
			COLUMN_NAME_FILE_NAME=>$targetArray[FILE_COLUMN_PARTS],
			// 表示ステータス
			COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_PARTS],
			// 備考
			COLUMN_NAME_NOTE=>$targetArray[NOTE_COLUMN_PARTS],
			// 更新日
			COLUMN_NAME_UPDATE_DATE=>date("Y-m-d H:i:s"),
		);

		// where句生成
		$where = COLUMN_NAME_NO." = '".$keyNo."' AND ".COLUMN_NAME_PARTS_ID." = '".$keyItemID."' AND ".COLUMN_NAME_FILE_NAME." = '".$keyFileName."'";

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($targetArray[DELETE_COLUMN_PARTS]);
		//削除フラグチェック
		if($deleteFlg){
			// DB削除処理(表示フラグ更新)
			$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->update($dataArray, $where);
		} else {
			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->checkData($where);
			if($dbCheck) {
				// DBUpdate処理
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dataArray[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日追加
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->insertParts($dataArray);
			}
		}
		return $dbCheck;
 	}
}

?>
