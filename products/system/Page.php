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
}