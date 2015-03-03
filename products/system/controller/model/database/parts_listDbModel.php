<?php
/**
 * 部品リストDB管理クラス
*/
class parts_listDbModel extends DbModel
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
				'parts_no',
				'parts_id',
				'parts_name',
				'price',
				'price_zei',
				'haiban_status',
				'daigae_status',
				'view_status',
				'item_id',
				'note',
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

		$sql = "SELECT * FROM parts_list WHERE item_id ='{$item_id}'";

		return $this->db->getAllData($sql);
	}
}