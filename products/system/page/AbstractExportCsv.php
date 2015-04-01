<?php
abstract class AbstractExportCsv extends Page{

	function __construct() {
		parent::__construct();
	}

	protected abstract function setExport();		// csvダウンロード実行

	/**
	 * csvファイル出力メイン処理
	 * @return	$result		出力結果（true：csv出力成功	false：csv出力失敗）
	 */
	public function executeExport() {
		$result = true;

		$result = $this->setExport();

		return $result;
	}
}
