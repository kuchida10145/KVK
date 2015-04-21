<?php
/**
 * 検証用関数群（ヘルパーというよりバリデーション用
 *
 */

/**
 * 交通費チェックとテキストがセットになっているか判別
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function commute_set($key,$data)
{
	if(!isset($data['treatment']))
	{
		return true;
	}
	
	if(in_array(2,$data['treatment']) && (!isset($data['commute_cost']) || $data['commute_cost'] == ""))
	{
		return false;
	}
	return true;
}

/**
 * セミナー補助費チェックとテキストがセットになっているか判別
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function seminar_set($key,$data)
{
	if(!isset($data['treatment']))
	{
		return true;
	}
	
	
	if(in_array(3,$data['treatment']) && (!isset($data['seminar']) || $data['seminar'] == ""))
	{
		return false;
	}
	return true;
}

/**
 * 休日：有給＋何日あるかのチェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function holiday_free($key,$data)
{
	if(!isset($data['holiday_type']))
	{
		return true;
	}
	
	if(in_array(4,$data['holiday_type']) === true)
	{
		if(!isset($data[$key]) || $data[$key] == '')
		{
			return false;
		}
	}
	
	return true;
	
}


/**
 * 休日：月の休日
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function holiday_monthly($key,$data)
{
	if(!isset($data['holiday_type']))
	{
		return true;
	}
	
	if(in_array(2,$data['holiday_type']) === true)
	{
		if(!isset($data[$key]) || $data[$key] == '')
		{
			return false;
		}
	}
	return true;
}

/**
 * 休日：週の休日
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function holiday_closed($key,$data)
{
	if(!isset($data['holiday_type']))
	{
		return true;
	}
	
	if(in_array(1,$data['holiday_type']) === true)
	{
		if(!isset($data[$key]) || $data[$key] == '')
		{
			return false;
		}
	}
	return true;
}


/**
 * 勤務時間チェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function work_time($key,$data)
{
	$val = $data[$key];
	
	$sh = getParam($val,'sh');
	$sm = getParam($val,'sm');
	$eh = getParam($val,'eh');
	$em = getParam($val,'em');
	$rm = getParam($val,'rm');
	
	if($sh == "" && $sm == "" && $eh == "" && $em == "" && $rm == "")
	{
		return true;
	}
	
	//数値チェック
	if(!is_numeric($sh) || !is_numeric($sm) || !is_numeric($eh) || !is_numeric($em))
	{
		return false;
	}
	
	
	
	//範囲チェック
	if($sh > 23 || $sh < 0 || $eh > 23 || $eh < 0)
	{
		return false;
	}
	//範囲チェック
	if($sm > 59 || $sm < 0 || $em > 59 || $em < 0)
	{
		return false;
	}
	
	
	return true;
}


/**
 * 募集職種チェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function job_salary_ids($key,$data)
{
	
	$account = $_SESSION['manage']['account'];
	$manager = Management::getInstance(); 
	$temp    = $manager->db_manager->get('job_salary')->findByCompanyId($account['id']);
	
	if(!isset($data[$key]))
	{
		return true;
	}
	
	if(!is_array($data[$key]) || !$temp)
	{
		return false;
	}
	
	if(count($data[$key]) == 0)
	{
		return true;
	}
	
	
	$ids = array();
	foreach($temp as $val)
	{
		$ids[] = $val['id'];
	}
	
	foreach($data[$key] as $val)
	{
		if(in_array($val,$ids) === false)
		{
			return false;
		}
	}
	return true;
}

/**
 * 都道府県チェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function pref_check($key,$data)
{
	if(!isset($data[$key]))
	{
		return true;
	}
	
	
	if(!is_numeric($data[$key]) || ($data[$key] < 1 || ($data[$key] > 47 && $data[$key] != 99)) )
	{
		return false;
	}
	
	return true;
}


/**
 * 市区町村チェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function city_check($key,$data)
{
	if(!isset($data[$key]))
	{
		return true;
	}
	if(!is_numeric($data[$key]) || is_array($data[$key]))
	{
		return false;
	}
	
	
	$manager = Management::getInstance();
	$temp = $manager->db_manager->get('city')->findById($data[$key]);
	
	if(!$temp)
	{
		return false;
	}
	
	
	return true;
}



/**
 * 住所チェック
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function address_check($key,$data)
{
	$manager = Management::getInstance();
	$pref_id = getParam($data,'pref_id');
	$city_id = getParam($data,'city_id');
	$address = getParam($data,'address1');
	
	//都道府県チェック
	if(!is_numeric($pref_id) || $pref_id < 1 || ($pref_id > 47 && $pref_id != 99))
	{
		return false;
	}
	//都市チェック
	if(!is_numeric($city_id) || $pref_id < 1 || !($res = $manager->db_manager->get('city')->findById($city_id)))
	{
		return false;
	}
	//住所
	if($address == '')
	{
		return false;
	}
	
	return true;
}


/**
 * メールアドレス確認チェック
 *
 */
function email_duble($key,$data)
{
	
	if(!isset($data['email']) && !isset($data['chk_email']))
	{
		return true;
	}
	else if(!isset($data['email']) && !isset($data['chk_email']))
	{
		return false;
	}
	
	else if($data['email']!=$data['chk_email'])
	{
		return false;
	}
	
	return true;
}


/**
 * パスワードの確認
 *
 */
function pw_conf($key,$data)
{
	$pw = getParam($data,$key);
	$ch = getParam($data,'new_login_pw_chk');
	
	
	if($pw != $ch)
	{
		return false;
	}
	return true;
}



/**
 * エリアの選択
 *
 * @param string $key 添え字
 * @param string $data データ配列
 * @return boolean 検証結果
 */
function select_area($key,$data)
{
	if(!isset($data[$key]))
	{
		return false;
	}
	
	$area = $data[$key];
	
	if(!isset($area['pref']) || !isset($area['area']))
	{
		return false;
	}
	
	if(getParam($area,'pref') != '' && getParam($area,'area') != '')
	{
		return true;
	}
	else
	{
		return false;
	}
	
	return true;
	
}