<?php
/**
 * データベース管理クラス
 */
require_once(dirname(__FILE__).'/database/DbModel.php');
class DatabaseManager
{
	
	var $db;
	var $models = array();
	
	
	/**
	 * コンストラクタ
	 *
	 */
	function DatabaseManager()
	{
		$config = array(
			'host'=>DB_HOST,
			'user'=>DB_USER,
			'pass'=>DB_PASS,
			'dbname'=>DB_NAME,
			'charset'=>DB_CHARSET
		);
		
		//データベースクラス振り分け
		if(function_exists('mysqli_query'))
		{
			require_once(dirname(__FILE__).'/database/MySQLi.php');
		}
		else
		{
			require_once(dirname(__FILE__).'/database/MySQL.php');
		}
		$this->db = new Database($config);
		
	}
	
	
	/**
	 * データベースモデルを取得する
	 * 
	 * @param String $model 取得するデータベースモデルの名前
	 */
	function get($model)
	{
		
		//モデルが生成されていない場合
		if(!array_key_exists($model,$this->models))
		{
			$model_name = ucwords($model).'DbModel';
			$model_file = dirname(__FILE__).'/../model/database/'.$model_name.'.php';
			
			//モデルのファイルが存在する場合
			if(is_readable($model_file))
			{
				require_once($model_file);
				
				$this->models[$model] =  new $model_name($this->db,strtolower($model));
			}
			else
			{
				exit($model.' table is not readable!');
			}
		}
		
		return $this->models[$model];
	}
}