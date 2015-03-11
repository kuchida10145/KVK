<?php
/**
 * ステータスリストDB管理クラス
*/
include_once('/../../core/database/DbModel.php');
class Status_listDbModel extends DbModel
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
				'item_status',
				'status_name',
				'icon',
		);
		return $data;
	}

	/**
	 * 対象の商品ステータスIDの部品一覧を取得する
	 *
	 * @param int $item_status 商品ステータスID
	 * @return Array
	 */
	public function findByItemStatus($item_status)
	{
		$item_status = $this->escape_string($item_status);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM status_list WHERE item_status ='{$item_status}'";

		return $this->db->getAllData($sql);
	}
}