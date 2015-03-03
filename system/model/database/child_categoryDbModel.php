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
}