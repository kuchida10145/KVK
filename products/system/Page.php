<?php
/**
 * ページコントローラー基本クラス
 *
 */
require_once(dirname(__FILE__).'/controller/Management.php');
abstract class Page
{
	public $manager = NULL;

	public function __construct()
	{
		$this->redierct301();
		$this->manager = Management::getInstance();
	}

	public function redierct301()
	{
		return true;
	}

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
	 * @param	$updateArray, $nowStatus
	 * @return	$result
	 */
	public function systemUpdate($updateArray, $nowStatus) {
		$whereSystem = "";			// システムデータ更新用where句
		$dbCheck = "";

		$whereSystem = COLUMN_NAME_SYSTEM_STATUS." = '".$nowStatus."'";
		$dbCheck = $this->manager->db_manager->get(TABLE_NAME_SYSTEM_STATUS)->update($updateArray, $whereSystem);

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