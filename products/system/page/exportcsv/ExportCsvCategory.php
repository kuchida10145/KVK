<?php
	include(dirname(__FILE__) . '/../AbstractExportCsv.php');
	class ExportCsvCategory extends AbstractExportCsv {
		function __construct() {
			parent::__construct();
			// 出力csvファイルのヘッダー行
			$this->csvHeader = array(
				'カテゴリID',
				'カテゴリ名',
				'親カテゴリID',
				'イメージ画像',
				'削除',
			);
		}

	/**
	 * csvファイル出力メイン処理
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExport() {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$childCategory = array();	// 子カテゴリ一覧
		$result = true;

		// csvファイル書き込み
		$filePointer = fopen(CSV_FILE_NAME_CATEGORY_MASTER, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_PARENT_CATEGORY)->getAll();

		foreach ($itemCodeArray as $parentKey=>$itemDataRow){
			$csvDataArray = array(
					// カテゴリID
 					$itemDataRow[COLUMN_NAME_PARENT_ID],
					// カテゴリ名
 					$itemDataRow[COLUMN_NAME_PARENT_NAME],
					// 親カテゴリID
 					"0",
					// イメージ画像
 					$itemDataRow[COLUMN_NAME_PARENT_IMAGE],
					// 削除
 					$itemDataRow[COLUMN_NAME_VIEW_STATUS],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);

			// 子カテゴリ取得
			$childCategory = $this->manager->db_manager->get(TABLE_NAME_CHILD_CATEGORY)->findByParentId($itemDataRow[COLUMN_NAME_PARENT_ID]);
			foreach ($childCategory as $childKey=>$childValue) {
				$csvDataArray = array(
						// カテゴリID
						$childValue[COLUMN_NAME_CATEGORY_ID],
						// カテゴリ名
						$childValue[COLUMN_NAME_CATEGORY_NAME],
						// 親カテゴリID
						$childValue[COLUMN_NAME_PARENT_ID],
						// イメージ画像
						$childValue[COLUMN_NAME_CATEGORY_IMAGE],
						// 削除
						$childValue[COLUMN_NAME_VIEW_STATUS],
				);
				mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
				fputcsv($filePointer, $csvDataArray);
			}
		}
		fclose($filePointer);

		header('Content-Disposition: attachment; filename="' .CSV_FILE_NAME_CATEGORY_MASTER. '"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(CSV_FILE_NAME_CATEGORY_MASTER));
		readfile(CSV_FILE_NAME_CATEGORY_MASTER);
		return $result;
	}

	protected function setCsvArray($filePointer, $dataArray) {

	}
}

?>
