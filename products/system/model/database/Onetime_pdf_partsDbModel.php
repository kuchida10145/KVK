<?php
/**
 * pdf用部品リストDB管理クラス
*/
class Onetime_pdf_partsDbModel extends DbModel
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
		$table = TABLE_NAME_PDF_PARTS_LIST;

		$item_id = $this->escape_string($item_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM {$table} WHERE item_id ='{$item_id}'";

		return $this->db->getAllData($sql);
	}

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$table = TABLE_NAME_PDF_PARTS_LIST;
		$result = true;
		$sql = "";
		$dataCount = array();

		$sql = "SELECT id FROM {$table} WHERE ".$where." limit 0, 1";

		$dataCount = $this->db->getData($sql);

		if(!$dataCount == 0) {
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
	public function insertParts($targetArray) {
		$table = TABLE_NAME_PDF_PARTS_LIST;

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $targetArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * 取込データを複数DBに登録する。
	 *
	 * @param	array	$sql			実行するSQL
	 * @return	boolean	$insert_result	DB追加結果
	 */
	public function insertManyParts($sql) {
		$table = TABLE_NAME_PDF_PARTS_LIST;

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->query($sql);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * 対象データをチェックして変更箇所を特定する。
	 *
	 * @param	array	$targetData	チェック対象データ
	 * @param	string	$where		DB検索用where句
	 * @return	array	$updateClm	更新対象データ
	 */
	public function updateCheck($targetData, $where) {
		$dbRow = array();
		$updateClm = array();
		$table = 'parts_list';
		$sql = "SELECT * FROM {$table} WHERE $where";

		$dbRow = $this->db->getData($sql);

		$updateClm = compareData($dbRow, $targetData);

		return $updateClm;
	}

	/**
	 * データの差異をチェックする。
	 *
	 * @param  array	$dbData		DBから取得したデータ
	 * @param  array	$targetData	チェック対象データ
	 * @return array	$updateClm	更新対象データ
	 */
	protected function compareData($dbData, $targetData) {
		$updateClm = array();

		foreach ($dbData as $key => $value) {
			// 取込データにidは無い
			if($key == 'id'){
				$updateClm[$key] = $value;
				continue;
			}

			// 値が違えば更新対象データ
			if($value != $targetData[$key]){
				$updateClm[$key] = $targetData[$key];
			}
		}

		return $updateClm;
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