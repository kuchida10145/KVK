<?php
/*
 * Form Helper
 *
 * @author Ippei Takahashi
 * @copyright Copyright &copy; 2012 Moon & Sixpence
 */

if(!function_exists('escapeHtml')){
	require_once(dirname(__FILE__).'/Util.php');
}


/**
 * テキストフィールド生成
 *
 * @param string $name Name属性
 * @param string $val 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_input($name,$val,$att="")
{
	if(is_array($val))
	{
		$val = $val[$name];
	}
	$attribute = _make_form_attribute($att);
	$type = 'text';
	
	return '<input type="'.$type.'" name="'.$name.'" value="'.escapeHtml($val).'" '.$attribute.' />';
}


/**
 * パスワードフィールド生成
 *
 * @param string $name Name属性
 * @param string $val 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_password($name,$val,$att="")
{
	
	if(is_array($val))
	{
		$val = $val[$name];
	}
	$attribute = _make_form_attribute($att);
	
	
	return '<input type="password" name="'.$name.'" value="'.escapeHtml($val).'" '.$attribute.' />';
}


/**
 * Hiddenタグ生成
 *
 * @param string $name Name属性
 * @param string $val 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_hidden($name,$val,$att="")
{
	$text = '';
	if(is_array($val))
	{
		$val = $val[$name];
	}
	
	
	$attribute = _make_form_attribute($att);
	return '<input type="hidden" name="'.$name.'" value="'.escapeHtml($val).'" '.$attribute.' />';
}


/**
 * チェックボックス生成
 *
 * @param string $name Name属性
 * @param string,array $valule 選択されている値
 * @param array $data チェックボックスの値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_checkbox($name,$value,$data,$att="")
{
	if(is_array($value)){
		$value = $value[$name];
	}
	
	
	
	$attribute = _make_form_attribute($att);
	$checkbox = array();
	foreach($data as $key => $val)
	{
		$key = str_replace("_","",$key);
		$check = _check_checked($key,$value);
		$checkbox[] = '<label><input type="checkbox" name="'.$name.'[]" value="'.escapeHtml($key).'" '.$check.' '.$attribute.' /> '.$val.'</label><br />';
	}
	return implode(" ",$checkbox);
}


/**
 * テキストエリア生成
 *
 * @param string $name Name属性
 * @param string $value 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_textarea($name,$value="",$att="")
{
	if(is_array($value))
	{
		$value = $value[$name];
	}
	$attribute = _make_form_attribute($att);
	
	if($attribute == "")
	{
		$attribute = ' rows="5" cols="50"';
	}
	
	return '<textarea '.$attribute.' name="'.$name.'" id="'.$name.'">'.escapeHtml($value).'</textarea>';
	
}

/**
 * ラジオボタン生成
 *
 * @param string $name Name属性
 * @param string,array $valule 選択されている値
 * @param array $data チェックボックスの値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_radio($name,$value,$data,$att="")
{
	if(is_array($value)){
		$value = $value[$name];
	}
	
	$attribute = _make_form_attribute($att);
	$checkbox = array();
	foreach($data as $key => $val)
	{
		$check = _check_checked($key,$value);
		$checkbox[] = '<label><input type="radio" name="'.$name.'" value="'.escapeHtml($key).'" '.$check.' /> '.$val.'</label>';
	}
	
	return $checkbox;
}


/**
 * プルダウン生成
 *
 * @param string $name Name属性
 * @param string,array $valule 選択されている値
 * @param array $data チェックボックスの値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_select($name,$value,$data,$att=""){
	if(is_array($value)){
		$value = $value[$name];
	}
	
	$attribute = _make_form_attribute($att);
	$selectbox = array();
	$selectbox[] = '<select name="'.$name.'" '.$attribute.'>';
	foreach($data as $key => $val)
	{
		$check = _check_selected($key,$value);
		$selectbox[] = '<option value="'.escapeHtml($key).'" '.$check.' /> '.$val.'</option>';
	}
	$selectbox[] = '</select>';
	
	return implode("\r\n",$selectbox);
}


/**
 * リッチテキストエディタ生成
 *
 * @param string $name Name属性
 * @param string,array $valule 値
 * @param array $att その他の属性の文字列、配列
 * @param boolean $upload 画像のアップロード機能を利用するかどうか
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_richtext($name,$value,$att="",$upload=false)
{
	
	if(is_array($value))
	{
		$value = $value[$name];
	}
	$attribute = _make_form_attribute($att);
	
	$script = '<script language="javascript" type="text/javascript">'."\r\n";
	
	
	
	$script.= '
tinyMCE.init({ language: "ja",
force_br_newlines : true,

forced_root_block : "",

force_p_newlines : false,


	mode : "exact",
elements: "'.$name.'",
	theme : "advanced",
	 dialog_type: "modal",
  plugins: "advimage,advlink,autosave,contextmenu,fullscreen,inlinepopups,searchreplace,table",
  theme: "advanced",'."\r\n";
	if($upload == true){
		$script.= '  file_browser_callback: "openKCFinder",'."\r\n";
	}

	$script.= '    theme_advanced_blockformats: "p,pre,h4,h5,h6",
  theme_advanced_buttons1: "bold,italic,strikethrough,|,bullist,numlist,|,table,visualaid,|,formatselect,|code,fullscreen,help",
  theme_advanced_buttons2: "forecolor,backcolor,removeformat,|,link,unlink,image,charmap,|,search,replace,|,undo,redo,|,code",
  theme_advanced_buttons3: "",
  theme_advanced_toolbar_location: "top",
  theme_advanced_toolbar_align: "left",
  theme_advanced_statusbar_location: "bottom",
  theme_advanced_resizing: true,
  template_external_list_url : "js/tiny_mce/lists/template_list.js",
  template_external_list_url : "js/tiny_mce/lists/template_list.js",
  external_link_list_url : "js/tiny_mce/lists/link_list.js",
  external_image_list_url : "js/tiny_mce/lists/image_list.js",
  media_external_list_url : "js/tiny_mce/lists/media_list.js",
  template_replace_values : {
	  username : "Some User",
	  staffid : "991234"
  }

});'."\r\n\r\n";

if($upload == true){
	$script.= '
//---[KCFinder]---
function openKCFinder(field_name, url, type, win) {
    tinyMCE.activeEditor.windowManager.open({
        file: "../library/pulgin/kcf/browse.php?opener=tinymce&type=" + type,
        title:"KCFinder",
        width: 700,
        height: 500,
        resizable: "yes",
        inline: true,
        close_previous: "no",
        popup_css: false
    }, {
        window: win,
        input: field_name
    });
    return false;
}
//---[KCFinder]---
'."\r\n";
	}
$script.= '</script>';
	return $script.'<textarea '.$attribute.' name="'.$name.'" id="'.$name.'">'.escapeHtml($value).'</textarea>';
	
}


/**
 * 日時入力ボックス生成
 *
 * @param string $name Name属性
 * @param string $val 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_datetime($name,$value,$att="")
{
	if(is_array($value))
	{
		$value = $value[$name];
	}
	$attribute = _make_form_attribute($att);
	
	if($value == '0000-00-00 00:00:00')
	{
		$value = '';
	}
	
	$script = '
	<input name="'.$name.'" id="'.$name.'" size="30" type="text" value="'.$value.'"/>
	<a  id="'.$name.'_cal" style="padding:1px; cursor:pointer"><img src="image/cal.gif"></a><br />
	<script type="text/javascript">
	var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: true,
		  onTimeChange : updateFields_'.$name.'
      });
      cal.manageFields("'.$name.'_cal", "'.$name.'", "%Y-%m-%d %H:%M:%S");
	  function updateFields_'.$name.'(cal){
		 // var date = cal.selection.get();
		 date = cal.selection.print("%Y-%m-%d %H:%M:%S", " ... ");
		 //alert(cal.getMonth());
		$("#'.$name.'").val(date);
	  }
</script>';
	
	return $script;

}

/**
 * 日付入力ボックス生成
 *
 * @param string $name Name属性
 * @param string $val 値
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_date($name,$value,$att="")
{
	if(is_array($value))
	{
		$value = $value[$name];
	}
	if($value == '0000-00-00')
	{
		$value = '';
	}
	
	$attribute = _make_form_attribute($att);
	
	$script = '
	<input name="'.$name.'" id="'.$name.'" size="10" type="text" value="'.$value.'"/>
	<a  id="'.$name.'_cal" style="padding:1px; cursor:pointer"><img src="image/cal.gif"></a><br />
	<script type="text/javascript">
	var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          //showTime: true,
		  onTimeChange : updateFields_'.$name.'
      });
      cal.manageFields("'.$name.'_cal", "'.$name.'", "%Y-%m-%d");
	  function updateFields_'.$name.'(cal){
		 // var date = cal.selection.get();
		 date = cal.selection.print("%Y-%m-%d", " ... ");
		 //alert(cal.getMonth());
		$("#'.$name.'").val(date);
	  }
</script>';
	
	return $script;

}

/**
 * ファイルタグ生成
 *
 * @param string $name Name属性
 * @param string,array $att その他の属性の文字列、配列
 * @return string HTMLタグ
 * @version 1.1.0
 */
