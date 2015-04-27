<?php
/**
 * 子カテゴリDB管理クラス
 */
class Child_categoryDbModel extends DbModel
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
				'category_id',
				'category_name',
				'category_image',
				'parent_id',
				'view_status',
		);
		return $data;
	}

	/**
	 * 対象の子カテゴリを取得する
	 *
	 * @param int $parent_id 子カテゴリID
	 * @return Array
	 */
	public function findByCategoryId($category_id)
	{
		$category_id = $this->escape_string($category_id);

		$field = implode(',',$this->getField());

		if(mb_strlen($category_id)  == 4){
			$sql = "SELECT * FROM child_category WHERE category_id + parent_id * 1000 = '{$category_id}' AND `view_status` = ".VIEW_OK;
		} else {
			$sql = "SELECT * FROM child_category WHERE category_id ='{$category_id}' AND `view_status` = ".VIEW_OK;
		}

		return $this->db->getData($sql);
	}

	/**
	 * 対象の親カテゴリIDの子カテゴリ一覧を取得する
	 *
	 * @param int $parent_id 親カテゴリID
	 * @return Array
	 */
	public function findByParentId($parent_id)
	{
		$parent_id = $this->escape_string($parent_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM child_category WHERE parent_id ='{$parent_id}' AND `view_status` = ".VIEW_OK." ORDER BY `category_id` ASC";

		return $this->db->getAllData($sql);
	}

	/**
	 * 有効な子カテゴリを全件取得する
	 *
	 * @param int $category_id 子カテゴリID
	 * @return Array
	 */
	public function getAllEnabled()
	{
		$field = implode(',',$this->getField());

		$sql = "SELECT {$field} FROM child_category WHERE `view_status` = ".VIEW_OK." ORDER BY `category_id` ASC";

		return $this->db->getAllData($sql);
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param array $categoryArray	csvから取り込んだ親カテゴリ情報
	 * @return $insertResult		DB取り込み結果
	 */
	public function insertDB($categoryArray) {
		$table = 'child_category';
		$insert_result = "";

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
		$table = 'child_category';
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM {$table} WHERE ".$where;
		$dataCount = $this->db->getCount($sql);

		if($dataCount == 0) {
			$result = false;
		}
		return $result;
	}
}