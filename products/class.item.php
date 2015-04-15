<?php
/**
 * アイテム用クラス
 * 主にHTMLパーツの生成を行う
 */
class Item extends Page {
	
	var $get;
	var $itemData = array();
	
	/**
	 * GETデータ取得
	 */
	private function getParam( $str=NULL ){
		if( isset( $_GET[$str] ) ){
			$this->get = mysql_real_escape_string($_GET[$str]);
			return true;
		}else{
			return false;
		}
	}
	
	/** 
	  * アイテムデータ生成
	  */
	public function createItemData( $id=NULL ){
		
		// 値チェック
		if( NULL || !is_numeric($id) ){
			$this->error('アイテムIDが指定されていないか、不正なIDです。');
			exit;
			//return false;
		}
		$res = $this->manager->db_manager->get('item')->findById( $id );
		if( $res == NULL ){
			$this->error('存在しないIDか、不正なIDです。');
			exit;
		}
		$this->itemData = $res;
		return true;
	}
	
	
	
	/**
	 * ItemId
	 */
	 public function getItemId(){
		 $res = $this->getParam('id');
		 if( $res ){
			 return $this->get;
		 }else{
			 return NULL;
		 }
	 }
	
	 /**
	 * IconData
	 *
	 * @return Array アイコン
	 */
	 public function getIconData(){
		 return $this->manager->db_manager->get('item_icon')->getAllItem();
	 }
	 
	 /**
	 * アイテム名
	 *
	 * @return String アイテム名
	 */
	public function getTitle(){
		$str = htmlspecialchars($this->itemData['item_name']);
		return $str;
	}
	
	/**
	 * アイテム型番
	 *
	 * @return String 型番
	 */
	public function getKataban(){
		$str = htmlspecialchars($this->itemData['item_id']);
		return $str;
	}
	
	/**
	 * コメント
	 *
	 * @return String コメント
	 */
	public function getComment(){
		if( isset($this->itemData['note']) ){
			$str = htmlspecialchars($this->itemData['note']);
		}else{
			$str = '';
		}
		return $str;
	}
	
	/**
	 * 価格
	 *
	 * @return Integer 価格
	 */
	public function getPrice(){
		if( isset($this->itemData['price']) ){
			$num = htmlspecialchars($this->itemData['price']);
		}else{
			$num = 0;
		}
		return $num;
	}
	
	/**
	 * 価格（税込み）
	 *
	 * @return Integer 税込み価格
	 */
	public function getPriceZei(){
		if( isset($this->itemData['price_zei']) ){
			$num = htmlspecialchars($this->itemData['price_zei']);
		}else{
			$num = 0;
		}
		return $num;
	}
	
	/**
	 * 販売時期
	 *
	 * @return String 販売時期
	 */
	public function getSellTime(){
		if( isset($this->itemData['sell_time']) ){
			$str = htmlspecialchars($this->itemData['sell_time']);
		}else{
			$str = "";
		}
		return $str;
	}
	
	/**
	 * メイン画像
	 * メインの画像を１点取得する
	 * 
	 * @return 画像ファイル名
	 */
	public function getMainImage(){
		
		$img = $this->itemData['item_image'];
		$ar = explode(',',$img);
		
		if( count($ar)>1 ){
			return $ar[0];
		}else{
			return $img;
		}
	}
	
	/**
	 * 画像の取得
	 * カンマ区切りで保存されている画像のファイル名を配列に変換して取得する
	 * 
	 * @return Array 画像ファイル１〜３を配列に保存
	 */
	public function getImages(){
		
		$img = $this->itemData['item_image'];
		$ar = explode(',',$img);
		return $ar;
	}
	
	/**
	 * ピッチ
	 *
	 * @return String ピッチ
	 */
	public function getPitch(){
		if( isset($this->itemData['pitch']) ){
			$str = htmlspecialchars($this->itemData['pitch']);
		}else{
			$str = "";
		}
		return $str;
	}
	
	/**
	 * カタログリンク
	 *
	 * @return Mixed リンク先ファイル名とディレクトリ名、存在しない場合はNULL
	 */
	public function getCatalogLink(){
		
		// ファイル名取得
		$filename = $this->itemData['catalog_link'];
		
		if( $filename != "" ){
			$ar = explode( '_',$filename);
			if( count($ar)==2){
				$dir_name = $ar[0];
			}else{
				return NULL;
			}
		}else{
			return NULL;
		}
		return array('filename'=>$filename,'dirname'=>$dir_name);
	}
	
	/**
	 * 寸法｜寸法とシャワー寸法を接続して出力
	 *
	 * @return String 寸法
	 */
	public function getSunpou(){
		$buff_ar = array();
		if( isset($this->itemData['sunpou']) ){
			$buff_ar[] = htmlspecialchars($this->itemData['sunpou']);
		}
		if( isset($this->itemData['shower_sunpou']) ){
			$buff_ar[] = htmlspecialchars($this->itemData['shower_sunpou']);
		}
		if( count($buff_ar) > 0 ){
			$str = implode("、",$buff_ar);
		}else{
			$str = "";
		}
		return $str;
	}
	
	/**
	 * パーツ表を取得
	 *
	 * @return String パーツ表
	 */
	public function getPartsTable( $file ){
		if( isset($this->itemData['item_id']) ){
			$res = $this->manager->db_manager->get('item')->getPartsData( $this->itemData['item_id'] , $file );
			if( is_array($res) ){
				$buff_ar = array();
				$buff_ar[] = '<table border="0" cellpadding="0" cellspacing="0" class="table03">';
				$buff_ar[] = '<tbody>';
				$buff_ar[] = '<tr>';
				$buff_ar[] = '<th>番号</th>';
				$buff_ar[] = '<th>品番</th>';
				$buff_ar[] = '<th>品名</th>';
				$buff_ar[] = '<th>希望小売価格</th>';
				$buff_ar[] = '</tr>';
				foreach( $res as $key=>$row ){
					$buff_ar[] = '<tr>';
					$buff_ar[] = '<td align="center">'.($key+1).'</td>';
					$buff_ar[] = '<td>'.$row['parts_id'].'</td>';
					$buff_ar[] = '<td>'.$row['parts_name'].'</td>';
					$buff_ar[] = '<td align="right">￥'.number_format($row['price']).'(税込￥'.number_format($row['price_zei']).')</td>';
					$buff_ar[] = '</tr>';
				}
				$buff_ar[] = '</table>';
				$str = implode(PHP_EOL,$buff_ar);
			}else{
				$str = '関連する部品は登録されていません。';
			}
			
		}else{
			$str = "";
		}
		return $str;
	}
	
	/**
	 * バリエーションを取得
	 * item_id（型番）から他のアイテム(バリエーション)を抽出する
	 * 
	 *
	 * @return String バリエーションのテキスト・リンク
	 */
	public function getVariation(){
		$item_id = $this->itemData['item_id'];
		
		// データ取得
		$res = $this->manager->db_manager->get('item')->getVariationItem( $item_id );
		
		// 取得時、HTML生成
		if( $res ){
			$buff_ar = array();
			foreach( $res as $key=>$val ){
				$buff_ar[] = $val["item_id"]." ".$val["item_name"].'￥'.number_format($val["price"]).' (税込 ￥'.number_format($val["price_zei"]).')';
			}
			return implode('<br>'.PHP_EOL , $buff_ar );
		}else{
			return "";
		}
	}
}