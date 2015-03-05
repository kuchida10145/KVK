<?php
/**
 * 親カテゴリーDB管理クラス
 */
class parent_categoryDbModel extends DbModel
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
	 * @param array $category_array csvから取り込んだ親カテゴリ情報
	 * @return Array
	 */
	public function insertParentCategory($category_array) {
		$table = 'parent_category';

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

		$sql = "SELECT * FROM parent_category WHERE parent_id = {$category_id}";
		$dataCount = $this->db->getCount($sql);

		if($dataCount != 0) {
			$result = false;
		}
		return $result;
	}
}