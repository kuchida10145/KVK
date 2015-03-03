<?php
include_once('../Page.php');
abstract class AbstractImportCsv extends Page{
	public abstract protected function dataFormCheck($checkData);		// cavデータ形式チェック
	public abstract protected function dataDBCheck($checkData);			// cavデータDBチェック

	private $_data = array();

	public function __set($name, $value) {
		$this->_data[$name] = $value;
	}

	public function __get($name) {
		if(isset($this->_data[$name])) {
			return $this->_data[$name];
		}
		return null;
	}

	public function __isset($name){
		return isset($this->_data[$name]);
	}

	public function __unset($name){
		unset($this->_data[$name]);
	}

	/**
	 * ファイル拡張子チェック
	 * @param  $csvFile	csvファイルパス
	 * @return $result	チェック結果
	 */
	public function checkExtension($csvFile) {
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
	public function getCsvData($csvFile) {
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
	public function csvDataCountCheck($dataCount) {
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
	public function csvColumnCheck($dataCount) {
		$result = true;
		if($dataCount == $this->_data['headerCount']){
			$result = false;
		}
		return $result;
	}

	/**
	 * CSVデータチェック取得
	 * @param	$csvData		CSVから取得した全データ
	 * @return	$result			チェック結果
	 */
	public function csvDataCheck($csvData) {
		$line_count = 0;			// csvデータ数カウント用
		$result = true;				// データチェック結果
		$errorMessage = array();	// エラーメッセージ

		foreach ($csvData as $row) {
			// ヘッダー行はチェックしない
			if($line_count != 0) {
				$result = $this->dataColumnCheck($row);
				if(!$result) {
					$errorMessage[] = "登録するデータ項目が足りません。 {$line_count}行目";
				}
				$result = $this->dataPrimaryCheck($row);
				if(!$result) {
					$errorMessage[] = "{$this->$row[0]}行目とデータが重複しています。 {$line_count}行目";
				}
				$result = $this->dataFormCheck($row);
				if(!$result) {
					$errorMessage[] = $this->dataFormCheckMessage;
				}
				$result = $this->dataDBCheck($row);
				if(!$result) {
					$errorMessage[] = $this->dataDBCheckMessage;
				}
			}
			$line_count++;
		}

		if(!$result){
			$this->Message = $errorMessage;
		}

		return $result;
	}

	/**
	 * cavデータ項目数チェック
	 * @param	$checkData	チェック対象データ
	 * @return	$result		チェック結果
	 */
	public function dataColumnCheck($checkData) {
		$result = true;
		if(sizeof($checkData) != $this->_data['headerCount']){
			$result = false;
		}
		return $result;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @return	$result		チェック結果
	 */
	public function dataPrimaryCheck($checkData) {
		$result = true;
		if (!isset($this->$checkData[0])) {
			$this->$checkData[0] = $checkData[0];
			$result = false;
		}
		return $result;
	}
}
