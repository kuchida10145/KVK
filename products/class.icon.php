<?php

class Icon{

	private $icon_ar = array();

	/**
	 * コンストラクタ
	 */
	public function Icon(){

	}

	/**
	 * アイコンデータセット
	 *
	 * @param $data Array アイコンデータ配列
	 * @return Boolean 成否
	 */
	public function setIconData( $data ){
		if( is_array($data) ){

			$buff_ar = array();
			foreach( $data as $row ){
				$buff_ar[$row["icon_id"]] = $row;
			}

			$this->icon_ar = $buff_ar;

			return true;
		}
		return false;
	}

	/**
	 * アイコン画像生成
	 *
	 * @param $icon_id Integer アイコンID
	 * @return String 画像
	 */
	public function createIconImage( $icon_id ){

		// チェック
		if( !is_numeric($icon_id) ){
			return '';
		}

		if( isset($this->icon_ar[$icon_id]) ){
			$icon_data = $this->icon_ar[$icon_id];
		}else{
			return '';
		}

		return '<img src="/products/upload/icon/'.$icon_data["icon_id"].'.png" class="balloonbtn" title="'.$icon_data["icon_name"].'" />';

	}
}