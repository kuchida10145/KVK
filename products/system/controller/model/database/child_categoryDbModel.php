<?php
/**
 * 子カテゴリDB管理クラス
 */
class child_categoryDbModel extends DbModel
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
	 * @param array $category_array csvから取り込んだ親カテゴリ情報
	 * @return Array
	 */
	public function insertChildCategory($category_array) {
		$table = 'child_category';

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $category_array);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * データチェック
	 * @param $category_id	検索キー
	 * @return $result		検索結果(true：データなし false：データあり)
	 */
	public function checkData($category_id) {
		$result = true;
		$table = 'child_category';

		$sql = "SELECT * FROM {$table} WHERE parent_id = {$category_id}";
		$dataCount = $this->db->getCount($sql);

		if($dataCount != 0) {
			$result = false;
		}
		return $result;
	}
}