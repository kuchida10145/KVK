<?php
/**
 * ステータスリストDB管理クラス
*/
include_once('/../../core/database/DbModel.php');
class Item_iconDbModel extends DbModel
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
				'icon_id',
				'icon_name',
				'icon_file',
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

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$result = true;
		$table = TABLE_NAME_STATUS_LIST;
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM {$table} WHERE ".$where;
		$dataCount = $this->db->getCount($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param array $categoryArray	csvから取り込んだ親カテゴリ情報
	 * @return $insertResult		DB取り込み結果
	 */
	public function insertDB($categoryArray) {
		$table = TABLE_NAME_STATUS_LIST;
		$insert_result = "";

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $categoryArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}
}