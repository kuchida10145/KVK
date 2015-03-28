<?php
/**
 *
 */
class DbModel
{
	var $db;
	var $table;
	var $use_sequence = false;

	public function getField()
	{
		return array('*');
	}

	/**
	 * コンストラクタ
	 *
	 * @param Database $db Databaseクラス
	 */
	function DbModel($db,$table)
	{
		$this->db = $db;
		$this->table = $table;
	}

	/**
	 * データの挿入
	 *
	 * @param Array $param Insertするデータ配列
	 * @return Int 成功時は主キーを、失敗時はfalseを返す
	 */
	function insert($param)
	{
		//シークエンスIDを取得
		if($this->use_sequence === true)
		{
			$id = $this->createSeaquence($this->table);
			$param['id'] = $id;
		}

		if(!isset($param['regist_date']) || $param['regist_date'] == '')
		{
			$param['regist_date'] = 'NOW()';
		}
		if(!isset($param['update_date']) || $param['update_date'] == '')
		{
			$param['update_date'] = 'NOW()';
		}

		//余計な配列を取り除く
		$data = $this->setRecord($this->getField(),$param);

		if($this->db->insert($this->table,$data) === false)
		{
			return false;
		}
		if($this->use_sequence === true)
		{
			return $id;
		}
		else
		{
			$temp = $this->db->GetData('SELECT LAST_INSERT_ID() as id');
			return $temp['id'];
		}
	}


	/**
	 * シークエンスを生成
	 *
	 * @param String $table シークエンスのテーブル
	 * @return Int シークエンス
	 */
	function createSeaquence($table)
	{
		$this->db->query('UPDATE '.$table.'_sequence SET sequence=LAST_INSERT_ID(sequence+1)');
		$c_s= $this->db->GetData('SELECT LAST_INSERT_ID() as id');
		return $c_s['id'];
	}

	/**
	 * リミットの作成
	 *
	 * @param Int $now 現在のページ番号
	 * @param Int $cnt 取得するデータ件数
	 * @return String  LIMIT文
	 */
	function createLimit( $now, $cnt = null)
	{
		$limit = '';
		if(!is_numeric($now) || $now =="")
		{
			$now = 0;
		}
		$now = ($now <= 0) ? 0:$now-1;

		if(is_numeric($cnt) && $cnt > 0)
		{
			$now = $now*$cnt;
			$limit = " Limit {$now},{$cnt}";
		}
		return $limit;
	}

	/**
	 * INSERT UPDATE用データ配列の生成
	 *
	 * @param Array $keys フィールド
	 * @param Array $param データ
	 * @param String $type セットタイプ（allは$keysの内容をすべて設定）
	 * @return Array 生成結果
	 */
	function setRecord($keys,$param,$type = 'all')
	{
		$data = array();
		foreach($keys as $key )
		{
			if($type == 'all')
			{
				$data[$key] = getParam($param,$key);
			}
			else if(array_key_exists($key,$param))
			{
				$data[$key] = $param[$key];
			}
		}
		return $data;
	}


	/**
	 * IDから商品を取得
	 *
	 * @param int $id ID
	 * @return Array 結果
	 */
	function findById($id)
	{

		$id = $this->db->escape_string($id);
		$feild = implode(',',$this->getField());
		$sql = "SELECT {$feild} FROM {$this->table} WHERE id = '{$id}'";

		return $this->db->getData($sql);
	}


	/**
	 * DB用に文字列をエスケープ
	 *
	 * @param String $str エスケープする文字列
	 * @return String エスケープ後の文字列
	 */
	function escape_string($str)
	{
		return $this->db->escape_string($str);
	}


	/**
	 * すべてのデータを無条件で取得
	 *
	 * @return Multi 検索結果
	 */
	function getAll()
	{
		$feild = implode(',',$this->getField());
		$sql = "SELECT {$feild} FROM {$this->table}";

		return $this->db->getAllData($sql);
	}

