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
}