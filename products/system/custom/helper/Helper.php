<?php
/**
 * 定数用ヘルパー
 *
 */


/**
 * ステータス配列の取得
 *
 */
function status()
{
	return array(
			'0'=>'一時保存',
			'1'=>'新規申請',
			'2'=>'仮承認',
			'3'=>'変更申請',
			'4'=>'承認',
			'5'=>'一時停止',
			'9'=>'不許可',
			);
}


function company_status()
{
	
}

function request_status($admin=false)
{
	$status = array(
			'0'=>'一時保存',
			'1'=>'新規申請中',
			'2'=>'仮承認',
			'3'=>'変更申請中',
			'4'=>'-',
			'5'=>'一時停止中',
			'9'=>'不許可',
			);
	if($admin== true)
	{
		$status[0] = '-';
	}
	return $status;
}






/**
 * 雇用形態の配列を取得
 *
 */
function employment_status()
{
	return array(
				1=>'正社員',
				2=>'パート',
				3=>'面貸し・業務委託'
			);
}

/**
 * 給与形態の配列を取得
 *
 */
function salary_type()
{
	return array(
			1=>'月給',
			2=>'時給',
			3=>'年俸',
			4=>'報酬',
			);
}



/**
 * 金額単位の配列を取得
 *
 */
function salary_unit()
{
	return array(
		 	 1=>'円～',
		 	 2=>'円',
		 	 3=>'円～＋歩合給',
			 99=>'',
			);
}


/**
 * 血液型
 *
 */
function blood_type()
{
	return array(
				 '1'=>'Ａ型',
				 '2'=>'Ｂ型',
				 '3'=>'Ｏ型',
				 '4'=>'ＡＢ型',
				 );
}


function gift_status()
{
	return array(1=>'内定報告',
				 2=>'内定報告確認中',
				 3=>'内定確定・申請待ち',
				 4=>'内定辞退',
				 5=>'祝い金申請受付',
				 6=>'勤務実態確認中',
				 7=>'祝い金支払い済み',
				 8=>'退社',
				 9=>'不許可',
				 );
}

/** 
 * ステータスによって、trの背景を変更
 *
 */
function status_trbg($status)
{
	
	$class = array(
			'0'=>'class="bggray"',//一時保存
			'1'=>'class="bggreen"',//新規申請
			'2'=>'class="bgyellow"',//仮承認申請
			//'3'=>'class="bggreen"',//変更申請
			'3'=>'class="bgyellow"',//変更申請
			'4'=>'',//承認
			'5'=>'class="bggray"',//一時停止
			'9'=>'class="bgred"',
			);
	if(isset($class[$status]))
	{
		return $class[$status];
	}
	return "";
}

/**
 * サロン用背景色設定
 *
 * @param array $val 配列
 * @return string 背景色class
 */
function salon_trbg($val)
{
	$req_status = $val['request_status'];
	$view   = $val['view_status'];
	
	//状態が不許可で、掲載が停止の場合は、灰色
	if($req_status == 9 && $view == 9)
	{
		return 'class="bgred"';
	}
	return status_trbg($req_status);
}



/**
 * 都道府県取得
 *
 */
function getPref()
{
	return array('1'=>'北海道','2'=>'青森県','3'=>'岩手県','4'=>'宮城県','5'=>'秋田県','6'=>'山形県','7'=>'福島県','8'=>'茨城県','9'=>'栃木県','10'=>'群馬県','11'=>'埼玉県','12'=>'千葉県','13'=>'東京都','14'=>'神奈川県','15'=>'新潟県','16'=>'富山県','17'=>'石川県','18'=>'福井県','19'=>'山梨県','20'=>'長野県','21'=>'岐阜県','22'=>'静岡県','23'=>'愛知県','24'=>'三重県','25'=>'滋賀県','26'=>'京都府','27'=>'大阪府','28'=>'兵庫県','29'=>'奈良県','30'=>'和歌山県','31'=>'鳥取県','32'=>'島根県','33'=>'岡山県','34'=>'広島県','35'=>'山口県','36'=>'徳島県','37'=>'香川県','38'=>'愛媛県','39'=>'高知県','40'=>'福岡県','41'=>'佐賀県','42'=>'長崎県','43'=>'熊本県','44'=>'大分県','45'=>'宮崎県','46'=>'鹿児島県','47'=>'沖縄県','99'=>'海外');
}


