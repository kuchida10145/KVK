<?php
	include(dirname(__FILE__) . '/../AbstractExportCsv.php');
	class ExportCsvParts extends AbstractExportCsv {
		function __construct() {
			parent::__construct();
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
	 * csvファイル出力メイン処理
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExport() {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$result = true;

		// csvファイル書き込み
		$filePointer = fopen(CSV_FILE_NAME_PARTS, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_PARTS_LIST)->getAll();

		foreach ($itemCodeArray as $itemDataRow){
			$csvDataArray = array(
					// 番号
					$itemDataRow[COLUMN_NAME_NO],
					// 品番
					$itemDataRow[COLUMN_NAME_PARTS_ID],
					// 品名
					$itemDataRow[COLUMN_NAME_PARTS_NAME],
					// 希望小売価格
					$itemDataRow[COLUMN_NAME_PRICE],
					// 税込
					$itemDataRow[COLUMN_NAME_PRICE_ZEI],
					// 品番
					$itemDataRow[COLUMN_NAME_ITEM_ID],
					// 分解図
					$itemDataRow[COLUMN_NAME_FILE_NAME],
					// 備考
					$itemDataRow[COLUMN_NAME_NOTE],
					// 削除
					$itemDataRow[COLUMN_NAME_VIEW_STATUS],
			);
			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		header('Content-Disposition: attachment; filename="' .CSV_FILE_NAME_PARTS. '"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(CSV_FILE_NAME_PARTS));
		readfile(CSV_FILE_NAME_PARTS);
 		return $result;
	}
}

?>
