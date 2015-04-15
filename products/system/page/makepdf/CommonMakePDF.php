<?php
include(dirname(__FILE__).'/../../../Page.php');
require(dirname(__FILE__).'/../../plugin/pdf/tcpdf/tcpdf.php');
require(dirname(__FILE__).'/../../plugin/pdf/fpdi/fpdi.php');
class CommonMakePDF extends Page{

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

	/**
	 * 画面表示データ取得
	 * @param -
	 * @return array $returnArray
	 */
	public function getViewData() {
		$dataArray = array();
		$returnArray = array();

		// システム状態取得
		$dataArray = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getAll();
		$returnArray = $dataArray[0];

		if($returnArray[COLUMN_NAME_SYSTEM_STATUS] != SYSTEM_STATUS_PDF_WAIT) {
			$returnArray[COLUMN_NAME_PDF_TIME] = "-";
		}

		return $returnArray;
	}

	/**
	 * システム状態変換
	 * @param	$statusID	システムステータスID
	 * @return	$resultVal	ID変換後
	 */
	public function convertSystemStatus($statusID) {
		$resultVal = "";

		if($statusID == SYSTEM_STATUS_NORMAL) {
			$resultVal = SYSTEM_STATUS_NORMAL_VAL;
		} elseif ($statusID == SYSTEM_STATUS_PDF_WAIT) {
			$resultVal = SYSTEM_STATUS_PDF_WAIT_VAL;
		} elseif ($statusID == SYSTEM_STATUS_PDF_MAKE) {
			$resultVal = SYSTEM_STATUS_PDF_MAKE_VAL;
		} elseif ($statusID == SYSTEM_STATUS_PDF_FINISH) {
			$resultVal = SYSTEM_STATUS_PDF_FINISH_VAL;
		} elseif ($statusID == SYSTEM_STATUS_PDF_STOP) {
			$resultVal = SYSTEM_STATUS_PDF_STOP_VAL;
		}

		return $resultVal;
	}

	/**
	 * pdf作成予約解除（1:pdf作成待ち）
	 * @param	-
	 * @return	$result
	 */
	public function unsetSystemStatus($nowStatus) {
		$systemArray = "";

		$systemArray = array(
				COLUMN_NAME_SYSTEM_STATUS=>SYSTEM_STATUS_NORMAL,
				COLUMN_NAME_PDF_TIME=>"",
		);
		// システムステータス更新
		$dbCheck = $this->systemUpdate($systemArray, $nowStatus);

		if($dbCheck) {
			$this->{KEY_ERROR_MESSAGE} = MESSAGE_UNSET_PDF;
		} else {
			$this->{KEY_ERROR_MESSAGE} = ERROR_UNSET_PDF;
		}

		return $dbCheck;
	}

	/**
	 * システムステータス更新（status = 0:通常 or 1:pdf作成待ち）
	 * @param	-
	 * @return	$result
	 */
	public function setSystemStatus($nowStatus) {
		$systemArray = "";

		$systemArray = array(
				COLUMN_NAME_SYSTEM_STATUS=>SYSTEM_STATUS_PDF_WAIT,
				COLUMN_NAME_PDF_TIME=>$this->pdfTime,
		);
		// システムステータス更新
		$dbCheck = $this->systemUpdate($systemArray, $nowStatus);

		if($dbCheck) {
			$this->{KEY_ERROR_MESSAGE} = MESSAGE_SET_PDF_TIME;
		} else {
			$this->{KEY_ERROR_MESSAGE} = ERROR_SET_PDF_TIME;
		}

		return $dbCheck;
	}

	/**
	 * pdf作成中止
	 * @param	-
	 * @return	$result
	 */
	public function stopSystemStatus($nowStatus) {
		$systemArray = "";

		$systemArray = array(
				COLUMN_NAME_SYSTEM_STATUS=>SYSTEM_STATUS_PDF_STOP,
				COLUMN_NAME_PDF_TIME=>"",
		);
		// システムステータス更新
		$dbCheck = $this->systemUpdate($systemArray, $nowStatus);

		if($dbCheck) {
			$this->{KEY_ERROR_MESSAGE} = MESSAGE_STOP_PDF;
		} else {
			$this->{KEY_ERROR_MESSAGE} = ERROR_STOP_PDF;
		}

		return $dbCheck;
	}

	/**
	 * エラーメッセージ取得
	 * @return	$message	エラーメッセージ
	 */
	public function getErrorMessage() {
		return $this->{KEY_ERROR_MESSAGE};
	}

