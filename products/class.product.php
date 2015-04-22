<?php
/**
 * 商品情報用クラス
 * 主にHTMLパーツの生成を行う
 */
class Product extends Page {

	var $get;
	var $post;
	var $parent_id;

	private function getParam( $str=NULL ){
		if( isset( $_GET[$str] ) ){
			$this->get = mysql_real_escape_string($_GET[$str]);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * サーチメニューのOPTIONを取得
	 *
	 * @return String オプションタグ
	 */
	public function getSearchMenu(){

		// 親カテゴリ
		$parentcategory	 = $this->manager->db_manager->get('parent_category')->getAllEnabled();
		$buff_ar = array();
		foreach ( $parentcategory as $row ) {

			// HTML生成とselected処理
			if( $_GET['category_id'] == $row['parent_id'] ){
				$buff_ar[] = '<option value="'.$row['parent_id'].'" selected="selected">'.$row['parent_name'].'</option>';
			}else{
				$buff_ar[] = '<option value="'.$row['parent_id'].'">'.$row['parent_name'].'</option>';
			}

			// 子カテゴリ
			$child_category	 = $this->manager->db_manager->get('child_category')->findByParentId( $row['parent_id'] );
			if( $child_category ){

				// HTML生成とselected処理
				foreach( $child_category as $child_row ){
					if( isset($_GET['category_id']) && $_GET['category_id'] == $row['parent_id'].'_'.$child_row['category_id'] ){
						$buff_ar[] = '<option value="'.$row['parent_id'].'_'.$child_row['category_id'].'" selected="selected">&nbsp;&nbsp;'.$child_row['category_name'].'</option>';
					}else{
						$buff_ar[] = '<option value="'.$row['parent_id'].'_'.$child_row['category_id'].'">&nbsp;&nbsp;'.$child_row['category_name'].'</option>';
					}
				}
			}

		}
		return implode( PHP_EOL , $buff_ar );
	}

	/**
	 * セルフ判定
	 */
	private function checkParentId( $current_id=NULL ){
		if( $current_id == $this->parent_id ){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *
	 */
	private function getParentId( $key , $id ){
		if( $key=="parent_id" ){
			return $id;
		}

	}


	/**
	 * 子カテゴリーIDから親カテゴリーIDを取得
	 *
	 * @return Integer 親カテゴリID
	 */
	private function getParentIdByCategoryId( $id ){
		$res = $this->manager->db_manager->get('child_category')->findByCategoryId( $id );
		if( $res != NULL ){
			return $res["parent_id"];
		}else{
			var_dump($res,$id);
			echo "error";exit;
		}
	}

	/**
	 * 商品IDから親カテゴリーIDを取得
	 *
	 * @return Integer 子カテゴリID
	 */
	private function getParentIdByProductId( $id ){
		$res = $this->manager->db_manager->get('child_category')->findById( $id );
		if( $res != NULL ){
			$res_category = $this->getParentIdByCategoryId( $res["category_id"] );
			return $res_category["parent_id"];
		}
	}

	/**
	 * サイドメニュー取得
	 *
	 * @return Striong メニューのHTML
	 */
	public function getSidemenu( $key=NULL,$value=NULL ){


		if( $key!=NULL && $value!= NULL ){
			if( $key=="category_id" ){
				$this->parent_id = $this->getParentIdByCategoryId($value);
			}else if( $key=="product_id" ){
				$this->parent_id = $this->getParentIdByProductId($value);
			}
		}else{
			if( isset($_GET['parent_id']) && is_numeric($_GET['parent_id']) ){
				$this->parent_id = mysql_real_escape_string( $_GET['parent_id'] );
			}
		}

		// 親カテゴリ一覧
		$parentcategory = $this->manager->db_manager->get('parent_category')->getAllEnabled();
		$buff_ar = array();
		foreach ( $parentcategory as $row ) {
			// データ件数取得
			$itemCount = $this->manager->db_manager->get('item')->findByParentCount($row['parent_id']);
			$buff_ar[] = '<dt><a href="/products/parent/?parent_id='.$row['parent_id'].'">'.$row['parent_name']."(".$itemCount.")".'</a></dt>';
			if( $this->checkParentId( $row['parent_id'] ) ){
				$buff_ar[] = $this->getSidemenuSubcategory( $row['parent_id'] );
			}
		}
		return implode( PHP_EOL , $buff_ar );
	}

	/**
	 * サイドメニューのサブカテゴリー展開
	 *
	 * @return Striong サブメニューのHTML
	 */
	private function getSidemenuSubcategory( $parent_id ){

		$childcategory = $this->manager->db_manager->get('child_category')->findByParentId($parent_id);

		if( $childcategory ){
			$buff_ar = array();
			foreach ( $childcategory as $row ) {
				// データ件数取得
				$itemCount = $this->manager->db_manager->get('item')->findByCategoryCount($row['category_id']);
			// 20150401
//				$buff_ar[] = '<dd><a href="/kvk/products/parent/category/?category_id='.$row['category_id'].'">'.$row['category_name'].'</a></dd>';
				$buff_ar[] = '<dd><a href="/products/parent/category/?category_id='.$row['category_id'].'&parent_id='.$parent_id.'">'.$row['category_name']."(".$itemCount.")".'</a></dd>';
			}
			return implode( PHP_EOL , $buff_ar );
		}else{
			return "";
		}

	}

	public function getParentCategoryData(){
		if( isset($_GET['parent_id']) && is_numeric( $_GET['parent_id'] ) ){
			$parent_id = $_GET['parent_id'];
		}else{
			$this->error('親カテゴリIDが指定されていません。');
			exit;
		}

		$this->parentCategory_data = $this->manager->db_manager->get('parent_category')->findByParentId($parent_id);
		if( $this->parentCategory_data == NULL ){
			$this->error('親カテゴリIDは存在していないか、または非公開です。');
			exit;
		}
	}

	/**
	 * 子カテゴリ一覧出力
	 * データを取得し、メンバー変数childCategory_dataに結果を保存する
	 */
	public function getChildCategoryData( $category_id=NULL ){

		if( $category_id != NULL ){
			$this->childCategory_data = $this->manager->db_manager->get('child_category')->findByCategoryId($category_id);
		}else{
			$this->error('子カテゴリIDが指定されていません');
			exit;
		}

		if( $this->childCategory_data == NULL ){
			$this->error('子カテゴリIDは存在していないか、または非公開です。');
			exit;
		}
	}

	/**
	 * 親カテゴリ一覧データの取得
	 */
	public function getParentCategoryItemList(){

		$res = $this->manager->db_manager->get('parent_category')->getAllEnabled();

		if( $res ){

			$buff_ar = array();
			foreach( $res as $val ){

				if( is_file($_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$val["parent_image"]) ){
					$img_path = DIR_UPLOAD.$val["parent_image"];
				}else{
					$img_path = DIR_UPLOAD.'blank_category.jpg';
				}

				$buff_ar[] = '<dl>';
				$buff_ar[] = '<dt><a href="/products/parent/?parent_id='.$val["parent_id"].'"><img src="'.$img_path.'" alt="" /></a></dt>';
				$buff_ar[] = '<dd>'.$val['parent_name'].'</dd>';
				$buff_ar[] = '</dl>';
			}
			return implode( PHP_EOL , $buff_ar );
		}

	}

	public function getChildCategoryItemList(){
		if( isset( $this->parentCategory_data["parent_id"] ) ){
			$parent_id = $this->parentCategory_data["parent_id"];
			$res = $this->manager->db_manager->get('child_category')->findByParentId($parent_id);

			if( $res ){

				$buff_ar = array();
				foreach( $res as $val ){

					if( is_file($_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$val["category_image"]) ){
						$img_path = DIR_UPLOAD.$val["category_image"];
					}else{
						$img_path = DIR_UPLOAD.'blank_category.jpg';
					}

					$buff_ar[] = '<dl>';
					// 20150401
//					$buff_ar[] = '<dt><a href="/kvk/products/parent/category/?category_id='.$val["category_id"].'"><img src="'.$img_path.'" /></a></dt>';
					$buff_ar[] = '<dt><a href="/products/parent/category/?category_id='.$val["category_id"].'&parent_id='.$parent_id.'"><img src="'.$img_path.'" /></a></dt>';
					$buff_ar[] = '<dd>'.$val['category_name'].'</dd>';
					$buff_ar[] = '</dl>';
				}
				return implode( PHP_EOL , $buff_ar );
			}
		}
	}

	/**
	 * アイテムのディレクトリ
	 *
	 * @return String ディレクトリ
	 */
	private function getItemDir(){
		return '/products/parent/category/item/';
	}

	/**
	 * 子カテゴリの商品一覧
	 * 子カテゴリ一覧ページと検索結果ページで共通となる
	 * ページ処理に必要なデータはメンバー変数に保存
	 *
	 * @param Integer $parent_id 親カテゴリID
	 * @param Integer $category_id カテゴリID
	 * @param Integer $page ページ番号
	 * @param Integer $mode 検索ソート
	 * @param String  $word ワード
	 * @param Integer $parent_id 子カテゴリID
	 * @return String 出力HTML
	 */
	public function getProductItemList( $parent , $category_id , $page , $mode , $word=NULL , $parent_id=NULL ){
		if( $word !== NULL ){
			$search_mode_enabled = true;
		}else{
			$search_mode_enabled = false;
		}

		if( $search_mode_enabled ){
			// search mode
			// 処理無し
		}else{
			// category mode
			// カテゴリーデータ取得
			$this->childCategory_data = $this->manager->db_manager->get('child_category')->findByCategoryId($category_id);
		}


		// 子カテゴリがあるか
		if( isset( $this->childCategory_data["category_id"] ) || $search_mode_enabled ){

			// IDから登録アイテムを取得
			$category_id = $this->childCategory_data["category_id"];
			if( $search_mode_enabled ){
				// 検索実行
				$ListData = $this->manager->db_manager->get('item')->getItemListBySearch($category_id,$page,$mode,$word,$parent_id);
			}else{
				// 子カテゴリからアイテム取得
				$ListData = $this->manager->db_manager->get('item')->getItemListByCategoryId($category_id,$page,$mode);
			}

			// データ
			$res = $ListData['data'];

			// ページ管理に必要なデータ
			$this->page = $ListData['page'];
			$this->cnt = $ListData['cnt'];
			$this->limit = $ListData['limit'];
			$this->start = $ListData['start'];
			$this->page_max = $ListData['page_max'];

			//var_dump( $res[0] );exit;

			$item_filename= "item.html?id=";

			if( $res ){
				//var_dump($res);exit;
				$buff_ar = array();
				foreach( $res as $val ){

					$img_ar = explode(',',$val['item_image']);

					if( is_file($_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$img_ar[0]) ){
						$img_path = DIR_UPLOAD.$img_ar[0];
					}else{
						$img_path = DIR_UPLOAD.'blank_item_thumbnail.jpg';
					}

					$buff_ar[] = '<!--1件-->';
					$buff_ar[] = '<dl>';
					$buff_ar[] = '<dt><a href="'.$this->getItemDir().$item_filename.$val["id"].'"><img src="'.$img_path.'" alt="" /></a></dt>';
					$buff_ar[] = '<dd><a href="'.$this->getItemDir().$item_filename.$val["id"].'" class="txt130">'.$val["item_name"].'</a><br />';
					$buff_ar[] = ''.$val["item_id"].'<br />';
					$buff_ar[] = '￥'.number_format($val["price"]).'（税込￥'.number_format($val["price_zei"]).'）';
					$buff_ar[] = '<div class="iconbox">';

					if( is_file( $_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$val['map_data'] ) ){
						$buff_ar[] = '<a href="/products/parent/category/item/authentication.html?file='.$val['map_data'].'" class="icondrawing balloonbtn" title="商品の外観図面をPDF形式で表示します" rel="shadowbox;width=720">図面</a>';
					}
					if( is_file( $_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$val['torisetsu_data'] ) ){
						$buff_ar[] = '<a href="/products/parent/category/item/authentication.html?file='.$val['torisetsu_data'].'" class="iconmanual balloonbtn" title="商品の取扱説明書をPDF形式で表示します" rel="shadowbox;width=720">取扱説明書</a>';
					}
					if( is_file( $_SERVER['DOCUMENT_ROOT'].DIR_UPLOAD.$val['kousetsu_data'] ) ){
						$buff_ar[] = '<a href="/products/parent/category/item/authentication.html?file='.$val['kousetsu_data'].'" class="iconconstruction balloonbtn" title="商品の施工説明書をPDF形式で表示します" rel="shadowbox;width=720">施工説明書</a>';
					}
					if( $val['buy_status'] ){
						$buff_ar[] = '<a href="'.$val['buy_status'].'" class="iconbuy balloonbtn" title="商品のご購入窓口にリンクします">購入</a>';
					}
					if( $val['map_data'] || $val['torisetsu_data'] || $val['kousetsu_data'] || $val['buy_status'] ){
						$buff_ar[] = '<br />';
					}

					if( $val['bunkai_data'] || $val['shower_data'] ){

						$buff_ar[] = '分解図<br />';

						if( $val['bunkai_data'] ){
							$buff_ar[] = '<a href="/products/parent/category/item/map.html?id='.$val["id"].'&file='.$val['bunkai_data'].'" class="iconfaucet balloonbtn" title="水栓です">水栓</a>';
						}
						if( $val['shower_data'] ){
							$buff_ar[] = '<a href="/products/parent/category/item/map.html?id='.$val["id"].'&file='.$val['shower_data'].'" class="iconshower balloonbtn" title="シャワーです">シャワー</a>';
						}
					}
					$buff_ar[] = '</div>';

					$buff_ar[] = '</dd>';
					$buff_ar[] = '</dl>';
					$buff_ar[] = '<!--/1件-->';
				}
				return implode( PHP_EOL , $buff_ar );
			}else{
				return '';
			}
		}
	}

	/**
	  * アイテムデータ生成
	  */
	public function createItemData( $id=NULL ){

		// 値チェック
		if( NULL || !is_numeric($id) ){
			return false;
		}
		$res = $this->manager->db_manager->get('item')->findById( $id );
		$this->itemData = $res;
		return true;
	}

	/**
	 * アイテムのタイトル
	 */
	public function getItemTitle(){

	}

	/**
	 * パンくず生成
	 */
	public function getTopicPath( $category_id , $disabled_category_link=false ){

		// データ取得
		$res = $this->manager->db_manager->get('item')->getCategoryData( $category_id );

		// 生成
		if( $disabled_category_link != true ){
		// 20150401
//			$topicpath = '<a href="/kvk/products/">商品情報 </a> &gt; <a href="/kvk/products/parent/?parent_id='.$res["parent_id"].'">'.$res["parent_name"].'</a> &gt; <a href="/kvk/products/parent/category/?category_id='.$res["category_id"].'">'.$res["category_name"].'</a>';
			$topicpath = '<a href="/products/">商品情報 </a> &gt; <a href="/products/parent/?parent_id='.$res["parent_id"].'">'.$res["parent_name"].'</a> &gt; <a href="/products/parent/category/?category_id='.$res["category_id"].'&parent_id='.$res["parent_id"].'">'.$res["category_name"].'</a>';
		}else{
			$topicpath = '<a href="/products/">商品情報 </a> &gt; <a href="/products/parent/?parent_id='.$res["parent_id"].'">'.$res["parent_name"].'</a> &gt; '.$res["category_name"].'';
		}

		return $topicpath;
	}

	/**
	 * コーディング参考
	 */
	public function indexAction() {

		/**
		 * 親カテゴリ取得テスト
		 **/
		$parentcategory		= $this->manager->db_manager->get('parent_category')->getAll();

		print("【親カテゴリ全件取得】");
		print('<br />');

		foreach ($parentcategory as $row) {
			print('<br />');
			print($row['parent_id'].', ');
			print($row['parent_name'].', ');
			print($row['parent_image']);
			print('<br />');
		}

		$parentcategory = null;
		$row = null;

		/**
		 * 子カテゴリ取得テスト
		 **/
		$parent_id = '1';
		$childcategory		= $this->manager->db_manager->get('child_category')->findByParentId($parent_id);

		print('<br />');
		print("【特定の親カテゴリに所属する子カテゴリ全件取得】");
		print('<br />');
		print("・親カテゴリID = '$parent_id'");
		print('<br />');

		foreach ($childcategory as $row) {
			print('<br />');
			print($row['category_id'].', ');
			print($row['category_name'].', ');
			print($row['category_image']);
			print('<br />');
		}

		$childcategory = null;
		$row = null;

		/**
		 * 商品一覧取得テスト
		 **/
		$category_id	= '100';
		$item			= $this->manager->db_manager->get('item')->findByCategoryId($category_id);

		print('<br />');
		print("【特定のカテゴリに所属する商品を全件取得】");
		print('<br />');
		print("・カテゴリID = '$category_id'");
		print('<br />');

		foreach ($item as $row) {
			print('<br />');
			print($row['id'].', ');
			print($row['item_id'].', ');
			print($row['item_name'].', ');
			print($row['view_status'].', ');
			print($row['item_status'].', ');
			print($row['price'].', ');
			print($row['price_zei'].', ');
			print($row['map_data'].', ');
			print($row['torisetsu_data'].', ');
			print($row['kousetsu_data'].', ');
			print($row['bunkai_data'].', ');
			print($row['shower_data'].', ');
			print($row['buy_status'].', ');
			print($row['catalog_link'].', ');
			print($row['relevance_data'].', ');
			print($row['parent_variation'].', ');
			print($row['variation_no'].', ');
			print($row['item_detail'].', ');
			print($row['item_image'].', ');
			print($row['relevance_item'].', ');
			print($row['category_id'].', ');
			print($row['pdf_status'].', ');
			print($row['regist_date'].', ');
			print($row['update_date']);
			print('<br />');
		}

		$item = null;
		$row = null;

		/**
		 * パーツ一覧取得テスト
		 **/
		$item_id		= 'KF2G3';
		$parts_list		= $this->manager->db_manager->get('parts_list')->findByItemId($item_id);

		print('<br />');
		print("【特定の品番に所属するパーツを全件取得】");
		print('<br />');
		print("・品番 = '$item_id'");
		print('<br />');

		foreach ($parts_list as $row) {
			print('<br />');
			print($row['id'].', ');
			print($row['parts_no'].', ');
			print($row['parts_id'].', ');
			print($row['parts_name'].', ');
			print($row['price'].', ');
			print($row['price_zei'].', ');
			print($row['haiban_status'].', ');
			print($row['daigae_status'].', ');
			print($row['item_id'].', ');
			print($row['note'].', ');
			print($row['regist_date'].', ');
			print($row['update_date'].', ');
			print('<br />');
		}

		$parts_list = null;
		$row = null;

		/**
		 * ステータス一覧取得テスト
		 **/
		$status_id = "1,2,4,5";
		$status_id_array = explode(',', $status_id);

		foreach ($status_id_array as $key_status_id) {
			$status = $this->manager->db_manager->get('item_icon')->findByItemStatus($key_status_id);
			$status_list[] = $status;
		}

		print('<br />');
		print("【対象のステータスIDのステータスを取得】");
		print('<br />');
		print("・ステータスID = '$status_id'");
		print('<br />');

		foreach ($status_list as $row) {
			print('<br />');
			print($row[0]['icon_jd'].', ');
			print($row[0]['icon_name'].', ');
			print($row[0]['icon_file'].', ');
			print('<br />');
		}

		$status_list = null;
		$row = null;
	}

}