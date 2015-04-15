<?php
	include(dirname(__FILE__) . '/../AbstractImportCsv.php');
	class ImportCsvItem extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM, 'required');
			// 品名
			$this->manager->validationColumns->setRule(ITEM_NAME_COLUMN_ITEM, 'required');
			// 写真
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM, 'required');
			// 価格
			$this->manager->validationColumns->setRule(PRICE_COLUMN_ITEM, 'required');
			// 価格（税込）
			$this->manager->validationColumns->setRule(PRICE_ZEI_COLUMN_ITEM, 'required');
			// バリエーション順序
			$this->manager->validationColumns->setRule(VARIATION_NO_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// カタログ年度
			$this->manager->validationColumns->setRule(CATALOG_YEAR_COLUMN_ITEM, 'numeric|digit|pnumeric');
			// カタログページ
			$this->manager->validationColumns->setRule(CATALOG_PAGE_COLUMN_ITEM, 'numeric|digit|pnumeric');
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

			// システム状態取得
			$this->sytemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();

			// csvアップロード情報
			$this->uploadInfo = CSV_FOLDER.CSV_FILE_NAME_ONETIME_ITEM;
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

		// カタログファイル名作成
		$catalogLink = $this->makeCatalogFileName(
			$targetArray[CATALOG_YEAR_COLUMN_ITEM], $targetArray[CATALOG_PAGE_COLUMN_ITEM]);

		// カテゴリID分解
		$parentIDVal = mb_substr($targetArray[CATEGORY_ID_COLUMN_ITEM], -4, NULL);
		$categoryIDVal = substr($targetArray[CATEGORY_ID_COLUMN_ITEM], -3);

		// 商品DB登録データ生成
		$dataArray = array(
			// 品番（商品）
			COLUMN_NAME_ITEM_ID=>$keyItemID,
			// 品名
			COLUMN_NAME_ITEM_NAME=>$targetArray[ITEM_NAME_COLUMN_ITEM],
			// 表示ステータス
			COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_ITEM],
			// 商品ステータス
			COLUMN_NAME_ITEM_STATUS=>"",
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
			COLUMN_NAME_CATALOG_LINK=>$catalogLink,
			// バリエーション親品番
			COLUMN_NAME_PARENT_VARIATION=>$targetArray[VARIATION_NAME_COLUMN_ITEM],
			// バリエーション表示順
			COLUMN_NAME_VARIATION_NO=>$targetArray[VARIATION_NO_COLUMN_ITEM],
			// 備考
			COLUMN_NAME_NOTE=>$targetArray[NOTE_COLUMN_ITEM],
			// 商品イメージ画像
			COLUMN_NAME_ITEM_IMAGE=>"",
			// 親カテゴリID
			COLUMN_NAME_PARENT_ID=>$parentIDVal,
			// 子カテゴリID
			COLUMN_NAME_CATEGORY_ID=>$categoryIDVal,
			// pdf作成ステータス
			COLUMN_NAME_PDF_STATUS=>"",
			// 検索ワード
			COLUMN_NAME_SEARCH_WORD=>$targetArray[SEARCH_WORD_COLUMN_ITEM],
			// 分岐金具1
			COLUMN_NAME_BUNKI_KANAGU_1=>$targetArray[BUNKI_KANAGU_1_COLUMN_ITEM],
			// 分岐金具2
			COLUMN_NAME_BUNKI_KANAGU_2=>$targetArray[BUNKI_KANAGU_2_COLUMN_ITEM],
			// 分岐金具3
			COLUMN_NAME_BUNKI_KANAGU_3=>$targetArray[BUNKI_KANAGU_3_COLUMN_ITEM],
			// 販売時期
			COLUMN_NAME_SELL_TIME=>$targetArray[SELL_KIKAN_COLUMN_ITEM],
			// 代替品
			COLUMN_NAME_SUB_ITEM=>$targetArray[DAIGAE_COLUMN_ITEM],
			// 本体取付穴
			COLUMN_NAME_SUNPOU=>$targetArray[SUNPOU_COLUMN_ITEM],
			// ピッチ
			COLUMN_NAME_PITCH=>$targetArray[PITCH_COLUMN_ITEM],
			// シャワー取付穴
			COLUMN_NAME_SHOWER_SUNPOU=>$targetArray[SHOWER_SUNPOU_COLUMN_ITEM],
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
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->insertDB($dataArray);
			}
		}

		// システムステータス更新
		$dbCheck = $this->systemUpdate(SYSTEM_STATUS_PDF_WAIT, $this->pdfTime);

		return $dbCheck;
	}

	/**
	 * カタログPDFファイル名作成
	 * @param	$year		年
	 * @param	$page		ページ
	 * @return	$fileName	ファイル名
	 */
	protected function makeCatalogFileName($year, $page) {
		$fileName = "";
		$nextYear = $year + 1;

		$fileName = $year."-".$nextYear."_".$page.".pdf";

		return $fileName;
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
			$this->{KEY_DB_CHECK_MESSAGE} = "商品CSVファイルのアップロードに失敗しました。<br>";
		}

		return $result;
	}
}

?>
