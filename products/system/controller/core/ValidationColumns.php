<?php
/**
 * 検証クラス（CSVカラム用）
 *
 */
include_once('/Validation.php');
class ValidationColumns extends Validation {

	/**
	 * エラーメッセージの取得
	 * @param int $row_number 行番号
	 * @param Array $msg_rules エラーメッセージのルールリスト
	 * @return Array エラーメッセージ
	 */
	function getErrorMessageColumn($row_number, $msg_rules) {
		$messages = array();
		$lineCount = '';
		$columnCount = '';
		foreach($this->errors as $key => $val) {
			$rules = explode(':',$val);
			$rule = $rules[0];
			switch ($rule) {
				case 'range':
					$message[$key] = sprintf($msg_rules[$rule],$rules[1],$rules[2],$rules[3]);
					break;
				case 'password':
					$message[$key] = sprintf($msg_rules[$rule],$rules[1],$rules[2]);
					break;
				case 'maxlength':
				case 'length':
				case 'point_check':
					$message[$key] = sprintf($msg_rules[$rule],$rules[1]);
					break;
				default:
					//print $msg_rules[$rule];
					$message[$key] = $msg_rules[$rule];
			}

			$lineCount = $row_number . "行目";
			if (is_numeric($key)) {
				$column = $key + 1;
				$columnCount = $column . "列目";
			}
			$messages[$key] = $message[$key] . $lineCount . ":" . $columnCount . "<br>";
		}
		return $messages;
	}
}

?>
