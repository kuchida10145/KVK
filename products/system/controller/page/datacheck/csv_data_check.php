<?php
	include_once('../../../Page.php');
	class csv_data_check extends Page{

		/**
		 * カテゴリデータ検証
		 *
		 * @param $check_array 検証対照データ（['category_id'], ['category_name'], ['parent_id'], ['category_image'], ['delete_flg']）
		 * @return $result[] 検証結果（['required']：必須チェック検証結果、['resultNum']：数値チェック検証結果）
		 * ※検証結果パターン（true：エラーなし、false：エラー）
		 **/
		public function categoryDataCheck($check_array) {
			$dataCount = 0;
			$requiredArray = $check_array;	// 必須チェック結果格納用
			$numArray = $check_array;		// 数値チェック結果格納用
			// 必須チェック：0～2
			// 数値チェック：0、2
			foreach ($check_array as $val) {
				switch ($dataCount) {
					case 0:
						// 必須チェック
						$requiredResult = $this->isRequired($val);
						$requiredArray[$dataCount] = $requiredResult;
						// 数値チェック
						$numResult = $this->isNum($val);
						$numArray[$dataCount] = $numResult;
						break;
					case 1:
						// 必須チェック
						$requiredResult = $this->isRequired($val);
						$requiredArray[$dataCount] = $requiredResult;
						// 数値チェックはしないので結果のみ格納
						$numArray[$dataCount] = true;
						break;
					case 2:
						// 必須チェック
						$requiredResult = $this->isRequired($val);
						$requiredArray[$dataCount] = $requiredResult;
						// 数値チェック
						$numResult = $this->isNum($val);
						$numArray[$dataCount] = $numResult;
						break;
					default:
				}
				$dataCount++;
			}

			$result['required'] = $requiredArray;
			$result['resultNum'] = $numArray;

			return $result;
		}

		/**
		 * カテゴリデータ検証（数値チェック）
		 *
		 * @param $check_array 検証対照データ（['category_id'], ['category_name'], ['parent_id'], ['category_image'], ['delete_flg']）
		 * @return $result 検証結果（true：エラーなし、false：エラーあり）
		 *
		 **/
		public function isNum($value) {
			$result = true;

			$result = $this->isNumeric($value);
			if(!$result) {
				return $result;
			}
			$result = $this->isDigit($value);
			if(!$result) {
				return $result;
			}
			$result = $this->isPnumeric($value);
			if(!$result) {
				return $result;
			}

			return $result;
		}

		/**
		 * 必須入力チェック
		 *
		 * @param string $value 検証用データ
		 * @return boolean 検証結果
		 * @version 1.1.0
		 */
		private function isRequired($value) {
			if(!isset($value) || $value == '')
			{
				return false;
			}
			return true;
		}

		/**
		 * 数値チェック
		 *
		 * @param string $key 添え字
		 * @param string $data データ配列
		 * @return boolean 検証結果
		 * @version 1.1.0
		 */
		private function isNumeric($value) {
			if(isset($value) && $value != "")
			{
				$val = $value;
				if(is_numeric($val) === false)
				{
					return false;
				}
			}
			return true;
		}

		/**
		 * 整数チェック
		 *
		 * @param string $key 添え字
		 * @param string $data データ配列
		 * @return boolean 検証結果
		 * @version 1.1.0
		 */
		private function isDigit($value) {
			if(isset($value) && $value != "")
			{
				$val = $value;
				if(ctype_digit($val) === false)
				{
					return false;
				}
			}
			return true;
		}

		/**
		 * 正数チェック
		 *
		 * @param string $key 添え字
		 * @param string $data データ配列
		 * @return boolean 検証結果
		 * @version 1.1.0
		 */
		private function isPnumeric($value) {
			if(isset($value) && $value != "" && is_numeric($value))
			{
				$val = $value;
				if($val < 0)
				{
					return false;
				}
			}
			return true;
		}

	}

?>
