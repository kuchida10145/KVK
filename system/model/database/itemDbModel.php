<?php
/**
 * 商品DB管理クラス
 */
class itemDbModel extends DbModel
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
				'relevance_data',
				'parent_variation',
				'variation_no',
				'item_detail',
				'item_image',
				'relevance_item',
				'category_id',
				'pdf_status',
				'regist_date',
				'update_date',
		);
		return $data;
	}

	/**
	 * 対象のカテゴリIDに該当する商品一覧を取得する
	 *
	 * @param int $category_id カテゴリID
	 * @return Array
	 */
	public function findByCategoryId($category_id)
	{
		$category_id = $this->escape_string($category_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM item WHERE category_id ='{$category_id}'";

		return $this->db->getAllData($sql);
	}
}