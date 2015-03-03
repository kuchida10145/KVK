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
		);
		return $data;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param array $category_array csvから取り込んだ親カテゴリ情報
	 * @return Array
	 */
	public function insertParentCategory($category_array)
	{
		$insert_ok = 1;
		$table = 'parent_category';
		$where = '1 = 1';

		$this->db->startTran();				// トランザクション開始

		$this->db->delete($table, $where);	// テーブルを全件削除する。

		foreach ($category_array as $row) {
			if($row[2] == 0) {
				$insert_array = array();
				$insert_array += array('parent_id'=>$row[0], 'parent_name'=>$row[1], 'parent_image'=>$row[2]);
				$insert_result = $this->db->insert($table, $insert_array);

				if($insert_result != $insert_ok) {

					break;
				}
			}
		}

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * データチェック
	 * @param $category_id	検索キー
	 * @return $result		検索結果
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