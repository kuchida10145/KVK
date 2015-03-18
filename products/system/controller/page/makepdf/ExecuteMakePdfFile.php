<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvParts extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
		}

		/**
		 * pdfファイル作成処理実行
		 * @param	-
		 * @return	$result			実行結果
		 */
		protected function executeMakePdf() {
			$result = true;				// 実行結果
			$pdfItemArray = array();	// pdf作成対象データ（商品）
			$pdfPartsArray = array();	// pdf作成対象データ（部品）
			$keyArray = array();		// 検索キー配列
			$keyPdfPartsList = "";		// テーブル検索用キー
			$errorMessage = array();	// エラーメッセージ格納用
			$oldPath = "";				// 現在のファイルパス
			$newPath = "";				// 移動先ファイルパス
			$backupFileName = "";		// バックアップ用のファイル名
			$limit = "";
			$order = "";

			// pdf作成対象データ取得(商品)
			$pdfItemArray = $this->manager->db_manager->get(TABLE_NAME_PDF_ITEM)->getAll();
			foreach ($pdfItemArray as $rowItem) {
				// 商品データからkey項目を取得して、pdf作成用部品リストを取得
				$keyPdfPartsList = $rowItem[COLUMN_NAME_BUNKAI_DATA];
				$keyPdfPartsList = COLUMN_NAME_FILE_NAME.' = "'.$keyPdfPartsList.'"';
				$pdfPartsListDB = $this->manager->db_manager->get(TABLE_NAME_PDF_PARTS_LIST)->search($keyPdfPartsList, $limit, $order);
				if(count($rowPdfPartsList) == 0) {
					$errorMessage[] = ERROR_MSG_NO_PARTS.$rowItem[COLUMN_NAME_ITEM_NAME]."<br>";
					$result = false;
				} else {
					foreach ($pdfPartsListDB as $rowParts) {
						$pdfPartsArray[] = $rowPdfPartsList;
					}
				}
			}
			
			// pdfファイル退避
			$oldPath = "/../../../".PDF_FILE_PATH.$rowItem[COLUMN_NAME_BUNKAI_DATA];
			$backupFileName = basename($rowItem[COLUMN_NAME_BUNKAI_DATA], '.pdf').date("YmdHis").".pdf";
			$newPath = "/../../../".PDF_BACKUP_PATH.$backupFileName;
			rename($oldPath, $newPath);

			// TODO：pdf作成処理（仮）	※テスト後削除
			$result = makePdf();

			if($result) {
				// 商品データ
				$keyArray = array(
						COLUMN_NAME_ITEM_ID=>$pdfPartsArray[NO_COLUMN_PARTS],
						COLUMN_NAME_CATEGORY_ID=>$pdfPartsArray[CATEGORY_ID_COLUMN_ITEM],
				);
				$table = TABLE_NAME_ITEM;
				$result = $this->runDB($keyArray, $table, $rowItem);

				// 初期化
				$keyArray = array();
				$table = "";

				// 部品データ
				$keyArray = array(
						COLUMN_NAME_NO=>$pdfPartsArray[NO_COLUMN_PARTS],
						COLUMN_NAME_FILE_NAME=>$pdfPartsArray[NO_COLUMN_PARTS],
				);
				$table = TABLE_NAME_PARTS_LIST;
				foreach ($pdfPartsArray as $dataArray) {
					$result = $this->runDB($keyArray, $table, $dataArray);
				}
			} else {
				$errorMessage[] = ERROR_MSG_MAKE_PDF;
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