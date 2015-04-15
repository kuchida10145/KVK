<?php
include(dirname(__FILE__) . '/../../../Page.php');
class CommonMakePDF extends Page{

	function __construct() {
		parent::__construct();

		// システム状態取得
		$this->sytemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();

		// アップロードしたcsvファイル
		$this->uploadInfo = CSV_FOLDER.CSV_FILE_NAME_ONETIME_PARTS;

		// pdf作成中csvファイル
		$this->makingPdfCsvInfo = CSV_FOLDER.MAKING_PDF_CSV;

		// 前回更新時に作成したcsvファイル
		$this->oldPartsDbCsv = CSV_FOLDER.OLD_PARTS_DB_CSV;

		// 出力csvファイルのヘッダー行
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

	/**
	 * 部品データ更新（デバッグ用）
	 * @param	-
	 * @return	$result					データ更新結果
	 */
	public function partsUpdateDebug($executePath) {
		$result = true;
		$nowArray = array();	// アップデートしたcsvファイル
		$oldArray = array();	// 前回までの更新した値
		$targetArray = array();	// 差異
		$checkArray = array();
		$updateArray = array();	// 更新対象配列
		$pdfArrat = array();
		$makeCount = 0;

		// ファイル存在チェック
		if(file_exists($this->makingPdfCsvInfo)) {
			// pdf作成、DB更新処理実行
			$pdfArrat = $this->getCsvData($this->makingPdfCsvInfo);
			// 部品データ更新
			foreach($pdfArrat as $key=>$partsRow) {
				if($makeCount == 0){
					$makeCount = $makeCount + 1;
					continue;
				}

				if($makeCount < 2) {
					//DB登録データ作成
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
					$whereParts = 	COLUMN_NAME_NO." = '".$dataArray[COLUMN_NAME_PARTS_ID]."' AND "
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

					// エラーチェック
					if(!$dbPartsCheck) {
						$this->{KEY_ERROR_MESSAGE} = $this->{KEY_ERROR_MESSAGE}.MESSAGE_FAIL_UPDATE_PARTS.$dataArray[COLUMN_NAME_PARTS_ID]."<br>";
						$result = false;
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

		if($makeCount == 1) {
			// csvファイル削除
			$result = unlink($this->makingPdfCsvInfo);
		} else {
			// csvファイル作成
			$result = $this->setExport($updateArray, $executePath);
		}


		return $result;
	}

	/**
	 * 商品データ更新（デバッグ用）
	 * @param	-
	 * @return	$result					データ更新結果
	 */
	public function itemUpdateDebug() {
		$result = true;
		$itemArray = array();

		// 更新データ取得
		$itemArray = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->getAll();

		// 商品データ更新
		foreach($itemArray as $itemRow) {
			// where句生成
			$whereItem = COLUMN_NAME_ITEM_ID." = '".$itemRow[COLUMN_NAME_ITEM_ID]."' AND "
						.COLUMN_NAME_CATEGORY_ID." = '".$itemRow[COLUMN_NAME_CATEGORY_ID]."'";
			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->checkData($whereItem);
			if($dbPartsCheck) {
				// DBUpdate処理
				$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
				$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->update($itemRow, $whereItem);
			} else {
				// DBinsert処理
				$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
				$itemRow[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日
				$dbPartsCheck = $this->manager->db_manager->get(TABLE_NAME_ITEM)->insertDB($itemRow);
			}

			// エラーチェック
			if(!$dbPartsCheck) {
				$this->{KEY_ERROR_MESSAGE} = $this->{KEY_ERROR_MESSAGE}.MESSAGE_FAIL_UPDATE_ITEM.$itemRow[COLUMN_NAME_ITEM_ID]."<br>";
				$result = false;
			}
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
