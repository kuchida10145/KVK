<?php
/**
 * マネージメントクラス
 */
register_shutdown_function( 'my_shutdown_handler' );
require_once(dirname(__FILE__).'/config.php');
require_once(dirname(__FILE__).'/core/DatabaseManager.php');
require_once(dirname(__FILE__).'/core/Validation.php');

class Management
{
	var $db_manager;
	var $validation;
	var $view;

	/**
	 * コンストラクタ(プライベート)
	 */
	function Management()
	{
		$this->db_manager = new DatabaseManager();
		$this->validation = new Validation();
	}

	/**
	 * Singleton化
	 */
	static function getInstance()
	{
		static $_instance;
		if($_instance === NULL)
		{
			$_instance = new Management();
		}

		return $_instance;
	}

	/**
	 * Coreディレクトリのクラスを追加
	 *
	 * @param mixed $core 追加するクラスの名前
	 */
	function setCore($core = array())
	{
		if(is_array($core))
		{
			foreach($core as $c)
			{
				$this->_loadCore($c);
			}
		}
		else
		{
			$this->_loadCore($core);
		}

	}

	/**
	 * クラスのロード
	 *
	 * @param String $core 追加するクラスの名前
	 */
	function _loadCore($core)
	{
		$core_name = ucwords($core);
		$core_file= dirname(__FILE__).'/custom/core/'.$core_name.'.php';


		if(!isset($this->{$core}))
		{
			if(file_exists($core_file))
			{
				require_once($core_file);
				$this->{$core} = new $core_name();
			}
			else
			{
				exit('Core:'.$core.' file is not exits!');
			}
		}
		return true;
	}
}

function my_shutdown_handler(){
    $obj = Management::getInstance();
	$obj->db_manager->db->endTran(false);//開
}