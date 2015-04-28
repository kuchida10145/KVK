<?php
/**
 * サロン
 *
 */
class Gift
{
	
	/**
	 * ギフト種別を取得
	 *
	 */
	public function getType($entry)
	{
		$license_flg = 3;
		$career_flg  = 2;
		$student_flg = 0;
		$manager = Management::getInstance();
		
		//免許
		$license = json_decode(getParam($entry,'license'),true);
		if(in_array(2,$license))
		{
			$license_flg = 2;//あり
		}
		
		//経験
		if(getParam($entry,'job_category_ex') == 1)
		{
			$career_flg = 1;//あり
		}
		
		
		//卒業見込み
		if(getParam($entry,'work_status') == 3)
		{
			$student_flg = 1;
		}
		
		
		$res = $manager->db_manager->get('giftcost')->findByJobCategory(getParam($entry,'job_category'));
		
		
		//キャリアチェック
		$gift_type = NULL;
		
		
		foreach($res as $val)
		{
			//免許・経験ヒット
			if($this->license_check($val['license'],$license_flg,$student_flg) && $this->career_check($val['career'],$career_flg,$student_flg))
			{
				$gift_type = $val;
				
				break;
			}
		}
		
		if($gift_type == NULL)
		{
			$data = array('giftcost_id' => 0 ,'price_1month'=>0,'price_3month'=>0,'price_6month'=>0);
		}
		else
		{
			$data = array('giftcost_id' => $gift_type['id'],'price_1month'=>$gift_type['price_1month'],'price_3month'=>$gift_type['price_3month'],'price_6month'=>$gift_type['price_6month']);
		}
		
		
		return $data;
	}
	
	/**
	 * 免許を取得
	 *
	 */
	private function license_check($license,$flg,$student)
	{
		//不問の場合
		if($license == '4'){ return true;}
		
		//ヒットした場合の場合
		if($license == $flg){ return true;}
		
		//卒業見込みで、生徒フラグがたっている場合
		if($license == 1 && $student == 1)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * 経験をチェック
	 *
	 */
	private function career_check($career,$flg,$student)
	{
		//不問の場合
		if($career == '3'){ return true;}
		
		//ヒットした場合の場合
		if($career == $flg){ return true;}
		
		//卒業見込みで、生徒フラグがたっている場合
		if($career == 2 && $student == 1)
		{
			return true;
		}
		
		return false;
	}
}