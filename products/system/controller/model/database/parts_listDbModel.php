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
				'item_id',
				'file_name',
				'view_status',
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

/**
	 * データチェック
	 * @param	$keyNo			取込対象key項目（表示順）
	 * @param	$keyFileName	取込対象key項目（ファイル名順）
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($keyNo, $keyFileName) {
		$result = true;
		$category_id = "";
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM parts_list WHERE parts_no = {$keyNo} AND file_name = {$keyFileName}";

		$dataCount = $this->db->getData($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}
}