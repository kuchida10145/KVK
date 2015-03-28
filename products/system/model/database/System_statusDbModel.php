<?php
/**
 * システムステータスDB管理クラス
*/
include_once('/../../core/database/DbModel.php');
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
}