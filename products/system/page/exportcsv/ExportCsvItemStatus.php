<?php
	include(dirname(__FILE__) . '/../AbstractExportCsv.php');
	class ExportCsvItemStatus extends AbstractExportCsv {
		function __construct() {
			parent::__construct();
			// 出力csvファイルのヘッダー行
			$this->csvHeader = array(
				'品番',
				'JIS',
				'※第三者認証登録品',
				'第三者認証品',
				'寒冷地用',
				'逆止弁付',
				'保証書',
				'新商品',
				'スーパーサーモ',
				'eシャワー',
				'やさしさ推奨品',
				'定量止水',
				'サーモスタット式水栓',
				'自閉式',
				'ミキシング式',
				'一次止水付',
				'ソーラー用',
				'セラミックシングル',
				'eレバー水栓',
				'マルチ水栓',
				'センサー付',
				'スーパーシングル',
				'シングルレバー式水栓',
				'NSFシャワー',
				'泡沫吐水',
				'省施工仕様',
				'上施工方式（省施工仕様）',
				'浄水器用',
				'エコこま水栓',
				'プチエコ水栓',
				'緊急止水機能付',
				'節湯A1',
				'節湯B1',
				'節湯C1',
				'節湯A',
				'節湯B',
				'節湯AB',
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
		$filePointer = fopen(CSV_FILE_NAME_ITEM_STATUS, 'w');
		$headerArray = $this->csvHeader;
		mb_convert_variables(CSV_CODE, SYSTEM_CODE, $headerArray);
 		fputcsv($filePointer, $headerArray);

		// データ取得
		$itemCodeArray = $this->manager->db_manager->get(TABLE_NAME_ITEM)->getAll();

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
					// TODO：使用確定後修正（商品イメージ）
					'商品イメージ',
					// バリエーション親品番
					$itemDataRow[COLUMN_NAME_PARENT_VARIATION],
					// バリエーション順序
					$itemDataRow[COLUMN_NAME_VARIATION_NO],
					// TODO：使用確定後修正（カタログ年度）
					'カタログ年度',
					// TODO：使用確定後修正（カタログページ）
					'カタログページ',
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
					$itemDataRow[COLUMN_NAME_CATEGORY_ID]
			);
			mb_convert_variables('sjis', 'utf-8', $csvDataArray);
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