function form_file($name,$att=""){
	$attribute = _make_form_attribute($att);
	
	return '<input type="file" name="'.$name.'" '.$attribute.' />';
}



function form_rank($data)
{
	if((!isset($data['_rank']) || $data['_rank'] == '') && $data['rank'] == '')
	{
		$data['_rank'] = 1;
	}
	elseif((!isset($data['_rank']) || $data['_rank'] == '') && $data['rank'] != '')
	{
		$data['_rank'] = 3;
	}
	$radios = array('1'=>'最初','2'=>'最後','3'=>'指定');
	$radio = implode('&nbsp;' ,form_radio('_rank',$data['_rank'],$radios));
	$input = form_input('rank',$data['rank'],'size="5"');
	return $radio.'&nbsp;'.$input;
}


/**
 * チェックorセレクト用処理
 *
 * @param string $value1 選択されている値
 * @param string,array $value2 値のリスト、文字列
 * @param string $type checked か selected
 * @return string 結果の文字列
 * @version 1.1.0
 */
function _check_checked($value1,$value2,$type='checked')
{
	
	
	if(!is_array($value2)){
		
		if((string)$value1 === (string)$value2)
		{
			return $type.'="'.$type.'"';
		}
	}else{
		foreach($value2 as $key => $val){
			
			if((string)$val === (string)$value1){
				
				return $type.'="'.$type.'"';
			}
		}
	}
	return '';
}


