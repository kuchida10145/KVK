<?php
/**
 * 丸数字DB管理クラス
 */
class number_conversionDbModel extends DbModel
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
				'parts_no',
				'no_image',
		);
		return $data;
	}

	/**
	 * 対象のパーツ表示順の丸数字を取得する
	 *
	 * @param int $parts_no パーツ表示順
	 * @return Array
	 */
	public function findByPartsNo($parts_no)
	{
		$parts_no = $this->escape_string($parts_no);

		$field = implode(',',$this->getField());

		$sql = "SELECT no_image FROM number_conversion WHERE $parts_no ='{$parts_no}'";

		return $this->db->getAllData($sql);
	}
}