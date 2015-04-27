<?php
	include(dirname(__FILE__) . '/../AbstractExportCsv.php');
	class ExportCsvItemStatus extends AbstractExportCsv {
		function __construct() {
			parent::__construct();
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
		$filePointer = fopen(CSV_FILE_NAME_ITEM_STATUS, 'w');
		$headerArray = $this->makeHeader();
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// 商品データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->getAll();

		// csv書き込みデータ生成
		foreach ($itemCodeArray as $key=>$itemDataRow){
			if ($this->{$itemDataRow['item_id']} != null) {
				continue;
			}
			$itemStatusArray = array();
			$csvDataArray = array();
			$dataCount = count($headerArray);

			for($setCount = 0; $setCount < $dataCount; $setCount++) {
				if($setCount == 0) {
					$csvDataArray[$setCount] = $itemDataRow[COLUMN_NAME_ITEM_ID];	// 品番
				} else {
					$csvDataArray[$setCount] = "";
				}
			}
			$itemStatusArray = explode(",", $itemDataRow[COLUMN_NAME_ITEM_STATUS]);
			foreach ($itemStatusArray as $statusArrayKey=>$statusArrayValue) {
				if(!empty($statusArrayValue)) {
					$csvDataArray[$statusArrayValue] = "1";
				}
			}

			mb_convert_variables(CSV_CODE, SYSTEM_CODE, $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		header('Content-Disposition: attachment; filename="' .CSV_FILE_NAME_ITEM_STATUS. '"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(CSV_FILE_NAME_ITEM_STATUS));
		readfile(CSV_FILE_NAME_ITEM_STATUS);
		return $result;
	}

	/**
	 * ヘッダー項目生成
	 *
	 * @return	array	$header	ヘッダー項目
	 */
	protected function makeHeader() {
		$header = array();
		$dbArray = $this->manager->db_manager->get('item_icon')->getAll();
		$header[] = '品番';
		foreach ($dbArray as $key=>$value) {
			$header[] = $value['icon_name'];
		}

		return $header;
	}
}

?>
