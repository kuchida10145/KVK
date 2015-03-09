<?php
/**
 * 親カテゴリーDB管理クラス
 */
include_once('/../../core/database/DbModel.php');
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
	 * @param array $categoryArray csvから取り込んだ親カテゴリ情報
	 * @return Array
	 */
	public function insertCategory($categoryArray) {
		$table = 'parent_category';

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $categoryArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * データチェック
	 * @param	$key	取込対象key項目
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($key) {
		$result = true;
		$category_id = "";
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM parent_category WHERE parent_id = {$key}";

		$dataCount = $this->db->getData($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}
}