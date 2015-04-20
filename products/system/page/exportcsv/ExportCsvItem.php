<?php
	include(dirname(__FILE__) . '/../AbstractExportCsv.php');
	class ExportCsvItem extends AbstractExportCsv {
		function __construct() {
			parent::__construct();
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

	/**
	 * csvファイル出力メイン処理
	 * @return	$result		出力結果（true：csv取込成功	false：csv取込失敗）
	 */
	protected function setExport() {
		$filePointer = "";			// ファイルポインタ
		$headerArray = array();		// csvヘッダー行
		$fileName = array();		// ファイル名
		$catalogYear = array();		// カタログ年度
		$catalogPage = array();		// カタログページ
		$result = true;

		// csvファイル書き込み
		$filePointer = fopen(CSV_FILE_NAME_ITEM, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->getAll();

		// カタログページ分割
		$fileName = explode(".",$itemDataRow[COLUMN_NAME_CATALOG_LINK]);
		$catalogYear = explode("-",$fileName[0]);
		$catalogPage = explode("_",$catalogYear[1]);

		foreach ($itemCodeArray as $itemDataRow){
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

		header('Content-Disposition: attachment; filename="' .CSV_FILE_NAME_ITEM. '"');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(CSV_FILE_NAME_ITEM));
		readfile(CSV_FILE_NAME_ITEM);
		return $result;
	}
}

?>