	/**
	 * 結果メッセージ取得
	 * @param	$result		取込結果(true：成功 false：失敗)
	 * @return	$message	実行結果メッセージ
	 */
	public function getResultMessage($result) {
		$message = "";

		if($result) {
			$message = RESULT_MSG_OK;
		} else {
			$message = RESULT_MSG_NG;
		}

		return $message;
	}

	/**
	 * 商品データおよび部品データ更新
	 * @param	$itemArray, $partsArray	更新対象データ
	 * @return	$result					データ更新結果
	 */
	public function  updateData() {
		$result = true;
		$onetimeFileArray = array();
		$partsArray = array();
		$itemArray = array();
		$wherePdfParts = "";
		$whereParts = "";
		$wherePdfItem = "";
		$whereItem = "";
		$dbPartsCheck = "";
		$dbItemCheck = "";
		$limit = "";
		$order = "";

		// 作成済みのpdfファイルを取得する
		$directory_pass= PDF_ROOT_FOLDER.ONETIME_PDF_FOLDER;
		if ($handle = opendir($directory_pass)) {
			while (false !== ($entry = readdir($handle))) {
				$onetimeFileArray[] = $entry;
			}
		}

		// pdfファイルをキーに部品データを取得
		foreach ($onetimeFileArray as $rowFileName) {
			// 更新データ取得
			$wherePdfParts = COLUMN_NAME_FILE_NAME.' = "'.$rowFileName.'"';
			$partsArray = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->search($wherePdfParts, $limit, $order);

			// 部品データ更新
			foreach($partsArray as $partsRow) {
				// where句生成
				$whereParts = 	COLUMN_NAME_NO." = '".$partsRow[COLUMN_NAME_NO]."' AND "
								.COLUMN_NAME_PARTS_ID." = '".$partsRow[COLUMN_NAME_PARTS_ID]."' AND "
								.COLUMN_NAME_FILE_NAME." = '".$partsRow[COLUMN_NAME_FILE_NAME]."'";
				// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
				$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->checkData($whereParts);
				if($dbPartsCheck) {
					// DBUpdate処理
					$partsRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
					$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->update($partsRow, $whereParts);
				} else {
					// DBinsert処理
					$partsRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
					$partsRow[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日
					$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->insertDB($partsRow);
				}

				// エラーチェック
				if(!$dbPartsCheck) {
					$this->{KEY_ERROR_MESSAGE} = MESSAGE_FAIL_UPDATE_PARTS.$partsRow[COLUMN_NAME_PARTS_ID]."<br>";
					$result = false;
				}
			}

			// 更新データ取得
			$wherePdfItem = COLUMN_NAME_ITEM_ID." = '".$partsRow[COLUMN_NAME_ITEM_ID]."' AND ".COLUMN_NAME_BUNKAI_DATA." = '".$rowFileName."'";
			$itemArray = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->search($wherePdfItem, $limit, $order);

			// 商品データ更新
			foreach($itemArray as $itemRow) {
				// where句生成
				$whereItem = COLUMN_NAME_ITEM_ID." = '".$itemRow[COLUMN_NAME_ITEM_ID]."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$itemRow[COLUMN_NAME_CATEGORY_ID]."'";
				// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
				$dbItemCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->checkData($whereItem);
				if($dbItemCheck) {
					// DBUpdate処理
					$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
					$dbItemCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($itemRow, $whereItem);
				} else {
					// DBinsert処理
					$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
					$itemRow[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日
					$dbItemCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->insertDB($itemRow);
				}

				// エラーチェック
				if(!$dbItemCheck) {
					$this->{KEY_ERROR_MESSAGE} = MESSAGE_FAIL_UPDATE_ITEM.$itemRow[COLUMN_NAME_ITEM_ID]."<br>";
					$result = false;
				}
			}

			if($result) {
				// 更新対象ファイルをバックアップフォルダへ移動する
				$oldPath = UPLOAD_FOLDER.$rowFileName;
				$backupFileName = basename($rowItem[COLUMN_NAME_BUNKAI_DATA], '.pdf').date("YmdHis").".pdf";
				$newPath = PDF_ROOT_FOLDER.BUCKUP_PDF_FOLDER.$backupFileName;
				rename($oldPath, $newPath);
				// pdfファイルをアップロード
				$oldPath = PDF_ROOT_FOLDER.ONETIME_PDF_FOLDER.$rowFileName;
				$newPath = UPLOAD_FOLDER.$rowFileName;
				rename($oldPath, $newPath);
			}
		}

		return $result;
	}

	public function executeMakePdf($executePath) {
		// pdf作成時間取得
		$this->getMakePdfTime();
		$nowData = date("Y-m-d H:i:s");
		$pdfTime = $this->dayVal." ".$this->dayHour.":".$this->dayMin.":"."00";
		$result = true;

		// csvファイル存在チェック
		if(!file_exists($this->uploadInfoItem)) {
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
			}
		} elseif($systemStatus == SYSTEM_STATUS_PDF_MAKE) {
			// csvファイル存在チェック
			if(!file_exists($this->uploadInfo)) {
				// システムステータス更新（pdf作成中：2⇒pdf作成完了：3）
				$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_FINISH);
			}
			// 日付チェック
			if($nowData > $pdfTime) {
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

		return $result;
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

		// ファイル存在チェック
		if(file_exists($this->makingPdfCsvInfo)) {
			//DB更新処理実行
			$pdfArray = $this->getCsvData($this->makingPdfCsvInfo);
			// 部品データ更新
			foreach($pdfArray as $key=>$value) {
				if($dataCount == 0){
					$dataCount = $dataCount + 1;
					continue;
				}

				// pdfを一定数以上作成したら残りのデータをバックアップ
				if($pdfCount > 10) {
					$updateArray[] = $value;
					continue;
				}

				// データ取り込みフラグ変更
				if($value[NO_COLUMN_PARTS] == 1) {
					if($dataGetFlg) {
						$dataGetFlg = false;
						$nextArray[] = $value;
					} else {
						$dataGetFlg = true;
					}
				}

				// データ取得判定
				if($dataGetFlg) {
					if(!empty($nextArray)) {
						$makePdfArray = $nextArray;
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
							if($dbPartsCheck) {
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
							} else {
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

		if($dataCount == 1 && $pdfCount == 0) {
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
		if(file_exists($this->makingPdfCsvInfoItem)) {
			// pdf作成、DB更新処理実行
			$pdfArrat = $this->getCsvData($this->makingPdfCsvInfoItem);
			// 商品データ更新
			foreach($pdfArrat as $key=>$targetArray) {
				if($makeCount == 0){
					$makeCount = $makeCount + 1;
					continue;
				}

				if($makeCount < 500) {
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
							$dataArray[COLUMN_NAME_PDF_STATUS] = NOT_MAKE;				// pdf未作成
							$dataArray[COLUMN_NAME_VIEW_STATUS] = VIEW_NG;				// 非表示（pdfファイルが未作成なので）
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
			// 商品データの差分を取得（csvファイルとDB）
 			$nowArray = $this->getCsvData($this->uploadInfoItem);
			foreach($nowArray as $key=>$value) {
				if($key == "0") {
					continue;
				}
				// where句
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
			$result = unlink($this->makingPdfCsvInfoItem);

			// システムステータス更新（pdf作成待ち：1⇒pdf作成中：2）
			$result = $this->systemStatusUpdate(SYSTEM_STATUS_PDF_MAKE);
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

		$file_name = getParam($pdfArray[0],'file_name');
		if($file_name == ''){
			return false;
		}

		//分解図名
		$name = getParam($pdfArray[0],'item_id');
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

		foreach($pdfArray as $key => $parts){
			$objPdf->SetX(180.0);
			if($parts['parts_no'] != ''){
				$price = '￥'.number_format($parts['price']).'(税込￥'.number_format($parts['price_zei']).')';
				$objPdf->Cell($width_ar['parts_no']  , $line, $parts['parts_no'] , 1,0, 'C');
				$objPdf->Cell($width_ar['parts_id']   , $line, $parts['parts_id'] , 1);
				$objPdf->Cell($width_ar['parts_name'], $line, $parts['parts_name'] , 1);
				$objPdf->Cell($width_ar['price_zei'] , $line, $price , 1);
				$objPdf->Cell($width_ar['note'] ,      $line, $parts['note'] , 1,1);
			}
			else{
				$objPdf->Cell(104, $line, $parts['parts_name'], 1,1);
			}
		}

		$objPdf->Image(PDF_IMAGE_DIR.'/'.$file_name.'.png', 20, 30,150);

		$objPdf->Output(PDF_SAVE_DIR.'/'.$file_name.'.pdf', 'F');

		// pdfファイル名を保持
		$this->pdfFileName = $file_name.'.pdf';

		return true;
	}
}
