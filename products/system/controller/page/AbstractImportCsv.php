<?php
include_once('/../../Page.php');
abstract class AbstractImportCsv extends Page{

	function __construct() {
		parent::__construct();
	}

	protected abstract function dataDBCheck($checkData, $line_count);		// cavデータDBチェック

	private $_data = array();

	/** GETTERメソッド
	 * @param	$key				取得するデータのkey項目
	 * @return	$this->get($key)	指定したkeyの値
	 */
	public function __get($key){
			return $this->get($key);
	}

	/** SETTERメソッド
	 * @param $key		key項目
	 * @param $value	key項目にセットする値
	 */
	public function __set($key,$value){
			$this->set($key,$value);
	}

	// GETTERメソッド詳細
	protected function get($key,$default=null){
			if(array_key_exists($key,$this->_data)){
					return $this->_data[$key];
			}
			return $default;
	}

	// SETTERメソッド詳細
	protected function set($key,$value){
			$this->_data[$key] = $value;
	}

	/**
	 * csvファイル取込メイン処理
	 * @param 	$filePath	取込csvファイルパス
	 * @param 	$fileName	取込csvファイル名
	 * @param	$testFlg	取込テスト判定
	 * @return	$result		取込結果（true：csv取込成功	false：csv取込失敗）
	 */
	public function executeImport($filePath, $fileName, $testFlg) {
		$line_count = 0;			// csvファイル行数カウント用
		$where = "";				// sql実行用のwhere句
		$parentID = "";				// 親カテゴリID格納用
		$dbCheck = "";				// db操作結果
		$error = true;				// errorフラグ（true：エラーなし、false：エラーあり）

		// 拡張子チェック
		$error = $this->checkExtension($fileName);
		if(!$error) {
			$this->{KEY_ERROR_MESSAGE} =  ERROR_MSG_FILE_ERROR;
			return false;
		}
		// CSVデータ取得
		$csvData = $this->getCsvData($filePath);
		// CSVデータ行数チェック
		if (count($csvData) < CSV_DATA_MIN) {
			$this->{KEY_ERROR_MESSAGE} =  ERROR_MSG_NODATA;
			return false;
		}
		// CSVカラム数チェック
		$error = $this->csvColumnCheck(count($csvData[CSV_HEADER_LINE]));
		if(!$error) {
			$this->{KEY_ERROR_MESSAGE} = ERROR_MSG_COLUMN_ERROR;
			return false;
		}
		// CSVデータチェック取得
		$error = $this->csvDataCheck($csvData);
		if(!$error) {
			return false;
		}
		// シュミレーションモードでなければDB更新。
		// true：シュミレーション（DB更新しない）	false：csv取込（DB更新を行う）
		if($testFlg) {
			$this->{KEY_ERROR_MESSAGE} = MSG_CHECK_OK;
			return true;
		} else {
			// csvファイル全データに対してDB処理を行う。
			foreach ($csvData as $row) {
				if($line_count != 0){
					// 親カテゴリID取得
					$parentID = $row[PARENT_ID_COLUMN_CATEGORY];
					// カテゴリDB処理
					$dbCheck = $this->runCategory($row, $parentID);
				}
				$line_count++;
			}

			if(!$dbCheck) {
				$this->{KEY_ERROR_MESSAGE} = ERROR_MSG_DB;
				return false;
			}
		}
		return true;
	}

	/**
	 * 親カテゴリDB処理
	 * @param	$targetArray	対象データ（csvファイル1行分）
	 * @param	$parentID		親カテゴリID（0：親カテゴリ、0以外：子カテゴリ）
	 * @return	$dbCheck		DB処理結果（true：成功	false：失敗）
	 */
	protected function runCategory($targetArray, $parentID) {
		$dataArray = array();		// 更新データ格納用の配列
		$dbCheck = "";				// DB動作結果
		$table = "";				// テーブル名
		$where = "";				// SQL実行用のwhere句
		$key = "";					// DB検索用Key

		// 親カテゴリor子カテゴリの設定をする。
		if($parentID == 0) {
			// 親カテゴリDB登録データ生成
			$dataArray = array(	COLUMN_NAME_PARENT_ID=>$targetArray[CATEGORY_ID_COLUMN_CATEGORY],
								COLUMN_NAME_PARENT_NAME=>$targetArray[CATEGORY_NAME_COLUMN_CATEGORY],
					 			COLUMN_NAME_PARENT_IMAGE=>$targetArray[IMAGE_COLUMN_CATEGORY],
								COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_CATEGORY] );
			// 親カテゴリテーブル
			$table = TABLE_NAME_PARENT_CATEGORY;
			// key項目設定
			$key = $dataArray[COLUMN_NAME_PARENT_ID];
			// where句生成
			$where = "parent_id = {$key}";
		} else {
			// 子カテゴリDB登録データ生成
			$dataArray = array(
					COLUMN_NAME_CATEGORY_ID=>$targetArray[CATEGORY_ID_COLUMN_CATEGORY],
					COLUMN_NAME_CATEGORY_NAME=>$targetArray[CATEGORY_NAME_COLUMN_CATEGORY],
					COLUMN_NAME_CATEGORY_IMAGE=>$targetArray[IMAGE_COLUMN_CATEGORY],
					COLUMN_NAME_PARENT_ID=>$targetArray[PARENT_ID_COLUMN_CATEGORY],
					COLUMN_NAME_VIEW_STATUS=>$targetArray[DELETE_COLUMN_CATEGORY] );
			// 子カテゴリテーブル
			$table = TABLE_NAME_CHILD_CATEGORY;
			// key項目設定
			$key = $dataArray[COLUMN_NAME_CATEGORY_ID];
			// where句生成
			$where = "category_id = {$key}";
		}

		// 削除フラグ取得
		$deleteFlg = $this->convertDeleteFlg($dataArray[COLUMN_NAME_VIEW_STATUS]);
		//削除フラグチェック
		if($deleteFlg){
			// DB削除処理(表示フラグ更新)
			$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
		} else {
			// データ存在チェック（true：データあり（データ更新）、false：データなし（データ追加））
			$dbCheck = $this->manager->db_manager->get($table)->checkData($key);
			if($dbCheck) {
				// DBUpdate処理
				$dbCheck = $this->manager->db_manager->get($table)->update($dataArray, $where);
			} else {
				// DBinsert処理
				$dbCheck = $this->manager->db_manager->get($table)->insertCategory($dataArray);
			}
		}
		return $dbCheck;
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
		$data = mb_convert_encoding($data, 'UTF-8', 'sjis-win');
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
		if($dataCount != HEADER_COUNT_CATEGORY){
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
					$errorMessage[] = "{$this->{$row[CATEGORY_ID_COLUMN_CATEGORY]}}行目とデータが重複しています。 {$errorLineCount}行目<br>";
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
		if(sizeof($checkData) != HEADER_COUNT_CATEGORY){
			$result = false;
		}
		return $result;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @param	$lineCount	現在のcsvの行数
	 * @return	$result		チェック結果
	 */
	protected function dataPrimaryCheck($checkData, $lineCount) {
		$result = true;
		// キー項目が前にチェックしたデータにあったかチェックする
		if ($this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]} != null) {
			$result = false;
		} else {
			$this->{$checkData[CATEGORY_ID_COLUMN_CATEGORY]} = $lineCount;
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
