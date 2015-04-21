<?php
/**
* Utilヘルパ
*
* @package Sixpence
* @author Ippei.Takahashi <takahashi@6web.co.jp>
* @since PHP 4.3.9
* @version 1.1.0
*/
/**
 * リンクパラメータ作成
 *
 * @param array $params パラメータが格納された連想配列
 * @return string パラメータ文字列
 * @version 1.1.0
 */
function createLinkParam($params){
	$tmp_params = array();
	$param = '';
	
	if(is_array($params))
	{
		foreach($params as $key => $val)
		{
			if(is_array($val))
			{
				foreach($val as $i_key => $i_val)
				{
					$tmp_params[] = $key.'[]='.$i_val;
				}
				
			}
			else
			{
				$tmp_params[] = $key.'='.$val;
			}
		}
		$param = implode('&',$tmp_params);
		
		if($param != '')
		{
			$param = '?'.$param;
		}
	}
	return $param;
}
/**
 * データのHTMLエスケープ
 *
 * @param array $data GET,POSTデータ
 * @param string $type 渡されたリクエストのタイプ
 * @return array エスケープされたデータ
 * @version 1.1.0
 */
function escapeHtml($data,$type= 'post')
{
	
	if(is_array($data)){
		$result = array();
		
		
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				$result[$key] = escapeHtml($val,$type);
			}else
			{
				//GETリクエストの場合はURLデコード
				if($type == 'get')
				{
					$val = urldecode($val);
				}
				$result[$key] = htmlspecialchars($val,ENT_QUOTES);
			}
		}
	}
	else
	{
		if($type == 'get')
		{
			$val = urldecode($data);
		}
		$result = htmlspecialchars($data,ENT_QUOTES);
	}
	return $result;
}


/**
 * HTMLエスケープの取り除き
 *
 * @param array $data GET,POSTデータ
 * @param string $type 渡されたリクエストのタイプ
 * @return array エスケープされたデータ
 * @version 1.1.0
 */
function decodeHtml($data,$type= 'post')
{
	
	if(is_array($data)){
		$result = array();
		
		
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				$result[$key] = decodeHtml($val,$type);
			}else
			{
				//GETリクエストの場合はURLデコード
				if($type == 'get')
				{
					$val = urldecode($val);
				}
				$result[$key] = htmlspecialchars_decode($val,ENT_QUOTES);
			}
		}
	}
	else
	{
		if($type == 'get')
		{
			$val = urldecode($data);
		}
		$result = htmlspecialchars_decode($data,ENT_QUOTES);
	}
	return $result;
}


/**
 * 配列のデータを取得
 * 配列が存在しない場合、$defaultを返す
 *
 * @param $param Arrray データ配列
 * @param $key String 配列のキー
 * @param $default Multi 配列が存在しない場合に返す値
 * @return $val Multi
 */
function getParam($param,$key,$default='')
{
	if(isset($param[$key]))
	{
		return $param[$key];
	}
	return $default;
}

/**
 * POSTのデータを取得
 * 配列が存在しない場合、$defaultを返す
 *
 * @param $key String 配列のキー
 * @param $default Multi 配列が存在しない場合に返す値
 * @return $val Multi
 */
function getPost($key,$default='')
{
	if(isset($_POST[$key]))
	{
		return $_POST[$key];
	}
	return $default;
}

/**
 * GETのデータを取得
 * 配列が存在しない場合、$defaultを返す
 *
 * @param $key String 配列のキー
 * @param $default Multi 配列が存在しない場合に返す値
 * @return $val Multi
 */
function getGet($key,$default='')
{
	if(isset($_GET[$key]))
	{
		return $_GET[$key];
	}
	return $default;
}


/**
 * ページ遷移
 *
 * @param String $url 遷移URL
 */
function redirect($url)
{
	header('Location: '.$url,true,301);
	exit();
}



/**
 * テキストの暗号化
 *
 */
/*
function encodePassword($str)
{
	
	$str = sha1($str);
	$str = sha1(PW_KEY.$str);
	
	
	return $str;
}
*/
/**
 * テキストの暗号化
 *
 */
function encodePassword($str)
{
	//暗号化モジュール使用開始
	$td  = mcrypt_module_open('des', '', 'ecb', '');
	$key = substr(PW_KEY, 0, mcrypt_enc_get_key_size($td));
	$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	
	//暗号化モジュール初期化
	if (mcrypt_generic_init($td, $key, $iv) < 0) {
		exit('error.');
	}
	
	//データを暗号化
	$crypt_text = base64_encode(mcrypt_generic($td, $str));
	
	//暗号化モジュール使用終了
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	
	return $crypt_text;
}
/**
 * テキストの復号化
 *
 */