	/**
	 * IDを使ってデータ更新
	 *
	 * @param Int $id ID
	 * @param Array $param 更新データ
	 * @return Multi 成功時はIDが、失敗時はfalseが戻ってくる
	 */
	function updateById($id,$param)
	{
		$id = $this->db->escape_string($id);
		$where = " id = '{$id}'";
		$param['update_date'] = 'NOW()';
		$param = $this->setRecord($this->getField(),$param,'isset');

		if($this->db->update($this->table,$param,$where) !== false)
		{
			return $id;
		}
		return false;
	}

	/**
	 * IDを使って削除
	 *
	 * @param Int $id 会員ID
	 * @return Bool 結果
	 */
	function deleteById($id)
	{
		$id = $this->db->escape_string($id);
		$where = " id = '{$id}'";
		return $this->db->delete($this->table,$where);
	}

	/**
	 * where句を指定して削除
	 *
	 * @param String $where WHERE句
	 * @return Bool 結果
	 */
	function delete($where)
	{
		return $this->db->delete($this->table,$where);
	}


	/**
	 * データの更新
	 *
	 * @param Array $param 更新するデータ
	 * @param String $where 更新条件
	 * @return Bool 結果
	 */
	function update($param,$where)
	{
		return $this->db->update($this->table,$param,$where);
	}


	/**
	 * 検索条件の最大件数を取得する
	 *
	 * @param Array $param パラメータ
	 */
	function searchMaxCnt($param)
	{
		$where = $this->createWhere($param);

		if(strpos(strtolower($where),'where') === false && $where !== '')
		{
			$where = 'WHERE '.$where;
		}

		$sql = $this->searchSql()." {$where}";



		$count = $this->db->getCount($sql);
		return $count;
	}


	/**
	 * 検索して一覧で取得
	 *
	 * @param Array $param 検索情報
	 * @param String $limit 取得件数
	 * @param String $order Order句
	 */
	function search($param ,$limit='',$order='')
	{
		$where = $this->createWhere($param);
		if($order != '')
		{
			$order = 'ORDER BY '.$order;
		}
		if(strpos(strtolower($where),'where') === false && $where !== '')
		{
			$where = 'WHERE '.$where;
		}

		$sql = $this->searchSql()." {$where} {$order} {$limit}";
		return $this->db->getAllData($sql);
	}

	/**
	 * 検索用SQL
	 *
	 * @param Array $param 検索情報
	 * @param String $limit 取得件数
	 * @param String $order Order句
	 */
	function searchSql()
	{
		$sql = "SELECT * FROM {$this->table} ";
		return $sql;
	}


	/**
	 * WHERE文の生成
	 *
	 * @param Array $param Where文生成に必要なデータ
	 * @return String Where文
	 */
	protected function createWhere($param)
	{
		$where = '';
		if(!is_array($param))
		{
			$where = $param;
			if(strpos(strtolower($where),'where') === false && $where !== '')
			{
				$where = ' WHERE '.$where;
			}
		}

		return $where;
	}








	/*=============================
	 * 法人用
	 *
	 *=============================*/
	 /**
	 * 法人用検索処理
	 *
	 */
	public function manageSearch($param,$id,$order="",$limit = "")
	{
		$field = implode(',',$this->getField());
		$where = $this->manageSearchWhere($param,$id);
		$sql = " SELECT {$field} FROM {$this->table} {$where} {$order} {$limit}";

		return $this->db->getAllData($sql);
	}

	/**
	 * 法人用検索結果数取得
	 *
	 */
	public function manageSearchCnt($param,$id)
	{
		$where = $this->manageSearchWhere($param,$id);
		$sql   = " SELECT count(id) as cnt FROM {$this->table} ".$where;
		$temp  =  $this->db->getData($sql);

		if(!$temp)
		{
			return 0;
		}
		return $temp['cnt'];
	}

	/**
	 * 法人用検索結WHERE区生成処理
	 *
	 */
	protected function manageSearchWhere($param,$id)
	{
		$wheres[] = "company_id ='{$id}'";

		$where = implode(' AND ',$wheres);

		if($where != "")
		{
			$where = ' WHERE '.$where;
		}

		return $where;
	}


