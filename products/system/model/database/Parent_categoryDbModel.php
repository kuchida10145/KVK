<?php
/**
 * 親カテゴリーDB管理クラス
 */
class Parent_categoryDbModel extends DbModel
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
				'parent_id',
				'parent_name',
				'parent_image',
				'view_status',
		);
		return $data;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param array $categoryArray csvから取り込んだ親カテゴリ情報
	 * @return Array
	 */
	public function insertDB($categoryArray) {
		$table = 'parent_category';

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $categoryArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$result = true;
		$category_id = "";
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM parent_category WHERE ".$where;

		$dataCount = $this->db->getData($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 対象の親カテゴリを取得する
	 *
	 * @param int $parent_id 親カテゴリID
	 * @return Array
	 */
	public function findByParentId($parent_id)
	{
		$parent_id = $this->escape_string($parent_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM parent_category WHERE `view_status` = ".VIEW_OK." AND `parent_id` ='{$parent_id}'";

		return $this->db->getData($sql);
	}

	/**
	 * 有効な親カテゴリを全件取得する
	 *
	 * @param int $parent_id 親カテゴリID
	 * @return Array
	 */
	public function getAllEnabled()
	{
		$field = implode(',',$this->getField());

		$sql = "SELECT {$field} FROM parent_category WHERE `view_status` = ".VIEW_OK." ORDER BY `parent_id` ASC";

		return $this->db->getAllData($sql);
	}
}