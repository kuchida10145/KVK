#!/usr/local/bin/php
<?php
include(dirname(__FILE__).'/../../Page.php');
require(dirname(__FILE__).'/../../system/plugin/pdf/tcpdf/tcpdf.php');
require(dirname(__FILE__).'/../../system/plugin/pdf/fpdi/fpdi.php');
class MakePDF extends Page{

	function __construct() {
		parent::__construct();

		// システム状態取得
		$this->sytemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();

		// アップロードしたパーツcsvファイル
		$this->uploadInfo = CSV_FOLDER.CSV_FILE_NAME_ONETIME_PARTS;

		// pdf作成中パーツcsvファイル
		$this->makingPdfCsvInfo = CSV_FOLDER.MAKING_PDF_CSV;

		// アップロードした商品csvファイル
		$this->uploadInfoItem = CSV_FOLDER.CSV_FILE_NAME_ONETIME_ITEM;

		// pdf作成中商品csvファイル
		$this->makingPdfCsvInfoItem = CSV_FOLDER.MAKING_PDF_ITEM_CSV;

		// バックアップ商品csvファイル
		$this->backupCsvInfoItem = CSV_FOLDER.BACK_UP_ITEM_CSV;

		// 出力csvファイルのヘッダー行（パーツ）
		$this->csvHeader = array(
				'番号',
				'品番',
				'品名',
				'希望小売価格',
				'税込',
				'品番',
				'分解図',
				'備考',
				'削除',
		);

		// 出力csvファイルのヘッダー行（商品）
		$this->csvHeaderItem = array(
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

	public function executeMakePdf($executePath) {
		// pdf作成時間取得
		$this->getMakePdfTime();
		$nowData = date("Y-m-d H:i:s");
		$pdfTime = $this->dayVal." ".$this->dayHour.":".$this->dayMin.":"."00";
		$result = true;

		// 商品csvファイル存在チェック
		if(!file_exists($this->makingPdfCsvInfoItem)) {
			// システムステータス更新（pdf作成待ち：1⇒pdf作成中：2）
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_MAKE);
		}

		// システムステータスチェック
		$systemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();
		if($systemStatus == SYSTEM_STATUS_PDF_WAIT) {
			// 日付チェック
			if($nowData > $pdfTime) {
				// 商品データ更新
				$result = $this->itemUpdate($executePath);
				if(!file_exists($this->makingPdfCsvInfo) && !file_exists($this->makingPdfCsvInfoItem)) {
					$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_FINISH);
				}
			}
		} elseif($systemStatus == SYSTEM_STATUS_PDF_MAKE) {
			// 部品csvファイル存在チェック
			if(!file_exists($this->makingPdfCsvInfo)) {
				// システムステータス更新（pdf作成中：2⇒pdf作成完了：3）
				$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_FINISH);
			} elseif($nowData > $pdfTime) {
				// 部品データ更新
				$result = $this->partsUpdate($executePath);
			}
		} elseif($systemStatus == SYSTEM_STATUS_PDF_STOP) {
			// csv作成中のcsvファイルを削除する
			$result = unlink($this->makingPdfCsvInfo);
			$result = unlink($this->makingPdfCsvInfoItem);
			// システムステータス更新
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_NORMAL);
		} elseif ($systemStatus == SYSTEM_STATUS_PDF_FINISH) {
			// システムステータス更新
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_NORMAL);
		}
	}

	/**
	 * 部品データ更新
	 * @param	-
	 * @return	$result					データ更新結果
	 */
	public function partsUpdate($executePath) {
		$result = true;
		$nowArray = array();	// アップデートしたcsvファイル
		$oldArray = array();	// 前回までの更新した値
		$targetArray = array();	// 差異
		$checkArray = array();
		$updateArray = array();	// 更新対象配列
		$pdfArray = array();
		$makePdfArray = array();
		$nextArray = array();
		$itemUpdateKey = array();
		$dataCount = 0;
		$pdfCount = 0;
		$dataGetFlg = false;
		$pdfResult = false;
		$itemUpload = true;
		$dbPartsCheck = true;

		// ファイル存在チェック
		if(file_exists($this->makingPdfCsvInfo)) {
			//DB更新処理実行
			$pdfArray = $this->getCsvData($this->makingPdfCsvInfo);
			$endCount = count($pdfArray) - 1;
			// 部品データ更新
			foreach($pdfArray as $key=>$value) {
				if($dataCount == 0){
					$dataCount = $dataCount + 1;
					continue;
				}

				// pdfを一定数以上作成したら残りのデータをバックアップ
				if($pdfCount > 15) {
					$updateArray[] = $value;
					continue;
				}

				// データ取り込みフラグ変更
				if($value[NO_COLUMN_PARTS] == 1 || $dataCount == $endCount) {
					if($dataGetFlg) {
						$dataGetFlg = false;
						if($value[NO_COLUMN_PARTS] == 1) {
							$nextArray[] = $value;
						} else {
							$makePdfArray[] = $value;
						}
					} else {
						$dataGetFlg = true;
					}
				}

				// データ取得判定
				if($dataGetFlg) {
					if(!empty($nextArray)) {
						$makePdfArray = $nextArray;
						$nextArray = array();
						$itemUpload = true;
					}
					// pdf作成用データ取得
					$makePdfArray[] = $value;
				} else {
					// pdf作成
					$pdfResult = $this->makePdf($makePdfArray);
					// データ更新
					if($pdfResult) {
						// pdf作成成功（データ更新）
						foreach($makePdfArray as $key=>$partsRow) {
							// 商品DB登録データ生成
							$dataArray = array(
									// 番号（部品表示順）
									COLUMN_NAME_NO=>$partsRow[NO_COLUMN_PARTS],
									// 品番（パーツ）
									COLUMN_NAME_PARTS_ID=>$partsRow[PARTS_ID_COLUMN_PARTS],
									// 品名（パーツ）
									COLUMN_NAME_PARTS_NAME=>$partsRow[PARTS_NAME_COLUMN_PARTS],
									// 希望小売価格
									COLUMN_NAME_PRICE=>$partsRow[PRICE_COLUMN_PARTS],
									// 希望小売価格（税込み）
									COLUMN_NAME_PRICE_ZEI=>$partsRow[PRICE_ZEI_COLUMN_PARTS],
									// 品番（商品）
									COLUMN_NAME_ITEM_ID=>$partsRow[ITEM_COLUMN_PARTS],
									// ファイル名
									COLUMN_NAME_FILE_NAME=>$partsRow[FILE_COLUMN_PARTS],
									// 表示フラグ
									COLUMN_NAME_VIEW_STATUS=>$partsRow[DELETE_COLUMN_PARTS],
									// 備考
									COLUMN_NAME_NOTE=>$partsRow[NOTE_COLUMN_PARTS],
							);
							// where句生成
							$whereParts = 	COLUMN_NAME_PARTS_ID." = '".$dataArray[COLUMN_NAME_PARTS_ID]."' AND "
									.COLUMN_NAME_ITEM_ID." = '".$dataArray[COLUMN_NAME_ITEM_ID]."'";
							// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
							$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->checkData($whereParts);
							if($dbPartsCheck) {
								// DBUpdate処理
								$dataArray[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
								$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->update($dataArray, $whereParts);
							} else {
								// DBinsert処理
								$dataArray[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
								$dataArray[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日
								$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->insertDB($dataArray);
							}

							// PDF作成エラーチェック
							if($dbPartsCheck && $itemUpload) {
								// 商品データ更新
								$itemUpdateKey = explode("・", $makePdfArray[0][ITEM_COLUMN_PARTS]);
								foreach ($itemUpdateKey as $key=>$value) {
									// where句生成
									$whereItem = COLUMN_NAME_ITEM_ID." = '{$value}'";
									$itemUpdateArray = array(
											// 分解図データ
											COLUMN_NAME_BUNKAI_DATA=>$this->pdfFileName,
											// PDF作成済フラグ
											COLUMN_NAME_PDF_STATUS=>MAKED,
											// 表示フラグ
											COLUMN_NAME_VIEW_STATUS=>VIEW_OK,
											// 更新日
											COLUMN_NAME_UPDATE_DATE=>date("Y-m-d H:i:s"),
									);
									// DBUpdate処理
									$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($itemUpdateArray, $whereItem);
									// エラーチェック
									if(!$dbPartsCheck) {
										$this->{KEY_ERROR_MESSAGE} = $this->{KEY_ERROR_MESSAGE}.MESSAGE_FAIL_UPDATE_ITEM.$itemRow[COLUMN_NAME_ITEM_ID]."<br>";
										$result = false;
									}
								}
								$itemUpload = false;
							} elseif(!$dbPartsCheck && $itemUpload) {
								// 商品データを非表示にする
								$itemUpdateKey = explode("・", $makePdfArray[0][ITEM_COLUMN_PARTS]);
								foreach ($itemUpdateKey as $key=>$value) {
									// where句生成
									$whereItem = COLUMN_NAME_ITEM_ID." = '{$value}'";
									$itemUpdateArray = array(
											// 表示フラグ
											COLUMN_NAME_VIEW_STATUS=>VIEW_NG,
											// 更新日
											COLUMN_NAME_UPDATE_DATE=>date("Y-m-d H:i:s"),
									);
									// DBUpdate処理
									$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($itemUpdateArray, $whereItem);
									// エラーチェック
									if(!$dbPartsCheck) {
										$this->{KEY_ERROR_MESSAGE} = $this->{KEY_ERROR_MESSAGE}.MESSAGE_FAIL_UPDATE_ITEM.$itemRow[COLUMN_NAME_ITEM_ID]."<br>";
									}
								}
								$this->{KEY_ERROR_MESSAGE} = $this->{KEY_ERROR_MESSAGE}.MESSAGE_FAIL_UPDATE_PARTS.$dataArray[COLUMN_NAME_PARTS_ID]."<br>";
								$result = false;
								$itemUpload = false;
							}
						}
					} else {
						// pdf作成失敗（データ更新しない）
						if(!$dbPartsCheck) {
							$this->{KEY_ERROR_MESSAGE} = "pdfの作成が失敗しました。ファイル名：{$value[FILE_COLUMN_PARTS]}<br>";
						}
					}
					// データ取得フラグを立てる
					$dataGetFlg = true;
					// pdf作成数をカウント
					$pdfCount = $pdfCount + 1;
				}
				// csvデータ数をカウント
				$dataCount = $dataCount + 1;
			}
		} else {
			// pdf作成対象データを取得
			$nowArray = $this->getCsvData($this->uploadInfo);
			foreach($nowArray as $key=>$value) {
				if($key == "0") {
					continue;
				}
				// where句生成
				$whereParts = 	COLUMN_NAME_NO." = '".$value[PARTS_ID_COLUMN_PARTS]."' AND "
						.COLUMN_NAME_ITEM_ID." = '".$value[ITEM_COLUMN_PARTS]."'";
				// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
				$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->checkData($whereParts);
				if($dbPartsCheck) {
					$targetArray = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->updateCheck($value, $whereParts);
					$checkArray = array_diff($value, $targetArray);
					if(!empty($checkArray)){
						$updateArray[] = $value;
					}
				} else {
					$updateArray[] = $value;
				}
			}
		}

		if(empty($updateArray)) {
			// csvファイル削除
			$result = unlink($this->makingPdfCsvInfo);
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_FINISH);
		} else {
			// csvファイル作成
			$result = $this->setExport($updateArray, $executePath);
		}

		return $result;
	}

	/**
	 * 商品データ更新
	 * @param	$path					csvアップロードフォルダ
	 * @return	$result					データ更新結果
	 */
	public function itemUpdate($path) {
		$result = true;
		$nowArray = array();	// アップデートしたcsvファイル
		$oldArray = array();	// 前回までの更新した値
		$targetArray = array();	// 差異
		$checkArray = array();
		$updateArray = array();	// 更新対象配列
		$pdfArrat = array();
		$makeCount = 0;
		$limit = "";
		$order = "";
		$errorMessage = array();

		// 商品データ更新
		if(file_exists($this->makingPdfCsvInfoItem)) {
			$pdfArrat = $this->getCsvData($this->makingPdfCsvInfoItem);
			foreach($pdfArrat as $key=>$targetArray) {
				if($makeCount == 0){
					$makeCount = $makeCount + 1;
					continue;
				}

				if($makeCount < 500) {
					$dataArray = $this->convertCsvData($targetArray);

					// where句
					// カテゴリID分解
					$parentIDVal = substr($targetArray[CATEGORY_ID_COLUMN_ITEM], -4, 1);
					$categoryIDVal = substr($targetArray[CATEGORY_ID_COLUMN_ITEM], -3);
					$where = COLUMN_NAME_ITEM_ID." = '".$targetArray[ITEM_ID_COLUMN_ITEM].
					"' AND ".COLUMN_NAME_PARENT_ID." = '".$parentIDVal.
					"' AND ".COLUMN_NAME_CATEGORY_ID." = '".$categoryIDVal."'";

					// 削除フラグ取得
					$deleteFlg = $this->convertDeleteFlg($targetArray[DELETE_COLUMN_ITEM]);
					//削除フラグチェック
					if($deleteFlg){
						// DB削除処理(表示フラグ更新)
						$dbCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($dataArray, $where);
					} else {
						// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
						$dbCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->checkData($where);
						if($dbCheck) {
							// DBUpdate処理
							$dbCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($dataArray, $where);
						} else {
							// DBinsert処理
							$dataArray[COLUMN_NAME_ITEM_STATUS] = "";					// 商品ステータス
							$dataArray[COLUMN_NAME_PDF_STATUS] = NOT_MAKE;				// pdf未作成
							$dataArray[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日追加
							$dbCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->insertDB($dataArray);
						}

						if(!$dbCheck) {
							$errorMessage[] = "商品データの登録に失敗しました。品番：{$targetArray[ITEM_ID_COLUMN_ITEM]}<br>";
							$result = false;
						}
					}
				} else {
					$updateArray[] = $targetArray;
				}
				$makeCount = $makeCount + 1;
			}
		} else {
			// 商品データの差分を取得（csvファイルとDB）
			$nowArray = $this->getCsvData($this->uploadInfoItem);
			foreach($nowArray as $key=>$value) {
				if($key == "0") {
					continue;
				}
				// where句
				// カテゴリID分解
				$parentIDVal = substr($value[CATEGORY_ID_COLUMN_ITEM], -4, 1);
				$categoryIDVal = substr($value[CATEGORY_ID_COLUMN_ITEM], -3);
				$where = COLUMN_NAME_ITEM_ID." = '".$value[ITEM_ID_COLUMN_ITEM].
				"' AND ".COLUMN_NAME_PARENT_ID." = '".$parentIDVal.
				"' AND ".COLUMN_NAME_CATEGORY_ID." = '".$categoryIDVal."'";
				// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
				$dbCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->checkData($where);
				if($dbCheck) {
					$dataArray = $this->convertCsvData($value);
					$targetArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->updateCheck($value, $where);
					//					$checkArray = array_diff($value, $targetArray);
					if(!empty($targetArray)){
						$updateArray[] = $value;
					}
				} else {
					$updateArray[] = $value;
				}
			}
		}

		if(empty($updateArray)) {
			// csvファイル削除
			$result = unlink($this->makingPdfCsvInfoItem);
			// システムステータス更新（pdf作成待ち：1⇒pdf作成中：2）
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_MAKE);
			// バックアップcsv作成
			$result = $this->backupItem();
		} else {
			// csvファイル作成
			$result = $this->setExportItem($updateArray, $path);
		}

		// エラーメッセージセット
		if(!$result) {
			foreach ($errorMessage as $key=>$value) {
				$errorMessageValue = $errorMessageValue.$value;
			}
			$this->{KEY_ERROR_MESSAGE} = $errorMessageValue;
		}

		return $result;
	}

	/**
	 * 商品csvデータ加工
	 * @param	array $csvData	csvデータ
	 * @return	array $dbData	DB更新用データ
	 */
	protected function convertCsvData($csvData) {
		// カタログファイル名作成
		$catalogLink = $this->makeCatalogFileName(
				$csvData[CATALOG_YEAR_COLUMN_ITEM], $csvData[CATALOG_PAGE_COLUMN_ITEM]);

		// カテゴリID分解
		$parentIDVal = substr($csvData[CATEGORY_ID_COLUMN_ITEM], -4, 1);
		$categoryIDVal = substr($csvData[CATEGORY_ID_COLUMN_ITEM], -3);

		//DB登録データ作成
		// 商品DB登録データ生成
		$dbData = array(
				// 品番（商品）
				COLUMN_NAME_ITEM_ID=>$csvData[ITEM_ID_COLUMN_ITEM],
				// 品名
				COLUMN_NAME_ITEM_NAME=>$csvData[ITEM_NAME_COLUMN_ITEM],
				// 表示ステータス
				COLUMN_NAME_VIEW_STATUS=>$csvData[DELETE_COLUMN_ITEM],
				// 希望小売価格
				COLUMN_NAME_PRICE=>$csvData[PRICE_COLUMN_ITEM],
				// 希望小売価格（税込み）
				COLUMN_NAME_PRICE_ZEI=>$csvData[PRICE_ZEI_COLUMN_ITEM],
				// 図面データ
				COLUMN_NAME_MAP_DATA=>$csvData[MAP_COLUMN_ITEM],
				// 取説データ
				COLUMN_NAME_TORISETSU_DATA=>$csvData[TORISETSU_COLUMN_ITEM],
				// 工説データ
				COLUMN_NAME_KOUSETSU_DATA=>$csvData[SEKOU_COLUMN_ITEM],
				// 分解図データ
				COLUMN_NAME_BUNKAI_DATA=>$csvData[BUNKAI_COLUMN_ITEM],
				// シャワーデータ
				COLUMN_NAME_SHOWER_DATA=>$csvData[SHOWER_COLUMN_ITEM],
				// 購入フラグ
				COLUMN_NAME_BUY_STATUS=>$csvData[BUY_STATUS_COLUMN_ITEM],
				// カタログへのリンク
				COLUMN_NAME_CATALOG_LINK=>$catalogLink,
				// バリエーション親品番
				COLUMN_NAME_PARENT_VARIATION=>$csvData[VARIATION_NAME_COLUMN_ITEM],
				// バリエーション表示順
				COLUMN_NAME_VARIATION_NO=>$csvData[VARIATION_NO_COLUMN_ITEM],
				// 備考
				COLUMN_NAME_NOTE=>$csvData[NOTE_COLUMN_ITEM],
				// 商品イメージ画像
				COLUMN_NAME_ITEM_IMAGE=>$csvData[ITEM_PHOTO_COLUMN_ITEM],
				// 親カテゴリID
				COLUMN_NAME_PARENT_ID=>$parentIDVal,
				// 子カテゴリID
				COLUMN_NAME_CATEGORY_ID=>$categoryIDVal,
				// 検索ワード
				COLUMN_NAME_SEARCH_WORD=>$csvData[SEARCH_WORD_COLUMN_ITEM],
				// 分岐金具1
				COLUMN_NAME_BUNKI_KANAGU_1=>$csvData[BUNKI_KANAGU_1_COLUMN_ITEM],
				// 分岐金具2
				COLUMN_NAME_BUNKI_KANAGU_2=>$csvData[BUNKI_KANAGU_2_COLUMN_ITEM],
				// 分岐金具3
				COLUMN_NAME_BUNKI_KANAGU_3=>$csvData[BUNKI_KANAGU_3_COLUMN_ITEM],
				// 販売時期
				COLUMN_NAME_SELL_TIME=>$csvData[SELL_KIKAN_COLUMN_ITEM],
				// 代替品
				COLUMN_NAME_SUB_ITEM=>$csvData[DAIGAE_COLUMN_ITEM],
				// 本体取付穴
				COLUMN_NAME_SUNPOU=>$csvData[SUNPOU_COLUMN_ITEM],
				// ピッチ
				COLUMN_NAME_PITCH=>$csvData[PITCH_COLUMN_ITEM],
				// シャワー取付穴
				COLUMN_NAME_SHOWER_SUNPOU=>$csvData[SHOWER_SUNPOU_COLUMN_ITEM],
				// 更新日
				COLUMN_NAME_UPDATE_DATE=>date("Y-m-d H:i:s"),
			);
		return $dbData;
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

	/**
	 * csvファイル出力メイン処理(商品)
	 * @param	array()		csv保存対象データ
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExportItem($updateArray, $executePath) {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;
		$makeFilePath = $this->makingPdfCsvInfoItem;

		// csvファイル書き込み
		$filePointer = fopen($makeFilePath, 'w');
		$headerArray = $this->csvHeaderItem;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
		fputcsv($filePointer, $headerArray);

		foreach ($updateArray as $itemDataRow){
			$csvDataArray = array(
					// 品番
					$itemDataRow[ITEM_ID_COLUMN_ITEM],
					// 品名
					$itemDataRow[ITEM_NAME_COLUMN_ITEM],
					// 写真
					$itemDataRow[ITEM_PHOTO_COLUMN_ITEM],
					// 図面
					$itemDataRow[MAP_COLUMN_ITEM],
					// 取説
					$itemDataRow[TORISETSU_COLUMN_ITEM],
					// 施工
					$itemDataRow[SEKOU_COLUMN_ITEM],
					// 分解図本体
					$itemDataRow[BUNKAI_COLUMN_ITEM],
					// 分解図_シャワー
					$itemDataRow[SHOWER_COLUMN_ITEM],
					// 購入
					$itemDataRow[BUY_STATUS_COLUMN_ITEM],
					// 価格
					$itemDataRow[PRICE_COLUMN_ITEM],
					// 価格（税込み）
					$itemDataRow[PRICE_ZEI_COLUMN_ITEM],
					// 備考
					$itemDataRow[NOTE_COLUMN_ITEM],
					// 商品イメージ
					$itemDataRow[ITEM_IMAGE_COLUMN_ITEM],
					// バリエーション親品番
					$itemDataRow[VARIATION_NAME_COLUMN_ITEM],
					// バリエーション順序
					$itemDataRow[VARIATION_NO_COLUMN_ITEM],
					// カタログ年度
					$itemDataRow[CATALOG_YEAR_COLUMN_ITEM],
					// カタログページ
					$itemDataRow[CATALOG_PAGE_COLUMN_ITEM],
					// 検索ワード
					$itemDataRow[SEARCH_WORD_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_1_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_2_COLUMN_ITEM],
					// 分岐金具
					$itemDataRow[BUNKI_KANAGU_3_COLUMN_ITEM],
					// 発売時期
					$itemDataRow[SELL_KIKAN_COLUMN_ITEM],
					// 代替品
					$itemDataRow[DAIGAE_COLUMN_ITEM],
					// 本体取付穴
					$itemDataRow[SUNPOU_COLUMN_ITEM],
					// ピッチ
					$itemDataRow[PITCH_COLUMN_ITEM],
					// シャワーS取付穴
					$itemDataRow[SHOWER_SUNPOU_COLUMN_ITEM],
					// 削除
					$itemDataRow[DELETE_COLUMN_ITEM],
					// カテゴリID
					$itemDataRow[CATEGORY_ID_COLUMN_ITEM],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		return $result;
	}

	/**
	 * バックアップ用csvファイル出力
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function backupItem() {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;
		$makeFilePath = $this->backupCsvInfoItem;

		// csvファイル書き込み
		$filePointer = fopen($makeFilePath, 'w');
		$headerArray = $this->csvHeaderItem;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->getAll();

		foreach ($itemCodeArray as $itemDataRow){
			// カタログページ分割
			$fileName = explode(".",$itemDataRow[COLUMN_NAME_CATALOG_LINK]);
			$catalogYear = explode("-",$fileName[0]);
			$catalogPage = explode("_",$catalogYear[1]);

			$csvDataArray = array(
					// 品番
					$itemDataRow[COLUMN_NAME_ITEM_ID],
					// 品名
					$itemDataRow[COLUMN_NAME_ITEM_NAME],
					// 写真
					$itemDataRow[COLUMN_NAME_ITEM_IMAGE],
					// 図面
					$itemDataRow[COLUMN_NAME_MAP_DATA],
					// 取説
					$itemDataRow[COLUMN_NAME_TORISETSU_DATA],
					// 施工
					$itemDataRow[COLUMN_NAME_KOUSETSU_DATA],
					// 分解図本体
					$itemDataRow[COLUMN_NAME_BUNKAI_DATA],
					// 分解図_シャワー
					$itemDataRow[COLUMN_NAME_SHOWER_DATA],
					// 購入
					$itemDataRow[COLUMN_NAME_BUY_STATUS],
					// 価格
					$itemDataRow[COLUMN_NAME_PRICE],
					// 価格（税込み）
					$itemDataRow[COLUMN_NAME_PRICE_ZEI],
					// 備考
					$itemDataRow[COLUMN_NAME_NOTE],
					// 商品イメージ
					$itemDataRow[COLUMN_NAME_ITEM_IMAGE],
					// バリエーション親品番
					$itemDataRow[COLUMN_NAME_PARENT_VARIATION],
					// バリエーション順序
					$itemDataRow[COLUMN_NAME_VARIATION_NO],
					// カタログ年度
					$catalogYear[0],
					// カタログページ
					$catalogPage[1],
					// 検索ワード
					$itemDataRow[COLUMN_NAME_SEARCH_WORD],
					// 分岐金具
					$itemDataRow[COLUMN_NAME_BUNKI_KANAGU_1],
					// 分岐金具
					$itemDataRow[COLUMN_NAME_BUNKI_KANAGU_2],
					// 分岐金具
					$itemDataRow[COLUMN_NAME_BUNKI_KANAGU_3],
					// 発売時期
					$itemDataRow[COLUMN_NAME_SELL_TIME],
					// 代替品
					$itemDataRow[COLUMN_NAME_SUB_ITEM],
					// 本体取付穴
					$itemDataRow[COLUMN_NAME_SUNPOU],
					// ピッチ
					$itemDataRow[COLUMN_NAME_PITCH],
					// シャワーS取付穴
					$itemDataRow[COLUMN_NAME_SHOWER_SUNPOU],
					// 削除
					$itemDataRow[COLUMN_NAME_VIEW_STATUS],
					// カテゴリID
					$itemDataRow[COLUMN_NAME_PARENT_ID].$itemDataRow[COLUMN_NAME_CATEGORY_ID]
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);
		return $result;
	}



	/**
	 * @param array $pdfArray ＰＤＦ作成部品データ
	 * @return bool
	 */
	public function makePdf($pdfArray){

		//PDFセーブパス
		$save_path = PDF_SAVE_DIR;

		//画像イメージ
		$image = '';

		$objPdf = new FPDI('L','mm','A4');
		$objPdf->setPrintHeader(false);
		$objPdf->AddPage();
		$objPdf->setSourceFile(PDF_TEMPLATE);
		$iIndex = $objPdf->importPage(1);
		$objPdf->useTemplate($iIndex);
		$objPdf->SetFont("kozgopromedium", "", 16);

		if(!is_array($pdfArray)){
			return false;
		}
		//画像取得

		$file_name = getParam($pdfArray[0],FILE_COLUMN_PARTS);
		if($file_name == ''){
			return false;
		}

		//分解図名
		$name = getParam($pdfArray[0],ITEM_COLUMN_PARTS);
		if($name == ''){
			return false;
		}
		$objPdf->SetXY(45.0, 21.0);
		$objPdf->Write(4,$name);

		$objPdf->SetFont("kozgopromedium", "", 5);

		$width_ar =array(
				'parts_no'  =>5,
				'parts_id'   =>20,
				'parts_name'=>40,
				'price_zei' =>24,
				'note'      =>15);
		$objPdf->SetXY(180.0, 30.0);
		$line = 3;

		$objPdf->Cell($width_ar['parts_no'],  $line, '番号', 1,0, 'C');
		$objPdf->Cell($width_ar['parts_id'],   $line, '品番', 1,0, 'C');
		$objPdf->Cell($width_ar['parts_name'],$line, '品名', 1,0, 'C');
		$objPdf->Cell($width_ar['price_zei'], $line, '希望小売価格(税込み)', 1,0, 'C');
		$objPdf->Cell($width_ar['note'],      $line, '備考', 1,1, 'C');

		$passCount = 0;	// 非表示行カウント
		foreach($pdfArray as $key => $parts){
			// 部品データが非表示の場合データを書き込まない
			if($parts[DELETE_COLUMN_PARTS] == 1) {
				$passCount = $passCount + 1;
				continue;
			}
			$objPdf->SetX(180.0);
			if($parts[NO_COLUMN_PARTS] != ''){
				$price = '￥'.number_format($parts[PRICE_COLUMN_PARTS]).'(税込￥'.number_format($parts[PRICE_ZEI_COLUMN_PARTS]).')';
				$objPdf->Cell($width_ar['parts_no']  , $line, $parts[NO_COLUMN_PARTS] - $passCount , 1,0, 'C');
				$objPdf->Cell($width_ar['parts_id']   , $line, $parts[PARTS_ID_COLUMN_PARTS] , 1);
				$objPdf->Cell($width_ar['parts_name'], $line, $parts[PARTS_NAME_COLUMN_PARTS] , 1);
				$objPdf->Cell($width_ar['price_zei'] , $line, $price , 1);
				$objPdf->Cell($width_ar['note'] ,      $line, $parts[NOTE_COLUMN_PARTS] , 1,1);
			}
			else{
				$objPdf->Cell(104, $line, $parts[PARTS_ID_COLUMN_PARTS], 1,1);
			}
		}

		$objPdf->Image(PDF_IMAGE_DIR.'/'.$file_name, 20, 30,150);

		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $name);
		$objPdf->Output(PDF_SAVE_DIR.'/'.$name.'.pdf', 'F');

		// pdfファイル名を保持
		$this->pdfFileName = $name.'.pdf';

		return true;
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
	 * 削除フラグ変換
	 * @param	$dalete_flg	削除フラグ
	 * @return	$result		変換後削除フラグ（true：非表示（データ削除扱い）, false：表示）
	 */
	public function convertDeleteFlg($dalete_flg){
		$result = false;

		if ($dalete_flg == DELETE_FLG){
			$result = true;
		}

		return $result;
	}
}

$makePDF = new MakePDF();
$makePDF->executeMakePdf(dirname(__FILE__));
