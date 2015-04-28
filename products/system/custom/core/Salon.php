<?php
/**
 * サロン
 *
 */
class Salon
{
	
	
	
	
	/**
	 * 指定サロンIDを気になるリストへ保存
	 *
	 */
	public function addFavorite($salon_id)
	{
		$manager = Management::getInstance();
		//クッキーからサロンIDの配列を生成
		$salon_ids = array();
		if(getParam($_COOKIE,'favorite_store') != '')
		{
			$favorite_temp = explode(':',getParam($_COOKIE,'favorite_store'));
			foreach($favorite_temp as $fav_val)
			{
				if($fav_val != '' && ctype_digit(strval($fav_val)) == true)
				{
					$salon_ids[] = $fav_val;
				}
			}
		}
		
		//サロンデータがない場合
		if($salon = $manager->db_manager->get('salon')->getDetail($salon_id))
		{
			$salon_ids[] = $salon_id;
			$salon_id_str = implode(':',$salon_ids);
			setcookie('favorite_store',$salon_id_str,time()+60*60*24*31,'/');
		}
	}
	
	
	/**
	 * 検索項目のテキストを生成
	 *
	 */
	public function createSearchText($param)
	{
		$manager = Management::getInstance();
		
		$city_text  = array();
		$text_array = array();
		$pref_text  = array();
		
		//都道府県・都市・エリア
		if(getParam($param,'pref_id'))
		{
			//エリア
			if(getParam($param,'area_id'))
			{
				$area_ids = $this->getIntIds($param,'area_id');
				
				if(count($area_ids) > 0 && $res = $manager->db_manager->get('area_city')->findByIds($area_ids))
				{
					foreach($res as $area)
					{
						$text_array[] = $area['name'];
					}
				}
			}
			
			//都市
			if(getParam($param,'city_id'))
			{
				$city_ids = $this->getIntIds($param,'city_id');
				
				if(count($city_ids) > 0 && $res = $manager->db_manager->get('city')->findByIds($city_ids))
				{
					foreach($res as $city)
					{
						$text_array[] = $city['city_name'];
					}
				}
			}
		}
		
		//路線
		if(getParam($param,'line_id'))
		{
			$line_ids = $this->getIntIds($param,'line_id');
			if(count($line_ids) > 0 && $res = $manager->db_manager->get('line_code')->findByIds($line_ids))
			{
				foreach($res as $line)
				{
					$text_array[] = $line['name'];
				}
			}
		}
		//駅
		if(getParam($param,'station_id'))
		{
			$station_ids = $this->getIntIds($param,'station_id');
			if(count($station_ids) > 0 && $res = $manager->db_manager->get('station')->findByIds($station_ids))
			{
				foreach($res as $station)
				{
					$text_array[] = $station['name']."駅";
				}
			}
		}
		
		//業種
		if(getParam($param,'business') && is_string(getParam($param,'business')))
		{
			if(getParam(business(),$param['business']))
			{
				$text_array[] = getParam(business(),$param['business']);
			}
		}
		//職種
		if(getParam($param,'job_category') && is_string(getParam($param,'job_category')))
		{
			if(getParam(job_category(),$param['job_category']))
			{
				$text_array[] = getParam(job_category(),$param['job_category']);
			}
		}
		
		//雇用形態
		if(getParam($param,'employment') && is_array(getParam($param,'employment')))
		{
			foreach($param['employment'] as $emp_id)
			{
				if(getParam(employment_status(),$emp_id))
				{
					$text_array[] = getParam(employment_status(),$emp_id);
				}
			}
		}
		
		
		if(getParam($param,'opening_staff') && $param['opening_staff'] == 1)
		{
			$text_array[] = "オープニングスタッフ募集";
		}
		
		if(getParam($param,'interview_flg') && $param['interview_flg'] == 1)
		{
			$text_array[] = "出張カフェ面接OK(新卒限定)";
		}
		
		//特徴3
		if(getParam($param,'treatment') && is_array(getParam($param,'treatment')))
		{
			$treatment = array(
						  14=>'ランクがある方OK',
						  15=>'美容通信生積極採用',
						  16=>'シャンプー施術なし',
						  17=>'大型サロン(11店舗以上)',
						  19=>'キッズ専門サロン',
						  20=>'バックシャンプー',
						  12=>'日曜日定休',
						  4 =>'寮･社宅完備',
						  1 =>'賞与あり',
						  18=>'託児所完備',
						  3 =>'講習･セミナー補助あり',
						  5 =>'社会保険完備',
						  2 =>'交通費支給',
						  11=>'完全週休2日',
						  10=>'産休･育児制度あり');
			foreach(getParam($param,'treatment') as $treat)
			{
				if(getParam($treatment,$treat))
				{
					$text_array[] = getParam($treatment,$treat);
				}
			}
		}
		//キーワード
		if(getParam($param,'keyword') != '')
		{
			$text_array[] = escapeHtml(getParam($param,'keyword'));
		}


		
		
		return implode('、',$text_array);
		
		
	}
	
	
	/**
	 * ID配列を取得する
	 *
	 */
	private function getIntIds($param,$key)
	{
		
		$ids = array();
		if(!is_array(getParam($param,$key)))
		{
			$param[$key][] = getParam($param,$key);
		}
				
		foreach($param[$key] as $id)
		{
			if(ctype_digit(strval($id)))
			{
				$ids[] = $id;
			}
		}
		
		
		return $ids;
	}
	
	
	/**
	 * 待遇・福利厚生・こだわりポイントを店舗データと企業データでマージする(１件）
	 *
	 */
	public function margeTreatmentBySalonId($salon_id)
	{
		$manager = Management::getInstance();
		
		$salon   = $manager->db_manager->get('salon')->findById($salon_id);
		
		$salon['treatment'] = json_decode($salon['treatment'],true);
		
		$company = $manager->db_manager->get('company')->getTreatment($salon['company_id']);
		
		
		$treat =  $this->margeTreatment($company,$salon);
		
		$manager->db_manager->get('salon')->updateById($salon_id,$treat);
	}
	
