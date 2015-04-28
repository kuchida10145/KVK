#!/usr/local/bin/php
<?php
include(dirname(__FILE__) . '/../../Page.php');
class updateItem extends Page{

	function __construct() {
		parent::__construct();

		// システム状態取得
		$this->sytemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();

		// アップロードしたcsvファイル
		$this->uploadInfo = CSV_FOLDER.CSV_FILE_NAME_ONETIME_ITEM;

		// データ更新中csvファイル
		$this->makingPdfCsvInfo = CSV_FOLDER.UPDATE_CSV;

		// 前回更新時に作成したcsvファイル
		$this->oldPartsDbCsv = CSV_FOLDER.OLD_PARTS_DB_CSV;

		// 出力csvファイルのヘッダー行
			$this->csvHeader = array(
				'品番',
				'品名',
				'写真',
				'図面',
				'取説',
				'施工',
				'分解図本体',
				'分解図_シャワー',
				'購入',
				'価格',
				'価格（税込み）',
				'備考',
				'商品イメージ',
				'バリエーション親品番',
				'バリエーション順序',
				'カタログ年度',
				'カタログページ',
				'検索ワード',
				'分岐金具',
				'分岐金具',
				'分岐金具',
				'発売時期',
				'代替品',
				'本体取付穴',
				'ピッチ',
				'シャワーS取付穴',
				'削除',
				'カテゴリID'
			);
	}

	function updateItem($path) {
		// pdf作成時間取得
		$this->getMakePdfTime();
		$nowData = date("Y-m-d H:i:s");
		$pdfTime = $this->dayVal." ".$this->dayHour.":".$this->dayMin.":"."00";

		// システムステータスチェック
		$systemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();
		if($systemStatus == SYSTEM_STATUS_PDF_WAIT ||
		$systemStatus == SYSTEM_STATUS_PDF_MAKE) {
			// 日付チェック
			if($nowData > $pdfTime) {
				$this->partsUpdateDebug($path);
			}
		}
	}


	/**
	 * 部品データ更新（デバッグ用）
	 * @param	-
	 * @return	$result					データ更新結果
	 */
	public function partsUpdateDebug($path) {
		$result = true;
		$nowArray = array();	// アップデートしたcsvファイル
		$oldArray = array();	// 前回までの更新した値
		$targetArray = array();	// 差異
		$checkArray = array();
		$updateArray = array();	// 更新対象配列
		$pdfArrat = array();
		$makeCount = 0;
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

		// ファイル存在チェック
		if(file_exists($this->makingPdfCsvInfo)) {
			// pdf作成、DB更新処理実行
			$pdfArrat = $this->getCsvData($this->makingPdfCsvInfo);
			// 部品データ更新
			foreach($pdfArrat as $key=>$targetArray) {
				if($makeCount == 0){
					$makeCount = $makeCount + 1;
					continue;
				}

				if($makeCount < 22) {
					//DB登録データ作成
					// 商品DB登録データ生成
					$dataArray = array(
							// 品番（商品）
							COLUMN_NAME_ITEM_ID=>$targetArray[ITEM_ID_COLUMN_ITEM],
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
				} else {
					$updateArray[] = $partsRow;
				}
				$makeCount = $makeCount + 1;
			}
		} else {
			// データの差異を取得
			$oldArray = $this->getCsvData($this->oldPartsDbCsv);
 			$nowArray = $this->getCsvData($this->uploadInfo);
 			$oldArrayCount =  count($oldArray);						// DB登録データの最大数

 			// 部品データ更新
			foreach($nowArray as $key=>$value) {
				if($key == "0") {
					continue;
				}
				// where句生成
				$where = COLUMN_NAME_ITEM_ID." = '".$keyItemID."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$keyCategoryNo."'";
				// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->checkData($where);
				if($dbCheck) {
					$targetArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->updateCheck($value, $where);
					$checkArray = array_diff($value, $targetArray);
					if(!empty($checkArray)){
						$updateArray[] = $value;
					}
				} else {
					$updateArray[] = $value;
				}
			}
		}

		if($makeCount == 1) {
			// csvファイル削除
			$result = unlink($this->makingPdfCsvInfo);
		} else {
			// csvファイル作成
			$result = $this->setExport($updateArray, $path);
		}


		return $result;
	}

	/**
	 * CSVデータ取得
	 * @param  $csvFile	csvファイルパス
	 * @return $csv		UTF-8変換後のcsvデータ
	 */
	protected function getCsvData($csvFile) {
		$csv  = array();
		// csvから取り込んだデータをUTF-8に変換する
		$data = file_get_contents($csvFile);
		$data = mb_convert_encoding($data, SYSTEM_CODE, CSV_CODE);
		$temp = tmpfile();

		fwrite($temp, $data);
		rewind($temp);

		while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
			$csv[] = $data;
		}
		fclose($temp);

		return $csv;
	}

	/**
	 * csvファイル出力メイン処理
	 * @param	array()		csv保存対象データ
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExport($updateArray, $executePath) {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;
		$makeFilePath = $this->makingPdfCsvInfo;

		// csvファイル書き込み
		$filePointer = fopen($makeFilePath, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
		fputcsv($filePointer, $headerArray);

		foreach ($updateArray as $itemDataRow){
			$csvDataArray = array(
					// 番号
					$itemDataRow[NO_COLUMN_PARTS],
					// 品番
					$itemDataRow[PARTS_ID_COLUMN_PARTS],
					// 品名
					$itemDataRow[PARTS_NAME_COLUMN_PARTS],
					// 希望小売価格
					$itemDataRow[PRICE_COLUMN_PARTS],
					// 税込
					$itemDataRow[PRICE_ZEI_COLUMN_PARTS],
					// 品番
					$itemDataRow[ITEM_COLUMN_PARTS],
					// 分解図
					$itemDataRow[FILE_COLUMN_PARTS],
					// 備考
					$itemDataRow[NOTE_COLUMN_PARTS],
					// 削除
					$itemDataRow[DELETE_COLUMN_PARTS],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		return $result;
	}
}

echo updateItem(dirname(__FILE__));
