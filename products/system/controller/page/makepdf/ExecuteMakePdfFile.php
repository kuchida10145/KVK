<?php
	include_once('/CommonMakePDF.php');
	class ImportCsvParts extends CommonMakePDF {
		function __construct() {
			parent::__construct();
		}

		/**
		 * pdfファイル作成処理実行
		 * @param	-
		 * @return	$result			実行結果
		 */
		protected function executeMakePdf() {
			$systemStatus = "";			// システムステータス
			$result = true;				// 実行結果
			$pdfItemArray = array();	// pdf作成対象データ（商品）
			$pdfPartsArray = array();	// pdf作成対象データ（部品）
			$wherePdfPartsList = "";	// テーブル検索用where句（pdf作成用部品）
			$errorMessage = array();	// エラーメッセージ格納用
			$viewError = "";			// エラーメッセージ表示用
			$limit = "";
			$order = "";

			// pdf作成対象データ取得(pdf作成用一次商品データ)
			$pdfItemArray = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->getAll();
			foreach ($pdfItemArray as $rowItem) {
				// システムステータスチェック（4：PDF作成中止だった場合次のデータへ）
				$systemStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();
				if($systemStatus == SYSTEM_STATUS_PDF_STOP) {
					break;
				}

				// pdf作成フラグチェック（pdfファイルが作成済みの場合次のデータへ）
				if($rowItem[COLUMN_NAME_PDF_STATUS] == PDF_STATUS_ZUMI) {
					break;
				}

				// 商品データからkey項目を取得して、pdf作成用部品リストを取得
				$wherePdfPartsList = COLUMN_NAME_ITEM_ID.' = "'.$rowItem[COLUMN_NAME_ITEM_ID].'" AND '.
									COLUMN_NAME_FILE_NAME.' = "'.$rowItem[COLUMN_NAME_BUNKAI_DATA].'" ';
				$pdfPartsListDB = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->search($wherePdfPartsList, $limit, $order);
				if(count($rowPdfPartsList) == 0) {
					$errorMessage[] = ERROR_MSG_NO_PARTS.$rowItem[COLUMN_NAME_ITEM_ID]."<br>";
					$result = false;
				} else {
					foreach ($pdfPartsListDB as $rowParts) {
						$pdfPartsArray[] = $rowPdfPartsList;
					}
					// TODO：pdf作成処理（仮）	※テスト後削除
					$result = makePdf($rowItem, $pdfPartsArray);

					// pdf作成完了フラグを立てる。
					if($result) {
						$rowItem[COLUMN_NAME_PDF_STATUS] = PDF_STATUS_ZUMI;
						$result = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->updateById($rowItem['id'], $rowItem);
					}

					// エラーチェック
					if(!$result) {
						$errorMessage[] = ERROR_MSG_MAKE_PDF."品番：".$rowItem[COLUMN_NAME_ITEM_ID]."<br>";
					}
				}

				// TODO：pdf作成処理（仮）	※テスト後削除
				$result = makePdf($pdfItemArray, $pdfPartsArray);

				if(!$result) {
					$errorMessage[] = ERROR_MSG_MAKE_PDF;
				} else {
					$result = $this->updateData();
				}
			}

			if(!$result) {
				foreach ($errorMessage as $errorRow) {
					$viewError = $viewError.$errorRow;
				}
				$this->{KEY_ERROR_MESSAGE} = $viewError;
			}

			return $result;
		}

		// TODO：デバッグ用仮メソッド
		protected function makePdf($pdfDataItem, $pdfDataParts) {
			return true;
		}


		/**
		 * DB処理実行
		 * @param	Array	$keyArray	検索用key項目
		 * @param			$table		対象テーブル
		 * @param	Array	$dataArray	登録対象データ
		 * @return	$result				実行結果
		 */
		protected function runDB($keyArray, $table, $dataArray) {
			$where = "";	// 検索用where句
			$keyCount = 0;	// key項目カウント用
			$result = true;	// 実行結果

			// where句生成
			while($key = current($keyArray)) {
				if($keyCount != 0) {
					$where = $where."AND ";
				}
				$where = $where.key($keyArray)." = '".$key."' ";
				$keyCount++;
				next($keyArray);
			}

			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbCheck = $this->manager->db_manager->get($table)->checkData($where);
			if($dbCheck) {
				// DBUpdate処理
				$dataArray[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
				$result = $this->manager->db_manager->get($table)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dataArray[COLUMN_NAME_REGIST_DATE] = date("Y-m-d H:i:s");	// 登録日追加
				$dataArray[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日
				$result = $this->manager->db_manager->get($table)->insertDB($dataArray);
			}
			return $result;
		}
	}


?>