	/**
	 * 待遇・福利厚生・こだわりポイントを店舗データと企業データでマージする(対象の企業ＩＤ）
	 *
	 */
	public function margeTreatmentByCompanyId($company_id)
	{
		
		$manager = Management::getInstance();
		
		$company = $manager->db_manager->get('company')->getTreatment($company_id);
		
		if($salons   = $manager->db_manager->get('salon')->findByCompanyId($company_id))
		{
			foreach($salons as $salon)
			{
				
				$salon_id = $salon['id'];
				$salon['treatment'] = json_decode($salon['treatment'],true);
				
				$treat = $this->margeTreatment($company,$salon);
				$manager->db_manager->get('salon')->updateById($salon_id,$treat);
			}
		}
	}
	
	/**
	 * 企業の待遇・福利厚生の配列とサロンデータ内のこだわりポイントの配列をマージ
	 *
	 * @param array $company 企業の福利厚生の配列
	 * @param array $salon   サロンデータ
	 * @return array 待遇・福利厚生・こだわりポイントの配列
	 */
	private function margeTreatment($company,$salon)
	{
		//更新用配列準備
		$treat = $company;
		unset($treat['treatment']);
		
		$temp = array();
		
		if(is_array(getParam($salon,'treat')))
		{
			$temp = getParam($salon,'treat');
		}
		
		//店舗と企業の待遇・福利厚生・こだわりポイント
		if(is_array(getParam($salon,'treat')))
		{
			$temp = array_merge ($temp,$company['treatment']);
		}
		
		
		
		$temp = array_merge (array_unique($temp));
		
		$treat['search_treatment'] = json_encode($temp);
		
		return $treat;
	}
	
	
	
	
	/**
	 * サロンデータに登録する募集職種（雇用形態）のデータ生成
	 *
	 */
	public function createEmploymentColumn($job_salary,$company_id)
	{
		
		$result = array('job_category'=>'[]',
						'job_employment'=>'[]');
		
		$manager = Management::getInstance();
		
		//JSON形式の場合
		if(is_string($job_salary))
		{
			$job_salary = json_decode($job_salary,true);
		}
		
		//募集ＩＤが配列ではない、総数が0の場合は終了
		if(!is_array($job_salary) || count($job_salary) == 0)
		{
			return $result;
		}
		
		
		//ＪＳＯＮ化
		if($res = $manager->db_manager->get('job_salary')->findApplyByIds($job_salary))
		{
			$temp_job_category= array();//職種配列
			$temp_employment  = array();//雇用形態配列
			foreach($res as $val)
			{
				if($company_id != $val['company_id'])
				{
					continue;
				}
				
				$temp_employment[] = $val['job_category_id'].":".$val['employment_status'];
				//同じ職種がない場合
				if(!in_array($val['job_category_id'],$temp_job_category))
				{
					$temp_job_category[] = $val['job_category_id'];
				}
			}
			
			$result['job_category']   = json_encode($temp_job_category);
			$result['job_employment'] = json_encode($temp_employment);
		}
		return $result;
	}
}