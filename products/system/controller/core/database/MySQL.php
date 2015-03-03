<?php
/**
 * データベースクラス
 *
 * @package Sixpence
 * @author Yuuki.Kuchida <Kuchida@6web.co.jp>
 * @since PHP 5.4.0
 * @version 1.0.0
 */

class Database{

	var $sql;
	var $connid;
	var $res;
	var $row;
	var $config;
	var $tran = false;


	/**
	 * コンストラクタ
	 * @version 1.1.0
	 */
	function Database($config)
	{
		$this->setConfig($config);
		$this->connect();
	}


	/**
	 * データベースの接続情報などの設定
	 * @version 1.1.0
	 */
	function setConfig($config)
	{
		$this->config = $config;
	}


	/**
	 * コネクション
	 * 接続して、IDを返す
	 * @version 1.1.0
	 */
	function connect()
	{
		if($this->connid == NULL)
		{

			$this->connid = mysqli_connect($this->config['host'],$this->config['user'],$this->config['pass']) or exit('can not connect');
			mysqli_select_db( $this->connid,$this->config['dbname']);
			///mysqli_query($this->connid,'SET NAMES '.$this->config['charset']);
			mysqli_set_charset( $this->connid,$this->config['charset']);
		}

	}

	/**
	 * 切断
	 * @version 1.1.0
	 */
	function Close()
	{
		mysqli_close($this->connid);
		$this->connid = NULL;
	}



	/**
	 * クエリ関数
	 *
	 * @param string $sql SQL文
	 * @return resource,boolean 実行結果
	 * @version 1.1.0
	 */
	function query($sql)
	{
		if($this->connid  === NULL)
		{
			$this->connid = $this->connect();
		}
		$this->res = mysqli_query($this->connid,$sql);
		return $this->res;
	}

	/**
	 * 挿入実行
	 *
	 * @param string $table テーブル名
	 * @param array $array データ配列
	 * @return int 実行結果
	 * @version 1.1.0
	 */
	function insert($table,$array)
	{
		//$arrayを分解
		$fields = "";
		$values = "";
		foreach($array as $key=>$value)
		{
			if(is_array($value))
			{
				foreach($value as $key2 => $val2)
				{
					$value[$key2] = $this->escape_string($val2);
				}

				$fields .= "`".$key."`,";
				$values .= "'/".implode("/",$value)."/',";
			}
			else if($value == "NOW()")
			{
				$fields .= "`".$key."`,";
				$values .= $value.",";
			}
			else
			{
				$value = $this->escape_string($value);
				if(is_int($value))
				{
					$fields .= "`".$key."`,";
					$values .= $value.",";
				}

				else if($value != "")
				{
					$fields .= "`".$key."`,";
					$values .= "'".$value."',";
				}
			}
		}
		$fields = substr($fields, 0, -1);
		$values = substr($values, 0, -1);
		$sql = "INSERT INTO $table ($fields) VALUES ($values)";
		//print "<br>".$sql."\r\n<br>---------------------<br>\r\n";
		//exit();
		//print $sql."<br />";
		return $this->query($sql);
	}

	/**
	 * 更新
	 *
	 * @param string $table テーブル名
	 * @param array $array データ配列
	 * @param string $where 条件
	 * @return int 実行結果
	 * @version 1.1.0
	 */
	function update($table,$array,$where)
	{
		//$arrayを分解
		$data = "";
		foreach($array as $key=>$value)
		{

			if(is_array($value))
			{
				foreach($value as $key2 => $val2)
				{
					$value[$key2] = $this->escape_string($val2);
				}

				$data .= "`".$key."`='/".implode("/",$value)."/',";
			}
			else if($value == "NOW()")
			{
				$data .= "`".$key."` = ".$value.",";
			}
			else
			{
				$value = $this->escape_string($value);
				$key = $key;
				$data .= "`".$key."` = '".$value."',";
			}
		}
		$data = substr($data, 0, -1);
		$sql = "UPDATE $table SET $data WHERE $where";

		return $this->query($sql);
	}


	/**
	 * 削除する
	 *
	 * @param string $table テーブル名
	 * @param string $where 条件
	 * @return int 実行結果
	 * @version 1.1.0
	 */
	function delete($table,$where)
	{
		$sql = "DELETE FROM $table WHERE $where";
		return $this->query($sql);
	}


	/**
	 * テーブルのデータを全て削除する
	 *
	 * @param string $table テーブル名
	 * @return int 実行結果
	 * @version 1.1.0
	 */
	function truncate($table)
	{
		$sql = "TRUNCATE TABLE $table";
		return $this->query($sql);
	}

	/**
	 * データを一件取得する。
	 *
	 * @param string $sql SQL文
	 * @return array,NULL 実行結果
	 * @version 1.1.0
	 */
	function getData($sql)
	{

		$this->row = NULL;
		//print $sql;
		$this->res = $this->query($sql);
		if($this->res)
		{
			$this->row = mysqli_fetch_array($this->res, MYSQL_ASSOC);
		}
		return $this->row;
	}

	/**
	 * データ件数を取得する
	 *
	 * @param string $sql SQL文
	 * @return array,NULL 実行結果
	 * @version 1.1.0
	 */
	function getCount($sql)
	{
		$this->row = NULL;
		$this->res = $this->query($sql);
		$this->row = mysqli_num_rows($this->res);
		mysqli_free_result($this->res);
		return $this->row;
	}

	/**
	 * データをすべて取得する<br>
	 *
	 * @param string $sql SQL文
	 * @return array,NULL 実行結果
	 * @version 1.1.0
	 */
	function getAllData($sql)
	{

		//unset($this->row);
		$this->row = NULL;
		$this->res = $this->query($sql);

		if($this->res != "")
		{
			$count = mysqli_num_rows($this->res);
			for($i = 0; $i < $count; $i++)
			{
				$this->row[] = mysqli_fetch_array($this->res, MYSQL_ASSOC);
			}
			mysqli_free_result($this->res);
		}

		return $this->row;
	}

	/**
	 * 最後に挿入したIDを取得
	 *
	 * @retrun int ＩＤ
	 * @version 1.1.0
	 */
	function getLastId()
	{
		return mysqli_fetch_array($this->query("SELECT LAST_INSERT_ID() as id"), MYSQL_ASSOC);
	}


	function escape_string($str)
	{
		if(is_array($str))
		{
			foreach($str as $key => $val)
			{
				$str[$key] =  mysqli_real_escape_string($this->connid,$val);
			}
			return $str;
		}

		return mysqli_real_escape_string($this->connid,$str);
	}


	/**
	 * トランザクション処理の開始
	 *
	 */
	function startTran()
	{
		$this->tran = true;
		$this->query("set autocommit = 0");
		$this->query('begin');
	}

	/**
	 * トランザクション処理の終了
	 *
	 * @param Bool $flg コミットするかロールバックするか
	 */
	function endTran($flg=true)
	{
		if($this->tran == false)
		{
			return;
		}

		if($flg === true)
		{
			$this->commit();
		}
		else
		{
			$this->rollback();
		}
	}

	/**
	 * ロールバック（プライベートメソッドとして利用すること）
	 */
	function rollback()
	{
		$this->tran = false;
		$this->query("rollback");
	}

	/**
	 * コミット（プライベートメソッドとして利用すること）
	 */
	function commit()
	{
		$this->tran = false;
		$this->query("commit");
	}

}
?>