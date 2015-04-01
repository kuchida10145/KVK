<?php
/**
 * ユーザDB管理クラス
 */
class UserDbModel extends DbModel
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
				'user_name',
				'pass_word',
				'regist_date',
				'update_date',
		);
		return $data;
	}

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$result = true;
		$sql = "";
		$table = TABLE_NAME_USER;
		$dataCount = array();

		$sql = "SELECT * FROM {$table} WHERE ".$where;

		$dataCount = $this->db->getData($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}
}