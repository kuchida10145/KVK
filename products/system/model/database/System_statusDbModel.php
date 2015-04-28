<?php
/**
 * システムステータスDB管理クラス
*/
class System_statusDbModel extends DbModel
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
			'status',
			'pdf_time',
			'item_disp_status',
			'status_master_disp_status',
			'status_disp_status',
			'category_disp_status',
			'parts_disp_status',
		);
		return $data;
	}

	/**
	 * システム状態を取得する
	 *
	 * @param	-
	 * @return	$systemStatus
	 */
	public function getSystemStatus() {
		$resultArray = array();
		$resultVal = array();
		$returnVal = "";

		$table = TABLE_NAME_SYSTEM_STATUS;

		$column = COLUMN_NAME_SYSTEM_STATUS;

		$sql = "SELECT {$column} FROM {$table}";

		$resultArray = $this->db->getAllData($sql);
		$resultVal = $resultArray[0];
		$returnVal = $resultVal[COLUMN_NAME_SYSTEM_STATUS];

		return $returnVal;
	}

	/**
	 * 画面状態を取得する
	 *
	 * @param	$dispName	対象画面
	 * @return	$systemStatus
	 */
	public function getDispStatus($dispName) {
		$resultArray = array();
		$resultVal = array();
		$returnVal = "";

		$table = TABLE_NAME_SYSTEM_STATUS;

		$column = $dispName;

		$sql = "SELECT {$column} FROM {$table}";

		$resultArray = $this->db->getAllData($sql);
		$resultVal = $resultArray[0];
		$returnVal = $resultVal[$dispName];

		return $returnVal;
	}
}