	/*=============================
	 * 管理者用
	 *
	 *=============================*/
	public function adminSearch($param,$limit = "",$order = '')
	{

		$field = implode(',',$this->getField());

		$where = $this->adminSearchWhere($param);
		$sql = " SELECT {$field} FROM {$this->table} {$where} {$order} {$limit}";
		return $this->db->getAllData($sql);
	}


	public function adminSearchCnt($param)
	{
		$where = $this->adminSearchWhere($param);
		$sql   = " SELECT count(id) as cnt FROM {$this->table} ".$where;
		$temp  =  $this->db->getData($sql);

		if(!$temp)
		{
			return 0;
		}
		return $temp['cnt'];
	}

	protected function adminSearchWhere($param)
	{
		$wheres = array();

		$where = implode(' AND ',$wheres);

		if($where != "")
		{
			$where = ' WHERE '.$where;
		}

		return $where;
	}



	/**
	 * フロント検索用WHERE文の生成
	 *
	 * @param Array $param Where文生成に必要なデータ
	 * @return String Where文
	 */
	protected function createSearchWhere($param,$flg=false)
	{
		//s,*サロンテーブル
		//c.*法人テーブル

		static $where;
		$wheres = array();
		$status = ST_APPLY;
		$company = '';

		if($flg == true && $where != '')
		{
			//企業ＩＤ
			if(getParam($param,'company_id'))
			{
				$company = " s.company_id = '".getParam($param,'company_id')."' AND ";
			}
			return " WHERE ".$company.$where;
		}


		$wheres[] = " s.status = '{$status}' AND c.status = '{$status}' AND c.id = s.company_id AND view_status = '1' ";

		//企業ＩＤ
		if(getParam($param,'company_id'))
		{
			$company = " s.company_id = '".getParam($param,'company_id')."' AND ";
		}

		//都道府県
		if(getParam($param,'pref_id') != '')
		{
			$wheres[] = " s.pref_id = '".$this->escape_string(getParam($param,'pref_id'))."' ";
		}
		//業種
		if(getParam($param,'business') != '')
		{
			$wheres[] = " c.business_category = '".$this->escape_string(getParam($param,'business'))."' ";
		}
		//出前面接
		if(getParam($param,'interview_flg') != '')
		{
			$wheres[] = " s.interveiw_flg = '".$this->escape_string(getParam($param,'interveiw_flg'))."' ";
		}
		//オープニングスタッフ
		if(getParam($param,'opening_staff') != '')
		{
			$wheres[] = " s.opening_staff = '".$this->escape_string(getParam($param,'opening_staff'))."' ";
		}



		//雇用形態
		if(is_array(getParam($param,'employment')) || getParam($param,'employment') != '')
		{
			$temp_wheres = array();
			$employment = getParam($param,'employment');
			if(!is_array($employment))
			{
				$employment = array($employment);
			}

			foreach($employment as $val)
			{
				$emp = $this->escape_string($val);
				//職種が設定されている場合
				if(getParam($param,'job_category') != '' && !is_array(getParam($param,'job_category')))
				{
					$job =  $this->escape_string(getParam($param,'job_category'));
					$val = "%\"{$job}:{$emp}\"%";
				}
				else
				{
					$val = "%:{$emp}\"%";
				}


				$temp_wheres[] = " s.job_employment LIKE '{$val}' ";
			}
			$wheres[] = "(".implode(' OR ',$temp_wheres).")";
		}
		//職種
		else if(getParam($param,'job_category') != '')
		{
			$val = '"'.getParam($param,'job_category').'"';
			$wheres[] = " s.job_category LIKE '%{$val}%'";
		}


		//特徴
		if(is_array(getParam($param,'treatment')) || getParam($param,'treatment') != '')
		{
			$temp_wheres = array();
			$treatment = getParam($param,'treatment');
			if(!is_array($treatment))
			{
				$treatment = array($treatment);
			}

			foreach($treatment as $val)
			{
				$val = $this->escape_string($val);
				$temp_wheres[] = " s.treatment LIKE '%\"".$val."\"%' ";
			}
			$wheres[] = implode(' AND ',$temp_wheres);
		}

		//サロンのID配列
		if(is_array(getParam($param,'salon_ids')))
		{
			$salon_ids = getParam($param,'salon_ids');
			$salon_ids_str = implode(',',$salon_ids);
			$wheres[] = " s.id IN ({$salon_ids_str}) ";
		}

		//路線・駅
		$common_wheres = array();

		//路線の処理
		if(is_array(getParam($param,'line_id')) || is_array(getParam($param,'line_id')) != '')
		{
			$line = getParam($param,'line_id');
			if(!is_array($line))
			{
				$line = array($line);
			}
			foreach($line as $val)
			{
				if(ctype_digit(strval($val)) === true)
				{
					$val = '"line":"'.$val.'"';
					$common_wheres[] = " s.station LIKE '%{$val}%'";
				}
			}
		}
		//駅の処理
		if(is_array(getParam($param,'station_id')) || is_array(getParam($param,'station_id')) != '')
		{
			$line = getParam($param,'station_id');
			if(!is_array($line))
			{
				$line = array($line);
			}
			foreach($line as $val)
			{
				if(ctype_digit(strval($val)) === true)
				{
					$val = '"station":"'.$val.'"';
					$common_wheres[] = " s.station LIKE '%{$val}%'";
				}
			}
		}



		//エリア・都市
		//エリア
		if(is_array(getParam($param,'area_id')) || is_array(getParam($param,'area_id')) != '')
		{
			$manager = Management::getInstance();
			$area = getParam($param,'area_id');
			if(!is_array($area))
			{
				$line = array($area);
			}

			foreach($area as $area_id)
			{
				if($temp = $manager->db_manager->get('area_city')->findById($area_id))
				{
					$city   = json_decode($temp['city'],true);
					$station = json_decode($temp['station'],true);

					if(count($city) !=0)
					{
						$common_wheres[] = " s.city_id IN (".implode(',',$city).") ";
					}
					if(is_array($station))
					{
						foreach($station as $val)
						{
							if(ctype_digit(strval($val)) === true)
							{
								$val = '"station":"'.$val.'"';
								$common_wheres[] = " s.station LIKE '%{$val}%'";
							}
						}
					}
				}
			}
		}

		//都市
		if(is_array(getParam($param,'city_id')) || is_array(getParam($param,'city_id')) != '')
		{
			$city = getParam($param,'city_id');
			if(!is_array($city))
			{
				$city = array($city);
			}
			if(count($city) !=0)
			{
				$common_wheres[] = " s.city_id IN (".implode(',',$city).") ";
			}
		}
		//駅・路線結合
		if(count($common_wheres) != 0)
		{
			$wheres[] = "(".implode(" OR ",$common_wheres).")";
		}

		if(getParam($param,'keyword') != '')
		{
			$keyword = $this->escape_string(getParam($param,'keyword'));

			$words = array();
			$words[] = " s.name LIKE '%{$keyword}%'";
			$words[] = " s.name_kana LIKE '%{$keyword}%'";
			$words[] = " s.pref_text LIKE '%{$keyword}%'";
			$words[] = " s.city_text LIKE '%{$keyword}%'";
			$words[] = " s.address1 LIKE '%{$keyword}%'";
			$words[] = " s.remarks LIKE '%{$keyword}%'";
			$words[] = " c.appeal_title LIKE '%{$keyword}%'";
			$words[] = " c.appeal_comment LIKE '%{$keyword}%'";
			$words[] = " c.name LIKE '%{$keyword}%'";
			$words[] = " c.name_kana LIKE '%{$keyword}%'";
			$wheres[] = " (".implode(' OR ',$words).") ";
		}



		$where = implode(' AND ',$wheres);
		return " WHERE ".$company.$where;
	}

}