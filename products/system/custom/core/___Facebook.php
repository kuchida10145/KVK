<?php
require(dirname(__FILE__).'/../../plugin/facebook/autoload.php');
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
class Facebook{
	
	private $helper = NULL;
	
	
	
	public function getHelper()
	{
		if($this->helper == NULL)
		{
			FacebookSession::setDefaultApplication(F_APP_ID,F_SECRET);
			$this->helper = new FacebookRedirectLoginHelper(HTTP_HOST.ROOT_URL.'register');
		}
		return $this->helper;
	}


	public function getFriends($token = '')
	{
		$helper = $this->getHelper();
		$data = array();
		
		
		if($token == "" || !($session = new FacebookSession($token)))
		{
			if(!($session = $helper->getSessionFromRedirect()))
			{
				return NULL;
			}
			$_SESSION['facebook_token'] = $session->getToken();
		}
		
		try{
			$me = (new FacebookRequest($session, 'GET', '/me/friends?locale=ja_JP'))->execute()->getGraphObject()->asArray();
			if(count($me['data']) > 0)
			{
				foreach($me['data'] as $val)
				{
					$temp = array();
					$temp['id'] = $val->id;
					$data[] = $temp;
				}
			}
			
			
		}catch (FacebookRequestException $e) {
			echo $e->getMessage();
			exit;

		}catch (\Exception $e) {
			echo $e->getMessage();
			exit;
		}
		
		return $data;
	}

	/**
	 * プロフィール取得
	 *
	 */
	public function getUserProfile($token="")
	{
		
		
		$helper = $this->getHelper();
		$data = array();
		
		
		if($token == "" || !($session = new FacebookSession($token)))
		{
			print_r($helper);
			
			if(!($session = $helper->getSessionFromRedirect()))
			{
				return NULL;
			}
			$_SESSION['facebook_token'] = $session->getToken();
		}
		
		
		try{
			$me = (new FacebookRequest($session, 'GET', '/me?locale=ja_JP'))->execute()->getGraphObject(GraphUser::className());
			
			$birthday = $me->getProperty('birthday');
			$data['facebook_id'] = $me->getProperty('id');
			$data['first_name']  = $me->getProperty('first_name');
			$data['family_name'] = $me->getProperty('last_name');
			$data['birth_year']  = date('Y',strtotime($birthday));
			$data['birth_month'] = date('m',strtotime($birthday));
			$data['birth_date']  = date('d',strtotime($birthday));
			$data['email']       = $me->getProperty('email');
			$data['gender']      = $me->getProperty('gender');
			if($me->getProperty('gender') == '男性')
			{
				$data['gender'] = 1;
			}
			else if($me->getProperty('gender') == '女性')
			{
				$data['gender'] = 2;
			}
		}catch (FacebookRequestException $e) {
			echo $e->getMessage();
			exit;

		}catch (\Exception $e) {
			echo $e->getMessage();
			exit;
		}
		
		return $data;
	}
	
	
	/**
	 * 顔写真取得
	 *
	 */
	public function getPicture($token="")
	{
		$helper = $this->getHelper();
		$photo = "";
		if($token == "" || !($session = new FacebookSession($token)))
		{
			if(!($session = $helper->getSessionFromRedirect()))
			{
				return NULL;
			}
			$_SESSION['facebook_token'] = $session->getToken();
		}
		
		try{
			$me = (new FacebookRequest($session, 'GET', '/me/picture?type=square&width=400&locale=ja_JP&redirect=false'))->execute()->getGraphObject()->asArray();
			$photo =$me['url'];
			
		}catch (FacebookRequestException $e) {
			//echo $e->getMessage();
			exit;

		}catch (\Exception $e) {
		//	echo $e->getMessage();
			exit;
		}
		
		return $photo;
	}
	
	
	
	/**
	 * ログイン用ＵＲＬ取得
	 *
	 */
	public function login()
	{
		
		$helper = $this->getHelper();
		$permissions = array(
				'email',
				'user_location',
				'user_birthday',
				'user_friends',
				);
		return $helper->getLoginUrl($permissions);
	}




}