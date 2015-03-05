<?php
include_once('/../../Page.php');
abstract class AbstractImportCsv extends Page{
	protected abstract function dataFormCheck($checkData, $line_count);		// cavデータ形式チェック
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

	public function executeImport($filename, $testFlg) {
		$result = true;
		$line_count = 0;			// csvファイル行数カウント用
		$where = "";				// sql実行用のwhere句
		$parentID = "";				// 親カテゴリID格納用
		$dbCheck = "";				// db操作結果

		// 拡張子チェック
		$error = $this->checkExtension($filename);
		if(!$error) {
			$this->set('errorMessage', 'CSVファイルを取り込んでください。<br>');
			$this->set('resultMessage', '失敗<br>');
			return false;
		}
		// CSVデータ取得
		$csvData = $this->getCsvData($filename);
		// CSVデータ行数チェック
		if (count($csvData) < 2) {
			$this->set('errorMessage', 'CSVファイルにデータがありません。<br>');
			$this->set('resultMessage', '失敗<br>');
			return false;
		}
		// CSVカラム数チェック
		$error = $this->csvColumnCheck(count($csvData[0]));
		if(!$error) {
			$this->set('errorMessage', 'CSVファイルの項目数が一致しません。<br>');
			$this->set('resultMessage', '失敗<br>');
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
			$this->set('errorMessage', '');
			$this->set('resultMessage', '成功<br>');
		} else {
			// csvファイル全データに対してDB処理を行う。
			foreach ($csvData as $row) {
				// 親カテゴリID取得
				$parentID = $row[2];
				if($parentID == '0') {
					// 親カテゴリDB処理
					$dbCheck = $this->runParentCategory($row);
				} else {
					// 子カテゴリDB処理
					$dbCheck = $this->runChildCategory($row);
				}
				$line_count++;
			}

			if($dbCheck) {
				$this->set('errorMessage', '');
				$this->set('resultMessage', '成功<br>');
			} else {
				$this->set('errorMessage', 'データベースエラーが発生しました。<br>');
				$this->set('resultMessage', '失敗<br>');
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * 親カテゴリDB処理
	 * @param	$targetArray 対象データ（csvファイル1行分）
	 * @return	$dbCheck DB処理結果（true：成功	false：失敗）
	 */
	protected function runParentCategory($targetArray) {
		$dataArray = array();		// 更新データ格納用の配列
		$dbCheck = "";				// DB動作結果

		// 配列を連想配列に変換
		$dataArray = array('category_id'=>$targetArray[0], 'parent_name'=>$targetArray[1], 'parent_image'=>$targetArray[3], 'view_status'=>$targetArray[4]);
		// 親カテゴリのキーは'category_id'
		$where = "category_id = {$dataArray['category_id']}";
		// ヘッダー行はチェックしない
		if($line_count != 0) {
			$deleteFlg = $this->convertDeleteFlg($dataArray['view_status']);	// 削除フラグ取得
			//削除フラグチェック
			if($deleteFlg){
				// DB削除処理(表示フラグ更新)
				$dbCheck = $this->manager->db_manager->get('parent_category')->update($dataArray, $where);
			} else {
				$dbCheck = $this->manager->db_manager->get('parent_category')->checkData($dataArray['category_id']);
				// 同じカテゴリIDのデータが存在する場合：Update
				// 同じカテゴリIDのデータが存在しない場合：Insert
				if($dbCheck) {
					// Update処理
					$dbCheck = $this->manager->db_manager->get('parent_category')->update($dataArray, $where);
				} else {
					// DBinsert処理
					$dbCheck = $this->manager->db_manager->get('parent_category')->insertParentCategory($dataArray);
				}
			}
		}
		return $dbCheck;
	}

	/**
	 * 子カテゴリDB処理
	 * @param	$targetArray 対象データ（csvファイル1行分）
	 * @return	$dbCheck DB処理結果（true：成功	false：失敗）
	 */
	protected function runChildCategory($targetArray) {
		$dataArray = array();		// 更新データ格納用の配列
		$dbCheck = "";				// DB動作結果

		// 配列を連想配列に変換
		$dataArray = array('category_id'=>$targetArray[0], 'category_name'=>$targetArray[1],
				'category_image'=>$targetArray[3], 'parent_id'=>$targetArray[2], 'view_status'=>$targetArray[4]);
		// 親カテゴリのキーは'category_id'
		$where = "category_id = {$dataArray['category_id']}";
		// ヘッダー行はチェックしない
		if($line_count != 0) {
			$deleteFlg = $this->convertDeleteFlg($dataArray['view_status']);	// 削除フラグ取得
			//削除フラグチェック
			if($deleteFlg){
				// DB削除処理(表示フラグ更新)
				$dbCheck = $this->manager->db_manager->get('child_category')->update($dataArray, $where);
			} else {
				$dbCheck = $this->manager->db_manager->get('child_category')->checkData($dataArray['category_id']);
				// 同じカテゴリIDのデータが存在する場合：Update
				// 同じカテゴリIDのデータが存在しない場合：Insert
				if($dbCheck) {
					// Update処理
					$dbCheck = $this->manager->db_manager->get('child_category')->update($dataArray, $where);
				} else {
					// DBinsert処理
					$dbCheck = $this->manager->db_manager->get('child_category')->insertChildCategory($dataArray);
				}
			}
		}
		return $dbCheck;
	}

	// エラーメッセージ取得
	public function getErrorMessage() {
		return $this->get('errorMessage');
	}

	// 結果メッセージ取得
	public function getResultMessage() {
		return $this->get('resultMessage');
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
			$this->set('errorMessage', $setMessage);
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

	/**
	 * 削除フラグ変換
	 * @param	$dalete_flg	削除フラグ
	 * @return	$result		変換後削除フラグ（true：非表示（データ削除扱い）, false：表示）
	 */
	public function convertDeleteFlg($dalete_flg){
		$result = false;

		if ($dalete_flg == '1'){
			$result = true;
		}

		return $result;
	}
}