/**
 * 口座種類
 *
 */
function bank_type()
{
		return array('1'=>'普通','2'=>'当座');
}


function find_birth_star($birth_day)
{
	$date = date('md',strtotime($birth_day));
	
	if($date >= 120 && $date <= 218){return 1;}
	else if($date >= 219 && $date <= 320){return 2;}
	else if($date >= 321 && $date <= 419){return 3;}
	else if($date >= 420 && $date <= 520){return 4;}
	else if($date >= 521 && $date <= 621){return 5;}
	else if($date >= 622 && $date <= 722){return 6;}
	else if($date >= 723 && $date <= 822){return 7;}
	else if($date >= 823 && $date <= 922){return 8;}
	else if($date >= 923 && $date <= 1023){return 9;}
	else if($date >= 1024 && $date <= 1121){return 10;}
	else if($date >= 1122 && $date <= 1221){return 11;}
	else if($date >= 1222 || ($date >= 101 && $date <= 119)){return 12;}
	return '';
}

function birth_star()
{
	return array( 1 => '水瓶座',
				  2 => '魚座',
				  3 => '牡羊座',
				  4 => '牡牛座',
				  5 => '双子座',
				  6 => '蟹座',
				  7 => '獅子座',
				  8 => '乙女座',
				  9 => '天秤座',
				  10=> '蠍座',
				  11=> '射手座',
				  12=> '山羊座');
}


function selection_status()
{
	return array('1'=>'書類選考中',
				 '2'=>'面接中',
				 '3'=>'採用',
				 '4'=>'不採用',
				 );
}

function entry_type()
{
	return array('1'=>'WEB応募',
				 '2'=>'電話応募',
				 '3'=>'スカウト',
				 );
}



function company_contact_category()
{
	return array(
		'1'=>'機能についてのご質問･ご意見',
		'2'=>'サイトバナー広告掲載について',
		'3'=>'美容室でのプロモーションのご検討',
		'4'=>'その他',
	);
}
function contact_category()
{
	return array(
		'1'=>'ログイン・会員登録について',
		'2'=>'応募について',
		'3'=>'祝い金についてのご質問',
		'4'=>'スタッフ登録について',
		'5'=>'機能についてのご質問・ご意見',
		'6'=>'掲載内容が事実と異なる場合',
		'7'=>'その他',
	);
}

function have_license()
{
	return array('1'=>'卒業見込み',
				 '2'=>'あり',
				 '3'=>'なし',
				 '4'=>'不問');
}


function have_career()
{
	return array(
				 '1'=>'あり',
				 '2'=>'なし',
				 '3'=>'不問');
}


function job_category_staff()
{
	return array(1=>'スタイリスト',
		2=>'アシスタント',
		4=>'カラーリスト',
		5=>'レセプション',
		6=>'アイリスト',
		7=>'ネイリスト',
		8=>'エステティシャン',
		9=>'ヘアメイク',
		10=>'スパニスト',
		11=>'美容部員',
		12=>'ブライダルコーディネーター',
		13=>'理容師',
		99=>'その他');

}
/*================================================
 *
 * マスターデータの取得関連
 *
 *================================================*/
/**
 * 職種の配列を取得
 */
function job_category()
{
	
	static $data = array();
	$data = getMaster('job_category',$data);
	
	return $data;
}

/**
 * 資格の配列を取得
 *
 */
function license($mode = '')
{
	static $data = array();
	$data = getMaster('license',$data);
	//エントリー用の場合
	if($mode == 'entry')
	{
		unset($data[1]);
		$data[0] = '免許なし';
		ksort($data);
	}
	return $data;
}

/**
 * 業種
 *
 */
function business()
{
	static $data = array();
	$data = getMaster('business',$data);
	return $data;
}

/**
 * 職歴
 *
 */
function career()
{
	static $data = array();
	$data = getMaster('career',$data);
	return $data;
}


/**
 * 趣味
 *
 */
function hobby()
{
	static $data = array();
	$data = getMaster('hobby',$data);
	return $data;
}

/**
 * 部活
 *
 */
function club()
{
	static $data = array();
	$data = getMaster('club',$data);
	return $data;
}

