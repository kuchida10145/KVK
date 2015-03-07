<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvCategory extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// カテゴリID
			$this->manager->validationColumns->setRule(CATEGORY_ID_COLUMN_CATEGORY, 'required|numeric|digit|pnumeric');
			// カテゴリ名
			$this->manager->validationColumns->setRule(CATEGORY_NAME_COLUMN_CATEGORY, 'required');
			// 親カテゴリID
			$this->manager->validationColumns->setRule(PARENT_ID_COLUMN_CATEGORY, 'required|numeric|digit|pnumeric');
			// イメージ画像
			$this->manager->validationColumns->setRule(IMAGE_COLUMN_CATEGORY, 'required');
			// 削除フラグ
			$this->manager->validationColumns->setRule(DELETE_COLUMN_CATEGORY, 'numeric|digit|pnumeric');
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
		$deleteFlg = $this->convertDeleteFlg($checkData[DELETE_COLUMN_CATEGORY]);

		// 削除フラグ
		if($deleteFlg){
			$result = $this->manager->db_manager->get('parent_category')->checkData($checkData[CATEGORY_ID_COLUMN_CATEGORY]);
		}

		if(!$result) {
			$this->{KEY_DB_CHECK_MESSAGE} = "対象のデータが存在しません。 {$line_count}行目<br>";
		}
		return $result;
	}

}

?>
