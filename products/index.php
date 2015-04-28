<?php
	include_once('Page.php');
	class index extends Page {
		
		
		
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
				//print($row['relevance_data'].', ');
				print($row['parent_variation'].', ');
				print($row['variation_no'].', ');
				//print($row['item_detail'].', ');
				print($row['item_image'].', ');
				//print($row['relevance_item'].', ');
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
			$item_id		= 'test001';
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
				print($row[0]['icon_id'].', ');
				print($row[0]['icon_name'].', ');
				print($row[0]['icon_file'].', ');
				print('<br />');
			}

			$status_list = null;
			$row = null;
		}
	}

	$index = new index();
	$index->indexAction();

?>
