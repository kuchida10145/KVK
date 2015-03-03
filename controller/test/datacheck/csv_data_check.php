<?php
	include_once('../../../Page.php');
	class csv_data_check extends Page{

		/**
		 * カテゴリデータ検証
		 *
		 * @param $check_array 検証対照データ（['category_id'], ['category_name'], ['parent_id'], ['category_image'], ['delete_flg']）
		 * @return $result 検証結果（true：エラーなし、false：エラーあり）
		 *
		 **/
		public function categoryDataCheck($check_array) {
			$result = array();

			$resultRequired = $this->categoryRequiredCheck($check_array);	// 必須チェック
			$resultNum = $this->categoryNumCheck($check_array);				// 数値チェック

			$result['required'] = $resultRequired;
			$result['resultNum'] = $resultNum;

			return $result;
		}

		/**
		 * カテゴリデータ検証（必須チェック）
		 *
		 * @param $check_array 検証対照データ（['category_id'], ['category_name'], ['parent_id'], ['category_image'], ['delete_flg']）
		 * @return $result 検証結果（true：エラーなし、false：エラーあり）
		 *
		 **/
		public function categoryRequiredCheck($check_array) {
			$this->manager->validation->setRule('category_id', 'required');		// 必須チェック
			$this->manager->validation->setRule('category_name', 'required');	// 必須チェック
			$this->manager->validation->setRule('parent_id', 'required');		// 必須チェック
// 			$this->manager->validation->setRule('category_image', 'required');	// 必須チェック
			$result = $this->manager->validation->run($check_array);

			return $result;
		}

		/**
		 * カテゴリデータ検証（数値チェック）
		 *
		 * @param $check_array 検証対照データ（['category_id'], ['category_name'], ['parent_id'], ['category_image'], ['delete_flg']）
		 * @return $result 検証結果（true：エラーなし、false：エラーあり）
		 *
		 **/
		public function categoryNumCheck($check_array) {
			$this->manager->validation->setRule('category_id', 'numeric');		// 数値チェック：数字チェック
			$this->manager->validation->setRule('category_id', 'digit');		// 数値チェック：整数チェック
			$this->manager->validation->setRule('category_id', 'pnumeric');		// 数値チェック：正数チェック
			$this->manager->validation->setRule('parent_id', 'numeric');		// 数値チェック：数字チェック
			$this->manager->validation->setRule('parent_id', 'digit');			// 数値チェック：整数チェック
			$this->manager->validation->setRule('parent_id', 'pnumeric');		// 数値チェック：正数チェック
// 			// 削除フラグは入力されていた場合のみチェックする。
// 			if($check_array['delete_flg'] != "") {
// 				$this->manager->validation->setRule('delete_flg', 'numeric');	// 数値チェック：数字チェック
// 				$this->manager->validation->setRule('delete_flg', 'digit');		// 数値チェック：整数チェック
// 				$this->manager->validation->setRule('delete_flg', 'pnumeric');	// 数値チェック：正数チェック
// 			}

			$result = $this->manager->validation->run($check_array);

			return $result;
		}
	}

?>