function decodePassword($str)
{
	
	//暗号化モジュール使用開始
	$td  = mcrypt_module_open('des', '', 'ecb', '');
	$key = substr(PW_KEY, 0, mcrypt_enc_get_key_size($td));
	$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

	//暗号化モジュール初期化
	if (mcrypt_generic_init($td, $key, $iv) < 0) {
	  exit('error.');
	}

	//データを復号化
	$plain_text = mdecrypt_generic($td, base64_decode($str));

	//暗号化モジュール使用終了
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return $plain_text;
}


/**
 * 日時の整形(管理画面専用)
 */
function formatDateTime($val)
{
	if($val == '' || $val == '0000-00-00 00:00:00')
	{
		return '-';
	}
	return str_replace(' ','<br />',$val);
}


//JSON用処理
if ( !function_exists('json_encode') )
{
  function json_encode( $array ) 
  {
    if( !is_array($array) )
      return _js_encValue( $array );

    $assoc = FALSE;
    if ( array_diff(array_keys($array),range(0,count($array)-1)) )
      $assoc = TRUE;

    $data = array();
    foreach( $array as $key=>$value )
    {
      if ( $assoc )
      {
        if ( !is_numeric($key) )
          $key = preg_replace('/(["\\\])/u','\\\\$1',$key );
        $key = '"'.$key.'"';
      }
      $value = _js_encValue( $value );
      $data[] = ($assoc ? "$key:$value" : $value);
    }
    if ( $assoc )
      return "{".implode(',',$data)."}";
    else
      return "[".implode(',',$data)."]";
  }

  function _js_encValue( $value )
  {
    if ( is_array($value) )
      return json_encode( $value );
    else if ( is_bool($value) )
      return ($value ? 'true' : 'false');
    else if ( $value === NULL )
      return 'null';
    else if ( is_string($value) )
      return '"'._js_toU16Entities($value).'"';
    else if ( is_numeric($value) )
      return $value;
    return '"'.$value.'"';
  }

  function _js_toU16Entities( $string )
  {
    $len = mb_strlen( $string, 'UTF-8' );
    $str = '';
    $strAry = preg_split( '//u', $string );
    for ( $idx=0, $len=count($strAry); $idx < $len; $idx++ )
    {
      $code = $strAry[$idx];
      if ( $code === '' ) continue;
      if ( strlen($code) > 1 )
      {
        $hex = bin2hex( mb_convert_encoding($code,'UTF-16','UTF-8') );
        if ( strlen($hex) == 8 ) // surrogate pair
          $str .= vsprintf( '\u%04s\u%04s', str_split($hex,4) );
        else
          $str .= sprintf( '\u%04s', $hex );
      } else {
        switch ( $code )
        {
          case '"':
          case '/':
          case '\\':
            $code = '\\'.$code;
        }
        $str .= $code;
      }
    }
    $str = str_replace( array("\r\n","\r","\n"), array('\r\n','\r','\n'), $str );
    return $str;
  }
}



function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"') {
	$d = preg_quote($d);
	$e = preg_quote($e);
	$_line = "";
	$eof = false;
	while (($eof != true)and(!feof($handle))) {
		$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
		$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
		if ($itemcnt % 2 == 0) $eof = true;
	}
	
	$_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
	$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
	preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
	$_csv_data = $_csv_matches[1];
	for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
		$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
		$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
	}
	return empty($_line) ? false : $_csv_data;
}


function execute($commond)
{
	if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
	{
    	// Windwos用の処理
		$fp = popen('start php ' . $commond, 'r');
		pclose($fp);
	} else {
		// サーバ環境用の処理
		exec('php  -f '.$commond.' > /dev/null &'); 
	}
}



/**
 * PHP5.4からでないと対応していないUnicodeアンエスケープをPHP5.3でもできるようにしたラッパー関数
 * @param mixed   $value
 * @param int     $options
 * @param boolean $unescapee_unicode
 */
function json_xencode($value, $options = 0, $unescapee_unicode = true)
{
  $v = json_encode($value);
  if ($unescapee_unicode) {
    $v = unicode_encode($v);
    // スラッシュのエスケープをアンエスケープする
    $v = preg_replace('/\\\\\//', '/', $v);
  }
  return $v;
}

/**
 * Unicodeエスケープされた文字列をUTF-8文字列に戻す。
 * 参考:http://d.hatena.ne.jp/iizukaw/20090422
 * @param unknown_type $str
 */
function unicode_encode($str)
{
  return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", "encode_callback", $str);
}

function encode_callback($matches) {
  return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
}