function genre()
{
	return array(
		'1'=>'カジュアル・ナチュラル',
		'2'=>'フェミニン・キュート',
		'3'=>'グラマラス・エレガンス',
		'4'=>'クール・モード ',
		'5'=>'オフィス・コンサバ');
}

function work_status()
{
	return array(
				'1'=>'在職中',
				'2'=>'離職中',
				'3'=>'在学中');
}

/**
 * 部活（体育会系・文科系別
 *
 */
function club_select($type)
{
	$manager = Management::getInstance();
	if($temp = $manager->db_manager->get('club')->findByType($type))
	{
		$data = array();
		foreach($temp as $val)
		{
			$data[$val['id']] = $val['name'];
		}
		
		return $data;
	}
	
	return array();
}


/**
 * 部活
 *
 */
function pet()
{
	static $data = array();
	$data = getMaster('pet',$data);
	return $data;
}

/**
 * コンテスト
 *
 */
function contest()
{
	static $data = array();
	$data = getMaster('contest',$data);
	return $data;
}

/**
 * マスターデータの取得（共通処理）
 *
 * @param string $table テーブル
 * @return array
 */
function getMaster($table,$data)
{
	
	if(count($data) == 0)
	{
		$manager = Management::getInstance();
		$res = $manager->db_manager->get($table)->getAll();
		foreach($res as $val)
		{
			$data[$val['id']] = $val['name'];
		}
		$data['99'] = 'その他';
	}
	
	return $data;
}


/**
 * フリー質問の取得
 *
 */
function free_question()
{
	static $data = array();
	if(count($data) == 0)
	{
		$manager = Management::getInstance();
		$res = $manager->db_manager->get('choose')->findById(2);
		$data = json_decode($res['questions'],true);
	}
	
	return $data;
}


/**
 * ２択質問の取得
 *
 */
function choose_question()
{
	static $data = array();
	if(count($data) == 0)
	{
		$manager = Management::getInstance();
		$res = $manager->db_manager->get('choose')->findById(1);
		$data = json_decode($res['questions'],true);
	}
	
	return $data;
}



/**
 * 最寄駅取得
 *
 */
function parse_station($str)
{
	$data = json_decode($str,true);
	if(!isset($data[0]))
	{
		return "";
	}
	$station = '';
	$val = $data[0];
	
	
	$manager = Management::getInstance();
		
	foreach($data as $val)
	{
		
		if(!($res = $manager->db_manager->get('line_code')->findStationById(getParam($val,'station'))))
		{
			return '';
		}
		
		if(getParam($val,'walk') != "")
		{
			$val['walk'] = '徒歩'.$val['walk']."分以内";
		}
		
		
		return $res['name']."駅:".getParam($val,'walk')."&nbsp;";
	}
	
	return '';
	//return $station;
}


/**
 * サロン一覧用画像を取得
 *
 *
 */
function parse_first_salon_image($str)
{
	$data = json_decode($str,true);
	$no_image = ROOT_URL.'common_pc/img/noimg02.jpg';
	if(!isset($data[0]))
	{
		//No image のURLを表示
		return $no_image;
	}
	
	$val = $data[0];
	
	if(getParam($val,'img') == '')
	{
		//No image のURLを表示
		return $no_image;
	}
	
	return SALON_UPLOAD_URL.'thum/370_'.getParam($val,'img');
}


function parse_face_image($str)
{
	if($str == '')
	{
		return  ROOT_URL.'common_pc/img/noimg01.jpg';
	}
	
	return USER_UPLOAD_URL.'thum/200_'.$str;
}


function parse_owner_image($str)
{
	if($str == '')
	{
		return  ROOT_URL.'common_pc/img/noimg01.jpg';
	}
	
	return COMP_UPLOAD_URL.'thum/200_'.$str;
}



/**
 * 注目のワードのリンクを生成
 *
 * @return string
 */
function getKeywordLinks()
{
	$manager = Management::getInstance();
	
	$res = $manager->db_manager->get('keywords')->getKeywordList();
	$html = array();
	foreach($res as $val)
	{
		$val = escapeHtml($val);
		$html[] = '<a href="/salon/search?keyword='.urlencode($val['keyword']).'">'.$val['keyword'].'</a>';
	}
	
	return implode('　',$html);
}

/**
 * 人気のエリアのリンクを生成
 *
 * @return string
 */
