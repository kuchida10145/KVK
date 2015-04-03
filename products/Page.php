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
	 * システムステータス登録時間保持
	 * @param	$day,$hour,$min
	 * @return	boolean	$result(true：時間セット完了	false：時間セット失敗)
	 */
	public function setPdfTime($day, $hour, $min) {
		$checkTime = "";
		$nowData = date("Y-m-d H:i:s");

		// 空白チェック
		if($day == '' or $hour == '' or $min == '') {
			$this->{KEY_ERROR_MESSAGE} = MINYURYOKU_NG;
			return false;
		}

		// 0埋め
		$hour = sprintf("%02d", $hour);
		$min = sprintf("%02d", $min);

		$checkTime = $day." ".$hour.":".$min.":00";
		$checkTime = str_replace('/', '-', $checkTime);

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
	 * サーバJOB登録
	 * @param	$viewData
	 * @param	$viewHour
	 * @param	$viewMin
	 * @return	$jobNum
	 */
	public function registJob($viewData, $viewHour, $viewMin){
		$year = "";
		$month = "";
		$day = "";
		$hour = "";
		$min = "";
		$command = "";
		$jobNum = "";
		$dateArray = array();

		// 日付設定
		$dateArray = explode("/", $viewData);
		$year = $dateArray[0];
		$month = $dateArray[1];
		$day = $dateArray[2];
		$hour = $viewHour;
		$min = $viewMin;

//		$command = JOB_COMMAND;
		$command = "ls > /system/page/makepdf/ExecuteMakePdfFile.php";

		$desc = array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w"),
		);

		/* at コマンド実行 (at hhmm MMDDYYYY) */
		if(($proc = proc_open(sprintf("%s %02d%02d %02d%02d%04d",
				"/usr/bin/at", $hour, $min, $month, $day, $year), $desc, $pipe))){

			/* コマンド登録 */
			fputs($pipe[0], $command);
			fclose($pipe[0]);

			/* job 番号を STDERR から取得 */
			$buf = trim(fgets($pipe[2], 4096));
			fclose($pipe[2]);

			/* STDOUT close */
			fclose($pipe[1]);

			proc_close($proc);

			/* job 番号をリターン */
			$jobNum = preg_replace("/^job\s+(\d+).*$/", "$1", $buf);
		}

		$jobNum = mb_convert_encoding($jobNum, SYSTEM_CODE, CSV_CODE);

		return $jobNum;
	}

	/**
	 * 日付チェック
	 */
	protected function validateDate($date, $format = 'Y-m-d H:i:s'){
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}