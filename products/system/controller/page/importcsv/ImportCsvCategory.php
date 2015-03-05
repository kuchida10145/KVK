<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvCategory extends AbstractImportCsv {
	/**
	 * CSVデータチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataFormCheck($checkData, $line_count) {
		$data_check = new csv_data_check();
		$checkCount = 0;	// データカウント用変数
		$errorCell = 0;		// エラー発生箇所
		$errorMessage = "";
		$result = true;
		$check_result = $data_check->categoryDataCheck($checkData);

		foreach ($check_result['required'] as $val) {
			$errorCell = $checkCount + 1;
			if(!$val) {
				$errorMessage = $errorMessage."未入力項目があります。 {$line_count}行目 {$errorCell}番目の項目<br>";
				$result = false;
			}
			$checkCount++;
		}

		$checkCount = 0;

		foreach ($check_result['resultNum'] as $val) {
			$errorCell = $checkCount + 1;
			if(!$val) {
				$errorMessage = $errorMessage."数値を入力してください。 {$line_count}行目 {$errorCell}番目の項目<br>";
				$result = false;
			}
			$checkCount++;
		}
		$this->set('dataFormCheckMessage',  $errorMessage);

		return $result;
	}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$result = true;

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($checkData[3]);

		// 削除フラグ
		if($deleteFlg){
			$result = $this->manager->db_manager->get('parent_category')->checkData($checkData[0]);
		}

		if(!$result) {
			$this->dataDBCheckMessage = "対象のデータが存在しません。 {$line_count}行目<br>";
		}

		return $result;
	}
}

?>
