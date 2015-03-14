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
		$where = "";
		$table = TABLE_NAME_PDF_ITEM;
		$result = true;

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_ITEM]);

		// 削除フラグ
		if($deleteFlg){
			$where = COLUMN_NAME_ITEM_ID." = '".$checkData[ITEM_ID_COLUMN_ITEM]."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$checkData[CATEGORY_ID_COLUMN_ITEM]."'";
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
		$setVal = $checkData[ITEM_ID_COLUMN_ITEM].','.$checkData[CATEGORY_ID_COLUMN_ITEM];

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
		$keyItemID = $targetArray[ITEM_ID_COLUMN_ITEM];				// 検索key(品番：部品)
		$keyCategoryNo = $targetArray[CATEGORY_ID_COLUMN_ITEM];		// 検索Key(ファイル名)
		$limit = "";
		$order = "";

		// TODO：商品ステータス取得処理
		// TODO：カテゴリリンク作成

		// 商品DB登録データ生成
		$dataArray = array(
			// 品番（商品）
			COLUMN_NAME_ITEM_ID=>$keyItemID,
			// 品名
			COLUMN_NAME_PARTS_ID=>$targetArray[ITEM_NAME_COLUMN_ITEM],
			// 表示ステータス
			COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_ITEM],
			// 商品ステータス
			COLUMN_NAME_ITEM_STATUS=>$targetArray[],
			// 希望小売価格
			COLUMN_NAME_PRICE=>$targetArray[PRICE_COLUMN_ITEM],
			// 希望小売価格（税込み）
			COLUMN_NAME_PRICE_ZEI=>$targetArray[PRICE_ZEI_COLUMN_ITEM],
			// 図面データ
			COLUMN_NAME_MAP_DATA=>$targetArray[MAP_COLUMN_ITEM],
			// 取説データ
			COLUMN_NAME_TORISETSU_DATA=>$targetArray[TORISETSU_COLUMN_ITEM],
			// 工説データ
			COLUMN_NAME_KOUSETSU_DATA=>$targetArray[SEKOU_COLUMN_ITEM],
			// 分解図データ
			COLUMN_NAME_BUNKAI_DATA=>$targetArray[BUNKAI_COLUMN_ITEM],
			// シャワーデータ
			COLUMN_NAME_SHOWER_DATA=>$targetArray[SHOWER_COLUMN_ITEM],
			// 購入フラグ
			COLUMN_NAME_BUY_STATUS=>$targetArray[BUY_STATUS_COLUMN_ITEM],
			// カタログへのリンク
			COLUMN_NAME_CATALOG_LINK=>$targetArray[],
			// バリエーション親品番
			COLUMN_NAME_PARENT_VARIATION=>$targetArray[VARIATION_NAME_COLUMN_ITEM],
			// バリエーション表示順
			COLUMN_NAME_VARIATION_NO=>$targetArray[VARIATION_NO_COLUMN_ITEM],
			// 備考
			COLUMN_NAME_NOTE=>$targetArray[NOTE_COLUMN_ITEM],
			// 商品イメージ画像
			COLUMN_NAME_ITEM_IMAGE=>$targetArray[],
			// カテゴリID
			COLUMN_NAME_CATEGORY_ID=>$targetArray[CATEGORY_ID_COLUMN_ITEM],
			// pdf作成ステータス
			COLUMN_NAME_PDF_STATUS=>$targetArray[],
			// 更新日
			COLUMN_NAME_UPDATE_DATE=>date("Y-m-d H:i:s"),
		);

		// where句生成
		$where = COLUMN_NAME_ITEM_ID." = '".$keyItemID."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$keyCategoryNo."'";

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($targetArray[DELETE_COLUMN_ITEM]);
		//削除フラグチェック
		if($deleteFlg){
			// DB削除処理(表示フラグ更新)
			$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->update($dataArray, $where);
		} else {
			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->checkData($where);
			if($dbCheck) {
				// DBUpdate処理
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dataArray[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日追加
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->insertParts($dataArray);
			}
		}
		return $dbCheck;
 	}
}

?>
