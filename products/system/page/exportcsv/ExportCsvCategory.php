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
		$result = true;

		// csvファイル書き込み
		$filePointer = fopen(CSV_FILE_NAME_CATEGORY_MASTER, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->getAll();

		foreach ($itemCodeArray as $itemDataRow){
			$csvDataArray = array(
					// カテゴリID
// 					$itemDataRow[],
					// カテゴリ名
// 					$itemDataRow[],
					// 親カテゴリID
// 					$itemDataRow[],
					// イメージ画像
// 					$itemDataRow[],
					// 削除
// 					$itemDataRow[],
			);
			mb_convert_variables('sjis', 'utf-8', $csvDataArray);
			fputcsv($filePointer, $csvDataArray);
		}
		fclose($filePointer);

		header('Content-Disposition: attachment; filename="' .CSV_FILE_NAME_CATEGORY_MASTER. '"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(CSV_FILE_NAME_CATEGORY_MASTER));
		readfile(CSV_FILE_NAME_CATEGORY_MASTER);
		return $result;
	}
}

?>