function getPopularAreaList()
{
	$manager = Management::getInstance();
	$html = array();
	
	
	if($res = $manager->db_manager->get('area_city')->getPopularAreaList())
	{
		foreach($res as $val)
		{
			$val = escapeHtml($val);
			$html[] = '<a href="/salon/search?area_id[]='.$val['id'].'">'.$val['name'].'</a>';
		}
	}
	
	return implode('　',$html);
}


/**
 * 募集職種のアイコンを取得
 *
 * @param multi $ids 職種ＩＤもしくは職種ＩＤの配列
 * @return string
 */
function getJobCategoryIcons($ids)
{
	if(!is_array($ids))
	{
		return "";
	}
	
	$job_category = job_category();
	$html = array();
	foreach($ids as $id)
	{
		if(isset($job_category[$id]))
		{
			$html[] = '<span>'.$job_category[$id].'</span>';
		}
	}
	return implode('',$html);
}


/**
 * 雇用形態のアイコンを取得
 *
 * @param multi $ids 職種ＩＤもしくは職種ＩＤの配列
 * @return string
 */
function getEmploymentIcons($ids)
{
	if(!is_array($ids))
	{
		return "";
	}
	
	$employment = employment_status();
	$html = array();
	foreach($ids as $id)
	{
		if(isset($employment[$id]))
		{
			$html[] = '<span>'.$employment[$id].'</span>';
		}
	}
	return implode('',$html);
}


/**
 *　地域ごとの求人リンク取得（関連したリンク）
 *
 * @param multi $ids 都市ＩＤもしくは都市ＩＤの配列
 * @return string
 */
function getRelationCityList($ids,$target = 'salon')
{
	
	if(!is_array($ids) && !is_numeric($ids))
	{
		return "";
	}
	
	if(!is_array($ids) && is_numeric($ids))
	{
		$ids = array($ids);
	}
	$manager = Management::getInstance();
	
	if(!($res = $manager->db_manager->get('city')->findByIds($ids)))
	{
		return '';
	}
	$html = array();
	foreach($res as $val)
	{
		$val = escapeHtml($val);
		$html[] = '<a href="/'.$target.'/search?pref_id='.$val['pref_id'].'&city_id[]='.$val['id'].'">'.$val['pref_name'].$val['city_name'].'の求人</a>';
	}
	return implode('　',$html);
	
}


/**
 * 募集職種リンク取得（関連したリンク）
 *
 * @param multi $ids 職種ＩＤもしくは職種ＩＤの配列
 * @return string
 */
function getJobCategoryList($ids)
{
	if(!is_array($ids) && !is_numeric($ids))
	{
		return "";
	}
	
	if(!is_array($ids) && is_numeric($ids))
	{
		$ids = array($ids);
	}
	
	$job_category = job_category();
	$html = array();
	foreach($ids as $id)
	{
		if(isset($job_category[$id]))
		{
			$html[] = '<a href="/salon/search?job_category='.$id.'">'.$job_category[$id].'の求人</a>';
		}
	}
	return implode('　',$html);
}


/**
 * 出身地のリンク（関連したリンク）
 *
 * @param int $id 都道府県ID
 * @return string
 */
function getFromPrefLink($id)
{
	$pref = getPref();
	
	
	if(!isset($pref[$id]))
	{
		return '';
	}
	
	return '<a href="/staff/search?staff_pref_id='.$id.'">'.$pref[$id].'</a>';
}


/**
 * 地域×職種（関連したリンク）
 *
 * @param int $id 都道府県ID
 * @return string
 */
function getCityJobLink($city,$job)
{
	$job_category = job_category();
	
	$manager = Management::getInstance();
	
	
	
	if(!isset($job_category[$job]))
	{
		return '';
	}
	
	if(!($res = $manager->db_manager->get('city')->findById($city)))
	{
		return '';
	}
	
	return '<a href="/staff/search?pref_id='.$res['pref_id'].'&city_id[]='.$res['id'].'&job_category='.$job.'">'.$res['pref_name'].$res['city_name'].'で勤務中の'.$job_category[$job].'</a>';
}


/**
 * 特長リスト
 *
 *
 */