/**
 * チェックorセレクト用処理
 *
 * @param string $value1 選択されている値
 * @param string,array $value2 値のリスト、文字列
 * @param string $type checked か selected
 * @return string 結果の文字列
 * @version 1.1.0
 */
function _check_selected($value1,$value2,$type='selected')
{
	
	
	if(!is_array($value2)){
		
		if((string)$value1 === (string)$value2)
		{
			return $type.'="'.$type.'"';
		}
	}else{
		foreach($value2 as $key => $val){
			
			if((string)$val === (string)$value1){
				
				return $type.'="'.$type.'"';
			}
		}
	}
	return '';
}


/**
 * 属性の生成
 *
 * @param string $att 属性の連想配列か、文字列
 * @return string 属性の文字列
 * @version 1.1.0
 */
function _make_form_attribute($att = "")
{
	if(is_array($att))
	{
		$atts = array();
		foreach($att as $key => $val)
		{
			$atts[] = $key.'="'.$val.'"';
		}
		
		$attribute = implode(" ",$atts);
	}
	else
	{
		$attribute = $att;
	}
	return $attribute;
}

/**
 * 連結されたチェックボックスの内容を分解する
 *
 * @param String $val データの値
 * @return Array 分解後の配列
 */
function separateCheckBoxData($val)
{
	
	if(is_array($val))
	{
		return $val;
	}
	$val = ltrim($val,'/');
	$val = rtrim($val,'/');
	
	$data = explode("/",$val);
	return $data;
}


function file_form($name,$data,$type='file',$size=array())
{
	
	$dir = $data['_base_table'];
	$code = '
	<input name="'.$name.'" id="'.$name.'" value="'.getParam($data,$name).'" type="hidden" />
	<input name="_file_'.$name.'" id="_file_'.$name.'" value="true" type="hidden" />
	
	<input type="file" name="_temp_'.$name.'" id="_temp_'.$name.'" onchange="return autoFileUpload(\''.$name.'\');" />';
	if(getParam($size,'width') != "")
	{
		$code.= '<input name="_width_'.$name.'" id="_width_'.$name.'" value="'.$size['width'].'" type="hidden" />'."\r\n";
	}
	if(getParam($size,'height') != "")
	{
		$code.= '<input name="_height_'.$name.'" id="_height_'.$name.'" value="'.$size['height'].'" type="hidden" />'."\r\n";
	}
	
	//ファイルがある場合は、ファイルのリンク表示
	if(getParam($data,$name) != '')
	{
		$file = $data[$name];
		if(strpos($data[$name],TEMP_FILE_URL) === false)
		{
			$file = UPLOAD_FILE_URL.$dir.'/'.$data['id'].'/'.$data[$name];
		}
		$file_name = str_replace(TEMP_FILE_URL,'',$data[$name]);
		
		$tag = '<div id="_result_'.$name.'">';
		if($type == 'file')
		{
			$tag.= '<a href="'.$file.'" target="_blank">'.$file_name.'</a>';
		}
		else
		{
			$tag.= '<a href="'.$file.'" target="_blank"><img src="'.$file.'"></a>';
		}
		$tag.= '<br /><a href="javascript:void(0);" onClick="deleteFile(\''.$name.'\');" class="button" style="margin-bottom:10px"><span>ファイルを削除する</span></a></div>'.$code;
	}
	else
	{
		$tag = '<div id="_result_'.$name.'"></div>'.$code;
	}
	
	return $tag;
}