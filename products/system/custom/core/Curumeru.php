<?php
/**
 * クルメル用プログラム
 *
 */
class Curumeru
{
	
	public $password = CURUMERU_PW;
	public $charset  = 1;//文字コード utf-8:1 SJIS:2 EUC:3
	public $report   = 0;//一括処理終了後にレポートメールを送信するかどうか。 0 : 送信しない 1 : 送信する 2 : エラー時のみ送信する。
	public $return_format = 'xml';
	public $url      = CURUMERU_URL;
	public $csv_dir  = CURUMERU_CSV_DIR;
	
	
	
	/**
	 *メール配信予約
	 *
	 * @param Array $mail_data メールデータ
	 * @param String $csv_name CSVファイル名
	 * @return Array 結果
	 */
	public function reserveMail($mail_data,$csv_name)
	{
		$param['transport_password'] = $this->password;//パスワード
		$param['charset']            = "1";//文字コード utf-8:1 SJIS:2 EUC:3
		$param['return_format']      = $this->return_format;//リターンフォーマット
		$param['list_name']          = $mail_data['subject'];//LIST 名前 ※使用できない文字『\ / : * ? “ < > |』 50文字以内
		$param['from_address']       = $mail_data['from_mail'];//メールのFromに使用するメールアドレス
		$param['from_name']          = $mail_data['from_name'];//メールの差出人名
		$param['subject']            = $mail_data['subject'];//件名
		$param['report_option']      = $this->report;
		
		//予約か即時配信か
		if($mail_data['type'] == 0)
		{
			$param['schedule_type']      = '1';//メールの予約種別 1 : 即時配信 2 : 予約配信
		}
		else
		{
			$reserve_time = str_replace('-','/',$mail_data['reserve_date'])." ".$mail_data['hour'].":".$mail_data['min'];
			$param['schedule_type']      = '2';//メールの予約種別 1 : 即時配信 2 : 予約配信
			$param['schedule_date']      = $reserve_time;//メールの配信を行う日時（yyyy/mm/dd hh24:mi） ※過去日時は指定できない。 ※予約配信の場合のみ必須。
		}
		
		//テキストメール
		if($mail_data['mail_type'] == 1)
		{
			$param['text_part'] = $mail_data['body'];
		}
		//htmlメール
		else
		{
			$param['html_part'] = $mail_data['body'];
		}

		
		//会員情報の入ったCSVファイル設定
		$file = $this->csv_dir.'/'.$csv_name;    //アップロードするテキストファイル 
		$files = array(
			'csvfile' => $file
		);
		
		$url = $this->url.'CreateNewMail';
		
		$response = $this->httpPost($url, $param, $files);
		
		return $response;
	}
	
	
	
	
	/**
	 * メール配信一覧取得
	 *
	 */
	public function getMailList($status='',$from='',$to='')
	{
		$param['transport_password'] = $this->password;//パスワード
		$param['charset']            = $this->charset;
		$param['return_format']      = $this->return_format;
		
		//ステータス
		if($status != '')
		{
			$param['mail_status'] = $status;
		}
		
		//開始
		if($from != '')
		{
			$param['from_date'] = $from;
		}
		//終了
		if($to != '')
		{
			$param['to_date'] = $to;
		}
		
		$url = $this->url.'GetMailList';
		
		$response = $this->httpPost($url,$param);
		
		return $response;
	}
	
	
	/**
	 * メールの詳細情報を取得
	 *
	 * @param Int $mail_id メールID
	 * @return xml
	 */
	public function getMailDetail($mail_id)
	{
		$param['transport_password'] = $this->password;//パスワード
		$param['charset']            = $this->charset;
		$param['return_format']      = $this->return_format;
		$param['mail_id']          = (int)$mail_id;
		$url = $this->url.'GetMailInfo';
		$response = $this->httpPost($url,$param);
		
		return $response;
	}
	
	
	/**
	 * メールのキャンセル
	 *
	 */
	public function cancelMail($mail_id)
	{
		$param['transport_password'] = $this->password;//パスワード
		$param['charset']            = $this->charset;
		$param['return_format']      = $this->return_format;
		$param['mail_id']          = (int)$mail_id;
		$url = $this->url.'CancelMailSchedule';
		$response = $this->httpPost($url,$param);
		
		return $response;
	}
	
	
	/**
	 * エラーメール取得
	 *
	 */
	public function getErrorMail()
	{
		$param['transport_password'] = $this->password;//パスワード
		$param['charset']            = $this->charset;
		$param['return_format']      = $this->return_format;
		$url = $this->url.'GetErrorAddressList';
		$response = $this->httpPost($url,$param);
		
		return $response;
	}
	
	
	
	/**
	 * HTTP送信
	 *
	 * @param String $url 送信先URL
	 * @param Array $params パラメータ
	 * @param Array $files 送信するファイル
	 * @return Array 
	 */
	public function httpPost($url,$params,$files=array())
	{
		$isMultipart = (count($files)) ? true : false;

		//ファイル送信を行う場合
		if ($isMultipart)
		{

			//ファイルアップロードを伴う場合、multipartで送信
	
			$boundary = '---------------------------'.time();
			$contentType = "Content-Type: multipart/form-data; boundary=" . $boundary;
			$data = '';
			foreach($params as $key => $value)
			{
				$data .= "--$boundary" . CRLF;
				$data .= 'Content-Disposition: form-data; name="' . $key .'"'. CRLF . CRLF;
				$data .= $value . CRLF;
			}
	
			foreach($files as $key => $file)
			{
				$data .= "--$boundary" . CRLF;
				$data .= sprintf('Content-Disposition: form-data; name="%s"; filename="%s"%s', $key, basename($file), CRLF);
				$data .= 'Content-Type: text/csv'. CRLF;
				$data .= "Content-Transfer-Encoding: binary" . CRLF.CRLF;
				$data .= file_get_contents($file) . CRLF;
			}
	
			$data .= "--$boundary--" . CRLF;

    	}
		//パラメータのみの送信の場合
		else
		{
        	$contentType = 'Content-Type: application/x-www-form-urlencoded';
			$data = http_build_query($params);

   		}
		
		//ヘッダ
		$headers = array(
						 $contentType,
						 'Content-Length: '.strlen($data)
						);
		
		//オプション
		$options = array(
						 'http' => array(
										 'method'  => 'POST',
										 'content' => $data,
										 'header'  => implode(CRLF, $headers)
										 )
						 );
		//実行
		$contents = file_get_contents($url, false, stream_context_create($options));
		
		return $contents;
	}
	
	
	
	
	
}