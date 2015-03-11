<?php
/**
 * 子カテゴリDB管理クラス
 */
include_once('/../../core/database/DbModel.php');
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
	 * 対象の親カテゴリIDの子カテゴリ一覧を取得する
	 *
	 * @param int $parent_id 親カテゴリID
	 * @return Array
	 */
	public function findByParentId($parent_id)
	{
		$parent_id = $this->escape_string($parent_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM child_category WHERE parent_id ='{$parent_id}'";

		return $this->db->getAllData($sql);
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param array $categoryArray	csvから取り込んだ親カテゴリ情報
	 * @return $insertResult		DB取り込み結果
	 */
	public function insertCategory($categoryArray) {
		$table = 'child_category';
		$insert_result = "";

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $categoryArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * データチェック
	 * @param	$key	検索対象key項目
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($key) {
		$result = true;
		$table = 'child_category';
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM {$table} WHERE parent_id = {$key}";
		$dataCount = $this->db->getCount($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}
}