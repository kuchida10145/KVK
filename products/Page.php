<?php
/**
 * ページコントローラー基本クラス
 *
 */
require_once(dirname(__FILE__).'/system/Management.php');
abstract class Page
{
	public $manager = NULL;

	private $_data = array();

	public function __construct()
	{
		$this->redierct301();
		$this->manager = Management::getInstance();
	}

	public function redierct301($url=NULL)
	{
		return true;
	}

	public function error( $str ){
		echo $str;
		exit;
	}

	public function commonHeaderOutputs(){
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // 過去の日付
	}

// --------------------------------------------------------------
// ■ 共通関数
// --------------------------------------------------------------

	function getDirname( $filename ){
		if( $filename != "" ){
			$ar = explode( '_',$filename);
			if( count($ar)==2){
				return $ar[0];
			}else{
				return false;
			}
		} else {
			return false;
		}
	}

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
	 * システムステータスチェック
	 * @param	$statusID
	 * @return	$result
	 */
	public function systemStatusCheck ($statusID) {
		if($statusID == SYSTEM_STATUS_PDF_MAKE) {
			$this->{KEY_ERROR_MESSAGE} =  ERROR_MSG_STATUS_ERROR;
			return false;
		} elseif ($statusID == SYSTEM_STATUS_PDF_FINISH) {
			$this->{KEY_ERROR_MESSAGE} =  PDF_FINISH_NG;
			return false;
		}
		return	true;
	}

	/**
	 * システムステータス更新（status = 0:通常 or 1:pdf作成待ち）
	 * @param	$updateStatus
	 * @return	$result
	 */
	public function systemUpdate($updateStatus) {
		$whereSystem = "";			// システムデータ更新用where句
		$dbCheck = "";
		$nowStatus = "";
		$updateArray = array();

		$nowStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();

		$updateArray = array(
				COLUMN_NAME_SYSTEM_STATUS=>$updateStatus,
				COLUMN_NAME_PDF_TIME=>$this->pdfTime
		);

		$whereSystem = COLUMN_NAME_SYSTEM_STATUS." = '".$nowStatus."'";
		$dbCheck = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->update($updateArray, $whereSystem);

		if(!$dbCheck) {
			$this->{KEY_DB_CHECK_MESSAGE} = "システムステータスの更新に失敗しました。<br>";
		}

		return $dbCheck;
	}

	/**
	 * 画面ステータス取得
	 * @param	$dispName		対象画面
	 * @return	$returnVal		画面ステータス
	 */
	public function getDispStatus($dispName) {
		$returnVal = "";

		$returnVal = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getDispStatus($dispName);

		return $returnVal;
	}


	/**
	 * 画面ステータス更新処理
	 * @param	$dispName		対象画面
	 * @param	$updateStatus	更新ステータス
	 * @return	$result			画面ステータス更新結果
	 */
	public function viewInitial($dispName, $updateStatus) {
		$whereSystem = "";			// システムデータ更新用where句
		$dbCheck = "";
		$nowStatus = "";
		$nowDataArray = array();
		$nowDataRow = array();

		$nowDataArray = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getAll();
		$nowDataRow = $nowDataArray[0];

		// 対象画面の項目に値をセット
		foreach ($nowDataRow as $key => $value) {
			if ($key == $dispName) {
				$nowDataRow[$key] = $updateStatus;
			}
		}

		$nowStatus = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getSystemStatus();
		$whereSystem = COLUMN_NAME_SYSTEM_STATUS." = '".$nowStatus."'";
		$dbCheck = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->update($nowDataRow, $whereSystem);

		if(!$dbCheck){
			$this->{KEY_ERROR_MESSAGE} = MESSAGE_FAIL_PAGE_INITIAL;
		}

		return $dbCheck;
	}

	/**
	 * PDF作成予定時間取得
	 */
	public function getMakePdfTime(){
		$nowDataArray = array();
		$nowDataRow = array();
		$explodeVal = array();
		$status = "";

		$nowDataArray = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->getAll();
		$nowDataRow = $nowDataArray[0];

		// 日付、時間、分を共通変数に登録
		foreach ($nowDataRow as $key => $value) {
			if($key == COLUMN_NAME_SYSTEM_STATUS){
				$status = $value;
			} elseif($key == COLUMN_NAME_PDF_TIME) {
				$explodeVal = explode(" ", $value);
				$this->dayVal = $explodeVal[0];
				$explodeVal = explode(":", $explodeVal[1]);
				$this->dayHour = $explodeVal[0];
				$this->dayMin = $explodeVal[1];
			}
		}

		// 共通変数チェック
		if($this->dayVal == "0000-00-00"){
			$this->dayVal = date("Y-m-d");
		}

		if($status == SYSTEM_STATUS_NORMAL) {
			$this->pdfStatus = SYSTEM_STATUS_NORMAL_VAL;
		} elseif ($status == SYSTEM_STATUS_PDF_WAIT) {
			$this->pdfStatus = SYSTEM_STATUS_PDF_WAIT_VAL;
		} elseif ($status == SYSTEM_STATUS_PDF_MAKE) {
			$this->pdfStatus = SYSTEM_STATUS_PDF_MAKE_VAL;
		} elseif ($status == SYSTEM_STATUS_PDF_FINISH) {
			$this->pdfStatus = SYSTEM_STATUS_PDF_FINISH_VAL;
		} elseif ($status == SYSTEM_STATUS_PDF_STOP) {
			$this->pdfStatus = SYSTEM_STATUS_PDF_STOP_VAL;
		}
	}

	/**
	 * システムステータス登録時間保持
	 * @param	$day		日付選択値
	 * @param	$hour		時間入力値
	 * @param	$min		分入力値
	 * @param	$dayLabel	日付初期値
	 * @return	boolean	$result(true：時間セット完了	false：時間セット失敗)
	 */
	public function setPdfTime($day, $hour, $min, $dayLabel) {
		$checkTime = "";
		$nowData = date("Y-m-d H:i:s");

		// 日付セット
		if($day == '') {
			$day = $dayLabel;
		}

		// 空白チェック
		if($hour == '' or $min == '') {
			$this->{KEY_ERROR_MESSAGE} = MINYURYOKU_NG;
			return false;
		}

		// 0埋め
		$hour = sprintf("%02d", $hour);
		$min = sprintf("%02d", $min);

		$checkTime = $day." ".$hour.":".$min.":00";

		// 日付チェック
		if($this->validateDate($checkTime)) {
			$this->{KEY_PDF_MAKE_TIME} = $checkTime;
		} else {
			$this->{KEY_ERROR_MESSAGE} = PDF_TIME_NG;
			return false;
		}

		// 過去の時間かどうかチェック
		if($checkTime < $nowData) {
			$this->{KEY_ERROR_MESSAGE} = PDF_TIME_NG_KAKO;
			return false;
		}

		$this->pdfTime = $checkTime;

		return true;
	}

	/**
	 * 日付チェック
	 */
	protected function validateDate($date, $format = 'Y-m-d H:i:s'){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}