function hit_feature($param)
{

	if(!is_array(getParam($param,'treatment')))
	{
		return array();
	}
	$res = array();
	if(in_array(1,getParam($param,'treatment')) == true){$res[] = '賞与('.getParam($param,'bonus_year').'年度実績有)';}
	if(in_array(2,getParam($param,'treatment')) == true){$res[] = '交通費('.getParam($param,'commute_cost').')';}
	if(in_array(3,getParam($param,'treatment')) == true){$res[] = 'セミナー補助('.getParam($param,'seminar').')';}
	if(in_array(22,getParam($param,'treatment')) == true){$res[] = '家族手当('.getParam($param,'family_cost').')';}
	if(in_array(23,getParam($param,'treatment')) == true){$res[] = '住宅手当('.getParam($param,'house_cost').')';}
	
	if(in_array(4,getParam($param,'treatment')) == true){$res[] = '寮・社宅あり';}
	if(in_array(5,getParam($param,'treatment')) == true){$res[] = '社会保険(厚生年金、雇用保険、健康保険、労災）';}
	else
	{
		$temp = array();
		
		if(in_array(6,getParam($param,'treatment')) == true){$temp[] = '厚生年金';}
		if(in_array(7,getParam($param,'treatment')) == true){$temp[] = '雇用保険';}
		if(in_array(8,getParam($param,'treatment')) == true){$temp[] = '健康保険';}
		if(in_array(9,getParam($param,'treatment')) == true){$temp[] = '労災';}
		
		if(count($temp) > 0)
		{
			$res[] = '社会保険('.implode("、",$temp).')';
		}
	}
	if(in_array(10,getParam($param,'treatment')) == true){$res[] = '産休・育児制度あり';}
	if(in_array(11,getParam($param,'treatment')) == true){$res[] = '完全週休２日';}
	if(in_array(12,getParam($param,'treatment')) == true){$res[] = '日曜定休';}
	if(in_array(13,getParam($param,'treatment')) == true){$res[] = '海外店舗あり';}
	if(in_array(14,getParam($param,'treatment')) == true){$res[] = 'ブランクがある方OK';}
	if(in_array(15,getParam($param,'treatment')) == true){$res[] = '美容通信生積極採用';}
	if(in_array(16,getParam($param,'treatment')) == true){$res[] = 'シャンプー施術なし';}
	if(in_array(17,getParam($param,'treatment')) == true){$res[] = '大型サロン（11店舗以上）';}
	if(in_array(18,getParam($param,'treatment')) == true){$res[] = '託児所完備';}
	if(in_array(19,getParam($param,'treatment')) == true){$res[] = 'キッズ専門サロン';}
	if(in_array(20,getParam($param,'treatment')) == true){$res[] = 'バックシャンプー';}
	
	
	
	if(in_array(24,getParam($param,'treatment')) == true){$res[] = '社員旅行('.getParam($param,'company_trip').')';}
	if(in_array(25,getParam($param,'treatment')) == true){$res[] = '新卒学生採用中';}
	
	//オープニングスタッフ
	if(getParam($param,'opening_staff') == 1)
	{
		$res[] = 'オープニングスタッフ募集（オープン予定'.date('Y年m月',strtotime(getParam($param,'opening_date'))).")";
	}
	
	
	if(in_array(21,getParam($param,'treatment')) == true){
		if(getParam($param,'treatment_other') == "")
		{
			if(array_key_exists('other',getParam($param,'treatment')) && $param['treatment']['other'] != "")
			{
				$res[] = $param['treatment']['other'];
			}
			else
			{
				$res[] = 'その他';
			}
		}
		else
		{
			$res[] = getParam($param,'treatment_other');
		}
	}
	
	
	return $res;

	
}
/**
 * 募集職種用データ配列
 *
 */
function create_job_treatment_txt($param)
{
	$res = array();
	if(in_array(1,getParam($param,'job_treatment'))){ $res[] = '●歩合給（'.getParam($param,'commition_txt').'）';}
	if(in_array(2,getParam($param,'job_treatment'))){ $res[] = '●技術・役職手当（'.getParam($param,'tec_txt').'）';}
	if(in_array(3,getParam($param,'job_treatment'))){ $res[] = '●店販手当（'.getParam($param,'store_txt').'）';}
	
	return $res;
}



/**
 * 休日休暇用データ配列
 *
 */
