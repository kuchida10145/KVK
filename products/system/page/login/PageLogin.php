<?php
	include(dirname(__FILE__) . '/../../../Page.php');
	class PageLogin extends Page{
		function __construct() {
			parent::__construct();
		}

	/**
	 * パスワードチェック
	 * @param  $userName	ユーザ名
	 * @param  $passWord	パスワード
	 * @return $result		チェック結果
	 */
	public function passWordCheck($userName, $passWord) {
		$deleteFlg = "";
		$where = "";
		$table = TABLE_NAME_USER;
		$result = true;
		$checkRow = array($userName, $passWord);
		$errorMessage = array();
		$errorLineCount = 0;

		// 入力チェック
		if(empty($userName)) {
			$errorMessage[] = "ユーザ名が未入力です。<br>";
			$result =  false;
		}

		if(empty($passWord)) {
			$errorMessage[] = "パスワードが未入力です。<br>";
			$result =  false;
		}

		if(!$result) {
			$this->setErrorMessage($errorMessage);
			return $result;
		}

		// ユーザチェック
		$where = COLUMN_NAME_USER_NAME." = '".$userName."'";
		$result = $this->manager->db_manager->get($table)->checkData($where);

		if(!$result) {
			$this->{KEY_ERROR_MESSAGE} = "ユーザが存在しません。<br>";
			return $result;
		}

		// パスワードチェック
		$where = COLUMN_NAME_USER_NAME." = '".$userName."' AND ".COLUMN_NAME_PASS_WORD." = '".$passWord."'";
		$result = $this->manager->db_manager->get($table)->checkData($where);

		if(!$result) {
			$this->{KEY_ERROR_MESSAGE} = "パスワードが一致しません。<br>";
			return $result;
		}

		return $result;
	}

	/**
	 * エラーメッセージ取得
	 * @return	$message	エラーメッセージ
	 */
	public function getErrorMessage() {
		return $this->{KEY_ERROR_MESSAGE};
	}

	/**
	 * エラーメッセージセット
	 * @param array $messageArray
	 */
	protected function setErrorMessage($messageArray) {
		$messageVal = "";

		foreach ($messageArray as $row){
			$messageVal = $messageVal.$row;
		}
		$this->{KEY_ERROR_MESSAGE} = $messageVal;
	}
}
