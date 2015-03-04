<?php
include_once('/../../Page.php');
abstract class AbstractImportCsv extends Page{
	protected abstract function dataFormCheck($checkData, $line_count);	// cavデータ形式チェック
	protected abstract function dataDBCheck($checkData, $line_count);		// cavデータDBチェック

	private $_data = array();

	public function __get($key){
			return $this->get($key);
	}

	public function __set($key,$value){
			$this->set($key,$value);
	}

	public function get($key,$default=null){
			if(array_key_exist($key,$this->var)){
					return $this->_data[$key];
			}
			return $default;
	}

	public function set($key,$value){
			$this->_data[$key] = $value;
	}

	public function executeImport($filename) {

		// 拡張子チェック
		$error = $this->checkExtension($csvFile);
		if(!$error) {
			$this->set($Message, 'CSVファイルを取り込んでください。<br>');
			return false;
		}
		// CSVデータ取得
		$csvData = $this->getCsvData($csvFile);
		// CSVデータ行数チェック
		if (count($csvData) < 2) {
			$this->set($Message, 'CSVファイルにデータがありません。<br>');
			return false;
		}
		// CSVカラム数チェック
		$error = $this->csvColumnCheck(count($csvData[0]));
		if(!$error) {
			$this->set($Message, 'CSVファイルの項目数が一致しません。<br>');
			return false;
		}
		// CSVデータチェック取得
		$error = $this->csvDataCheck($csvData);
		if(!$error) {
			return false;
		}
		// シュミレーションモードでなければDB更新。
		if($dbFlg) {
			//削除フラグチェック
			if($checkData[3]){
				// DB削除処理
			} else {
				$dbCheck = $this->manager->db_manager->get('parent_category')->checkData($checkData[0]);
				if(!$dbCheck) {
					// DBinsert処理
				} else {
					// Update処理
				}
			}
		}

		// エラーなし
		$importCsv->Message = '成功';
		// エラーあり
		$importCsv->Message = '失敗';

	}

	public function getErrorMessage() {

	}

	public function getHtmlErrorMessage() {

	}

	/**
	 * ファイル拡張子チェック
	 * @param  $csvFile	csvファイルパス
	 * @return $result	チェック結果(true：正常、false：異常)
	 */
	protected function checkExtension($csvFile) {
		$result = false;
		$csvFile = end(explode('.', $csvFile));

		if($csvFile == 'csv') {
			$result = true;
		}
		return $result;
	}

	/**
	 * CSVデータ取得
	 * @param  $csvFile	csvファイルパス
	 * @return $csv		UTF-8変換後のcsvデータ
	 */
	protected function getCsvData($csvFile) {
		$csv  = array();
		// csvから取り込んだデータをUTF-8に変換する
		$data = file_get_contents($csvFile);
		$data = mb_convert_encoding($data, 'UTF-8', 'sjis-win');

		fwrite($temp, $data);
		rewind($temp);

		while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {
			$csv[] = $data;
		}
		fclose($temp);

		return $csv;
	}

	/**
	 * CSVデータ数チェック
	 * @param	$dataCount	データ数
	 * @return	$result		チェック結果
	 */
	protected function csvDataCountCheck($dataCount) {
		$result = true;
		// haader行 + データ行で最低2行は必要。
		if($dataCount < 2){
			$result = false;
		}
		return $result;
	}

	/**
	 * CSVカラム数チェック
	 * @param	$dataCount	データ数
	 * @return	$result		チェック結果
	 */
	protected function csvColumnCheck($dataCount) {
		$result = true;
		if($dataCount == HEADER_COUNT_CATEGORY){
			$result = false;
		}
		return $result;
	}

	/**
	 * CSVデータチェック取得
	 * @param	$csvData		CSVから取得した全データ
	 * @return	$result			チェック結果
	 */
	protected function csvDataCheck($csvData) {
		$line_count = 0;			// csvデータ数カウント用
		$result = true;				// データチェック結果
		$errorMessage = array();	// エラーメッセージ配列
		$errorLineCount	= 0;		// エラーが発生したcsvファイルの行数
		$setMessage = '';			// 画面に表示するエラーメッセージ

		foreach ($csvData as $row) {
			// ヘッダー行はチェックしない
			if($line_count != 0) {
				$errorLineCount = $line_count + 1;
				$result = $this->dataColumnCheck($row);
				if(!$result) {
					$errorMessage[] = "登録するデータ項目数が一致しません。 {$errorLineCount}行目<br>";
					continue;
				}
				$result = $this->dataFormCheck($row, $errorLineCount);
				if(!$result) {
					$errorMessage[] = $this->get('dataFormCheckMessage');
				}
				$result = $this->dataPrimaryCheck($row);
				if(!$result) {
					$errorMessage[] = "{$this->$row[0]}行目とデータが重複しています。 {$errorLineCount}行目<br>";
					continue;
				}
				$result = $this->dataDBCheck($row, $line_count);
				if(!$result) {
					$errorMessage[] = $this->get('dataDBCheckMessage');
				}
			}
			$line_count++;
		}

		if(!$result){
			foreach ($errorMessage as $val) {
				$setMessage = $setMessage.$val;
			}
			$this->set('Message', $setMessage);
		}

		return $result;
	}

	/**
	 * cavデータ項目数チェック
	 * @param	$checkData	チェック対象データ
	 * @return	$result		チェック結果
	 */
	protected function dataColumnCheck($checkData) {
		$result = true;
		if(sizeof($checkData) != HEADER_COUNT_CATEGORY){
			$result = false;
		}
		return $result;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @return	$result		チェック結果
	 */
	protected function dataPrimaryCheck($checkData) {
		$result = true;
		if (!isset($this->$checkData[0])) {
			$this->$checkData[0] = $checkData[0];
			$result = false;
		}
		return $result;
	}
}