function create_holiday_txt($data)
{
	$holiday = array();
	
	if(getParam($data,'holiday_other') != "")
	{
		$holiday[] = getParam($data,'holiday_other');
	}
	if(in_array(1,getParam($data,'holiday_type')) !== false){ $holiday[] = '毎週'.getParam($data,'holiday_closed').'曜日定休'; }
	if(in_array(2,getParam($data,'holiday_type')) !== false){ $holiday[] = '月'.getParam($data,'holiday_monthly').'日休み'; }
	if(in_array(3,getParam($data,'holiday_type')) !== false){ $holiday[] = '夏季・冬季休暇'; }
	if(in_array(4,getParam($data,'holiday_type')) !== false){ $holiday[] = '有給('.getParam($data,'holiday_free').'日)'; }
	//5は欠番扱い
	if(in_array(5,getParam($data,'holiday_type')) !== false){}
	if(in_array(6,getParam($data,'holiday_type')) !== false){ $holiday[] = '慶弔休暇'; }
	
	return $holiday;

}


/**
 * 応募表示用データ作成
 *
 */
function create_display_entry($data)
{
	
	
	//免許
	$license = json_decode($data['license'],true);
	$temp_license = array();
	if(!is_array($license))
	{
		$license = array();
	}
	foreach($license as $key => $val)
	{
		if($val != 99 || getParam($data,'license_other') == '')
		{
			$temp_license[$key] = getParam(license('entry'),$val);
		}
	}
	$data['license_text'] = implode('/',$temp_license);
	if(getParam($data,'license_other') !='')
	{
		$data['license_text'].="/".getParam($data,'license_other');
	}
	
	
	
	//職歴
	$data['job_career'] = json_decode($data['job_career'],true);
	
	//開始日
	if($data['company_start'] != "")
	{
		list($data['start_year'],$data['start_month'],$data['start_day']) = explode('-',$data['company_start']);
	}
	
	return $data;
}


//
function create_mail_data($mail,$param=array())
{
	foreach($param as $key=> $val)
	{
		$mail['subject'] = str_replace("##{$key}##",$val,$mail['subject']);
		$mail['body'] = str_replace("##{$key}##",$val,$mail['body']);
	}
	
	return $mail;
}


/**
 * お知らせのタイトルタグの生成
 *
 * @param array お知らせ情報のデータ配列
 * @return string タイトルタグ
 */
function create_info_title_tag($param)
{
	
	$title = getParam($param,'title');
	
	//強調フラグが１なら赤字強調
	if(getParam($param,'special') == 1)
	{
		$title = '<span style="color:#ff0000">'.$title.'</span>';
	}
	return $title;
}


/**
 *
 *
 */
function sort_column($title,$column_name)
{
	$get=$_GET;
	$asc_val  = $column_name.':asc';
	$desc_val = $column_name.':desc';
	
	//デフォルトで昇順をセット
	$get['sort'] = $asc_val;
	
	//ページ番号がある場合葉取り除き
	if(getParam($get,'page')!='')
	{
		unset($get['page']);
	}
	//パスインフォ取り除き
	unset($get['pathinfo']);
	
	//項目が利用されている場合
	if(getGet('sort')==$asc_val || getGet('sort')==$desc_val)
	{
		//昇順の場合降順へ
		if(getGet('sort')==$asc_val)
		{
			$title = "▼".$title;
			$get['sort'] = $desc_val;
		}
		//降順の場合昇順へ
		else
		{
			$title = "△".$title;
			$get['sort'] = $asc_val;
		}
	}
	
	$get_query = http_build_query($get);
	
	$temp = explode('?',$_SERVER['REQUEST_URI']);
	
	$url = $temp[0]."?".$get_query;
	
	return '<a href="'.$url.'">'.$title.'</a>';
	
}




/**
 * マッチサロン表示用データ生成
 *
 * @param array サロン配列
 * @return 
 */
