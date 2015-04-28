<?php
/**
 * Pdf用商品DB管理クラス
 */
class Onetime_pdf_itemDbModel extends DbModel
{
	var $use_sequence = false;

	/**
	 * フィールドを取得
	 *
	 * @return Array フィールド
	 */
	public function getField()
	{
		$data = array(
			'id',
			'item_id',
			'item_name',
			'view_status',
			'item_status',
			'price',
			'price_zei',
			'map_data',
			'torisetsu_data',
			'kousetsu_data',
			'bunkai_data',
			'shower_data',
			'buy_status',
			'catalog_link',
			'parent_variation',
			'variation_no',
			'item_image',
			'category_id',
			'pdf_status',
			'search_word',
			'bunki_kanagu_1',
			'bunki_kanagu_2',
			'bunki_kanagu_3',
			'sell_time',
			'sub_item',
			'sunpou',
			'pitch',
			'shower_sunpou',
			'regist_date',
			'update_date',
		);
		return $data;
	}

	/**
	 * 対象の品番の部品一覧を取得する
	 *
	 * @param int $item_table_id 商品テーブルID
	 * @return Array
	 */
	public function findByItemId($item_id)
	{
		$item_id = $this->escape_string($item_id);

		$field = implode(',',$this->getField());

		$table = TABLE_NAME_PDF_ITEM;

		$sql = "SELECT * FROM '{$table}' WHERE item_id ='{$item_id}'";

		return $this->db->getAllData($sql);
	}

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$table = TABLE_NAME_PDF_ITEM;
		$result = true;
		$sql = "";
		$dataCount = array();

		$sql = "SELECT id FROM {$table} WHERE ".$where." limit 0, 1";

		$dataCount = $this->db->getData($sql);

		if(!$dataCount == 0) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param	array	$targetArray	csvから取り込んだ親カテゴリ情報
	 * @return	boolean	$insert_result	DB追加結果
	 */
	public function insertDB($targetArray) {
		$table = TABLE_NAME_PDF_ITEM;

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $targetArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}
}