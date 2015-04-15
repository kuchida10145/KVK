<?php
include(dirname(__FILE__) . '/../../Page.php');
abstract class AbstractImportCsv extends Page{

	function __construct() {
		parent::__construct();
	}

	protected abstract function dataDBCheck($checkData, $line_count);		// CSVデータDBチェック
	protected abstract function dataPrimaryCheck($checkData, $lineCount); 	// CSVデータ重複データチェック
	protected abstract function runDB($targetData);							// DB関連の処理実行
	protected abstract function csvUpload($targetFile);						// ファイルアップロード処理実行

	/**
	 * csvファイル取込メイン処理
	 * @param 	$filePath	取込csvファイルパス
	 * @param 	$fileName	取込csvファイル名
	 * @param	$testFlg	取込テスト判定
	 * @param	bool		$uploadFlg	アップロードフラグ（true：csvアップロード	false：DB更新）
	 * @return	$result		取込結果（true：csv取込成功	false：csv取込失敗）
	 */
	public function executeImport($filePath, $fileName, $testFlg, $uploadFlg) {
		$line_count = 0;			// csvファイル行数カウント用
		$where = "";				// sql実行用のwhere句
		$parentID = "";				// 親カテゴリID格納用
		$result = "";				// チェック結果
		$error = true;				// errorフラグ（true：エラーなし、false：エラーあり）

		// システムステータスチェック
		$result = $this->systemStatusCheck($this->systemStatus);
		if(!$result) {
			return $result;
		}

		// 拡張子チェック
		$result = $this->checkExtension($fileName);
		if(!$result) {
			$this->{KEY_ERROR_MESSAGE} =  ERROR_MSG_FILE_ERROR;
			return $result;
		}
		// CSVデータ取得
		$csvData = $this->getCsvData($filePath);
		// CSVデータ行数チェック
		if (count($csvData) < CSV_DATA_MIN) {
			$this->{KEY_ERROR_MESSAGE} =  ERROR_MSG_NODATA;
			return false;
		}
		// CSVカラム数チェック
		$result = $this->csvColumnCheck(count($csvData[CSV_HEADER_LINE]));
		if(!$result) {
			$this->{KEY_ERROR_MESSAGE} = ERROR_MSG_COLUMN_ERROR;
			return $result;
		}
		// CSVデータチェック取得
		$result = $this->csvDataCheck($csvData);
		if(!$result) {
			return $result;
		}
		// 登録処理
		if($testFlg) {
			$this->{KEY_ERROR_MESSAGE} = MSG_CHECK_OK;
		} elseif($uploadFlg) {
			$result = $this->csvUpload($filePath);
		} else {
			foreach ($csvData as $row) {
				if($line_count != 0){
					$result = $this->runDB($row);
				}
				$line_count++;
			}

			if(!$result) {
				$this->{KEY_ERROR_MESSAGE} = ERROR_MSG_DB;
			}
		}
		return $result;
	}

	/**
	 * エラーメッセージ取得
	 * @return	$message	エラーメッセージ
	 */
	public function getErrorMessage() {
		return $this->{KEY_ERROR_MESSAGE};
	}

	/**
	 * 結果メッセージ取得
	 * @param	$result		取込結果(true：成功 false：失敗)
	 * @return	$message	実行結果メッセージ
	 */
	public function getResultMessage($result) {
		$message = "";

		if($result) {
			$message = RESULT_MSG_OK;
		} else {
			$message = RESULT_MSG_NG;
		}

		return $message;
	}

	/**
	 * ファイル拡張子チェック
	 * @param  $csvFile	csvファイルパス
	 * @return $result	チェック結果(true：正常、false：異常)
	 */
	protected function checkExtension($csvFile) {
		$result = true;
		$checkVal = pathinfo($csvFile, PATHINFO_EXTENSION);

		if($checkVal != CSV_EXTENTION) {
			$result = false;
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
		$data = mb_convert_encoding($data, SYSTEM_CODE, CSV_CODE);
		$temp = tmpfile();

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
		if($dataCount < CSV_DATA_MIN){
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
		if($dataCount != $this->headerCount){
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
				// データ項目数チェック
				$errorLineCount = $line_count + 1;
				if(!$this->dataColumnCheck($row)) {
					$errorMessage[] = "登録するデータ項目数が一致しません。 {$errorLineCount}行目<br>";
					$result = false;
					$line_count++;
					continue;
				}

				// データ型チェック
				$this->manager->validationColumns->resetError();
				if(!$this->manager->validationColumns->run($row)) {
					$errorMessage[] = $this->manager->validationColumns->getErrorMessageColumn($errorLineCount, $this->msg_rules);
					$result = false;
				}
				// 重複データチェック
				if(!$this->dataPrimaryCheck($row, $errorLineCount)) {
					$errorMessage[] = "{$this->{DUPLICATION_LINE}}行目とデータが重複しています。 {$errorLineCount}行目<br>";
					$result = false;
					$line_count++;
					continue;
				}
				// DBチェック
				if(!$this->dataDBCheck($row, $line_count)) {
					$errorMessage[] = $this->{KEY_DB_CHECK_MESSAGE};
					$result = false;
				}
			}
			$line_count++;
		}

		if(!$result){
			foreach ($errorMessage as $val) {
				if(is_array($val)) {
					foreach ($val as $item) {
						$setMessage = $setMessage.$item;
					}
				} else {
					$setMessage = $setMessage.$val;
				}
			}
			$this->{KEY_ERROR_MESSAGE} = $setMessage;
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
		if(sizeof($checkData) != $this->headerCount){
			$result = false;
		}
		return $result;
	}

	/**
	 * 削除フラグ変換
	 * @param	$dalete_flg	削除フラグ
	 * @return	$result		変換後削除フラグ（true：非表示（データ削除扱い）, false：表示）
	 */
	public function convertDeleteFlg($dalete_flg){
		$result = false;

		if ($dalete_flg == DELETE_FLG){
			$result = true;
		}

		return $result;
	}
}