function create_matich_salon_list($salon)
{
	$manager = Management::getInstance();
	foreach($salon as $key => $data)
	{
		$data = escapeHtmlSalon($data);
		//
		if($data['job_salary_ids'] != '')
		{
			$job_salary_ids = json_decode($data['job_salary_ids'],true);
			$josb_salary = $manager->db_manager->get('job_salary')->findByIds($job_salary_ids);
			$val = getSalaryData($josb_salary);
			$data['job_category'] = implode('',getParam($val,'job_category'));
			$data['job_salary']   = implode('<br />',getParam($val,'job_salary'));
			$data['employment']   = implode('/',getParam($val,'employment'));
		}
			
		//スタッフ一覧
		$data['staff_left_cnt'] = 0;
		if($data['staff'] = $manager->db_manager->get('staff')->getCoworker($data['id'],0,5))
		{
			$data['staff_left_cnt']= $manager->db_manager->get('staff')->getSalonStaffCnt($data['id'])-count($data['staff']);
		}
			
		//メッセージ
		$data['staff_message'] = '';
		if($staff_message = $manager->db_manager->get('staff')->getStaffMessage($data['id']))
		{
			$data['staff_message'] = $staff_message['message'];
		}
		
		
		
		$salon[$key]   = $data;
	}
	return $salon;
}

function getSalaryData($param =array())
{
	$job_category      = job_category();
	$employment_status = employment_status();
	$salary_type = salary_type();
	$salary_unit = salary_unit();
	//募集職種・給与
	$temp_job_category = array();
	$temp_job_salary   = array();
	$temp_employment   = array();
	$temp_job_category_id= array();
	
		
	if(count($param) > 0)
	{
		foreach($param as $salary)
		{
			$salary = escapeHtmlJob_salary($salary);
			
			$salary['job_category'] = $job_category[$salary['job_category_id']];
			$salary['salary_type']  = $salary_type[$salary['salary_type']];
			$salary['salary_unit']  = $salary_unit[$salary['salary_unit']];
			$salary['employment']   = $employment_status[$salary['employment_status']];
			
			//その他の場合
			if($salary['job_category_id'] == 99)
			{
				$salary['job_category'] = $salary['job_category_other'];
			}
					
			//職種
			if(!in_array('<span>'.$salary['job_category'].'</span>',$temp_job_category))
			{
				$temp_job_category[] ='<span>'.$salary['job_category'].'</span>';
			}
			
			//職種ID
			if(!in_array($salary['job_category_id'],$temp_job_category_id))
			{
				$temp_job_category_id[] = $salary['job_category_id'];
			}
			
			//給与
			$salary_txt = "<strong>".$salary['job_category']."</strong>"."【".$salary['salary_type']."】".$salary['salary_price'].$salary['salary_unit'];
			if(!in_array($salary_txt,$temp_job_salary))
			{
				$temp_job_salary[] =  $salary_txt;
			}
			
			//雇用形態
			if(!in_array($salary['employment'],$temp_employment))
			{
				$temp_employment[] = $salary['employment'];
			}
		}
	}
	
	$val['job_category'] = $temp_job_category;
	$val['job_category_id'] = $temp_job_category_id;
	$val['job_salary']   = $temp_job_salary;
	$val['employment']   = $temp_employment;
	
	return $val;
}


/**
 * 気になるボタン生成
 *
 */
function create_favorite_btn($salon_id,$account=NULL,$device='pc')
{
	$salons = explode(':',getParam($_COOKIE,'favorite_store',''));
	
	$add_img = '/common_pc/img/salon/btn_favorite.png';
	$del_img = '/common_pc/img/salon/btn_favorite_off.png';
	
	//画像Path
	if($device != 'pc')
	{
		$add_img = '/common_sp/img/salon/btn_favorite.png';
		$del_img = '/common_sp/img/salon/btn_favorite_off.png';
	}
	
	//ログインしていない場合
	if($account==NULL)
	{
		return '<a href="/mypage/login?back=/salon/'.$salon_id.'?fv_add=true" data-id="'.$salon_id.'"><img src="'.$add_img.'" /></a>&nbsp;';
	}
	
	foreach($salons as $favorite_id)
	{
		if($salon_id == $favorite_id)
		{
			return '<a href="javascript:void(0);"  class="rem_fav_btn" data-id="'.$salon_id.'"><img src="'.$del_img.'" /></a>&nbsp;';
		}
	}
	
	return '<a href="javascript:void(0);"  class="add_fav_btn" data-id="'.$salon_id.'"><img src="'.$add_img.'" /></a>&nbsp;';
}


/**
 * ハッシュ生成
 *
 */
function create_hash($data, $algo = 'CRC32')
{
    return strtr(rtrim(base64_encode(pack('H*', sprintf('%u', $algo($data)))), '='), '+/', '-_');
}