<?php
/**
 * トピックスDB管理クラス
 */
class TopicsDbModel extends DbModel
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
				'text',
				'link',
				'regist_date',
		);
		return $data;
	}

	/**
	 * 全アイテム取得
	 */
	public function getAllTopics(){
		$sql = "SELECT * FROM `topics` ORDER BY `regist_date` DESC";
		return $this->db->getAllData($sql);
	}

	/**
	 * 削除
	 */
	public function deleteTopics($id){
		$table = 'topics';
		$where = "id = {$id}";
		return $this->db->delete($table, $where);
	}

	/**
	 * 更新
	 */
	public function updateTopics($id, $date, $text, $link){
		$table = 'topics';
		$array = array(
			'text'=>$text,
			'link'=>$link,
			'regist_date'=>$date,
		);
		$where = "id = {$id}";
		return $this->db->update($table, $array, $where);
	}

	/**
	 * 登録
	 */
	public function insertTopics($date, $text, $link){
		$table = 'topics';
		$array = array(
				'text'=>$text,
				'link'=>$link,
				'regist_date'=>$date,
		);
		return $this->db->insert($table, $array);
	}
}