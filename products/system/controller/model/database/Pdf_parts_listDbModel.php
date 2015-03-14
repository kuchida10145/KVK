<?php
/**
 * pdf用部品リストDB管理クラス
*/
class Pdf_parts_listDbModel extends DbModel
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
				'parts_no',
				'parts_id',
				'parts_name',
				'price',
				'price_zei',
				'item_id',
				'file_name',
				'view_status',
				'note',
				'regist_date',
				'update_date',
		);
		return $data;
	}

	/**
	 * 対象の品番の部品一覧を取得する
	 *
	 * @param int $item_table_id 商品テーブルID
	 * @return Array
	 */
	public function findByItemId($item_id)
	{
		$item_id = $this->escape_string($item_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM pdf_parts_list WHERE item_id ='{$item_id}'";

		return $this->db->getAllData($sql);
	}

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$result = true;
		$sql = "";
		$dataCount = array();

		$sql = "SELECT * FROM pdf_parts_list WHERE ".$where;

		$dataCount = $this->db->getData($sql);

		if(count($dataCount) == 0) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param	array	$targetArray	csvから取り込んだ親カテゴリ情報
	 * @return	boolean	$insert_result	DB追加結果
	 */
	public function insertDB($targetArray) {
		$table = 'pdf_parts_list';

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $targetArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * シーケンスID取得。
	 *
	 * @return	int	$sequenceID	DBに追加するシーケンスID
	 */
	public function getSequenceId(){
		$sequenceArray = array();
		$sequenceID = "";
		$sequenceArray = $this->db->getLastId();
		$sequenceID = $sequenceArray['id'] + 1;
		return $sequenceID;
	}
}