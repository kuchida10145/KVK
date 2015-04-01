<?php
/**
 * 商品DB管理クラス
 */
class ItemDbModel extends DbModel
{
	var $use_sequence = false;

	/**
	 * フィールドを取得
	 *
	 * @return Array フィールド
	 */
	public function getField()
	{
		$data = array(
				'id',
				'item_id',
				'item_name',
				'view_status',
				'item_status',
				'price',
				'price_zei',
				'map_data',
				'torisetsu_data',
				'kousetsu_data',
				'bunkai_data',
				'shower_data',
				'buy_status',
				'catalog_link',
				'parent_variation',
				'variation_no',
				'note',
				'item_image',
				'category_id',
				'pdf_status',
				'search_word',
				'bunki_kanagu_1',
				'bunki_kanagu_2',
				'bunki_kanagu_3',
				'sell_time',
				'sub_item',
				'sunpou',
				'pitch',
				'shower_sunpou',
				'regist_date',
				'update_date',
		);
		return $data;
	}

	/**
	 * 対象のカテゴリIDに該当する商品一覧を取得する
	 *
	 * @param int $category_id カテゴリID
	 * @return Array
	 */
	public function findByCategoryId($category_id)
	{
		$category_id = $this->escape_string($category_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM item WHERE category_id ='{$category_id}'";

		return $this->db->getAllData($sql);
	}

	public function getCategoryData( $category_id=NULL ){
		if( $category_id != NULL ){
			$sql = "SELECT `child_category`.`category_id` , `parent_category`.`parent_id`,`parent_name`,`category_name`  FROM `child_category` INNER JOIN `parent_category` USING(`parent_id`) WHERE `category_id` = {$category_id}";
			return $this->db->getData($sql);
		}
	}

	/**
	 * 商品一覧を取得
	 */
	public function getItemListByCategoryId( $category_id , $page , $mode ){

		switch( $mode ){
			case SORT_MODE_NEW;
				$order_by = "regist_date";
				$order_type = "DESC";
				break;
			case SORT_MODE_ID;
				$order_by = "item_id";
				$order_type = "ASC";
				break;
		}

		// 件数の取得
		$sql = "SELECT count(*) as cnt FROM `item` WHERE `category_id` = ".$category_id." AND `view_status` = ".VIEW_OK;
		$res = $this->db->getData($sql);
		$cnt = $res["cnt"];

		// ページマックス
		$page_max = ceil($cnt/MAX_LIST_CNT);

		if( $page >  $page_max ){
			$page = $page_max;
		}

		// ページ範囲
		$page_per_num = MAX_LIST_CNT;
		$start = $page*MAX_LIST_CNT-MAX_LIST_CNT;
		$limit = MAX_LIST_CNT;

		// 指定範囲のみ取得
		$sql = "SELECT * FROM `item` WHERE `category_id` = ".$category_id." AND `view_status` = ".VIEW_OK." ORDER BY ".$order_by." ".$order_type." LIMIT ".$start." , ".$limit;
		$res = $this->db->getAllData($sql);

		$return_ar = array();
		$return_ar['cnt'] = $cnt;
		$return_ar['page'] = $page;
		$return_ar['page_max'] = $page_max;
		$return_ar['limit'] = $start+count($res);
		$return_ar['data'] = $res;
		$return_ar['start'] = $start+1;

		return $return_ar;
	}

	/**
	 * 商品検索
	 */
	public function getPartsData( $item_id , $file ){
		// 指定範囲のみ取得
		$sql = "SELECT * FROM `parts_list` WHERE `item_id` = '".$item_id."' AND `file_name` = '".$file."' AND `view_status` = ".VIEW_OK." ORDER BY `parts_no` ASC";
		$res = $this->db->getAllData($sql);
		//var_dump($res,$sql);
		return $res;
	}

	/**
	 * バリエーションデータ取得
	 *
	 * @param Integer $item_id 商品の型番
	 * @return Mixed バリエーションデータ配列　、　NULL時はデータ無し
	 */
	public function getVariationItem( $item_id ){

		$sql = "SELECT * FROM `item` WHERE `parent_variation` = '".$item_id."' AND `view_status` = ".VIEW_OK." ORDER BY `variation_no` ASC";
		$res = $this->db->getAllData($sql);
		return $res;
	}

	/**
	 * 商品検索
	 */
	public function getItemListBySearch( $category_id , $page , $mode , $word , $parent_id=NULL){

		//var_dump($category_id,$parent_id);

		// すべてのカテゴリーを対象
		if( $category_id === NULL && $parent_id === NULL ){

			// 親カテゴリデータ取得（表示がOKのデータのみ取り出したい）
			$sql = "SELECT `category_id` FROM `child_category` WHERE `view_status` = ".VIEW_OK." ORDER BY `category_id` ASC";
			$res = $this->db->getAllData( $sql );
			if( $res ){
				// 親カテゴリ
				$buff_ar = array();
				foreach( $res as $row ){
					$buff_ar[] = $row["category_id"];
				}
				$category_ids = implode(",",$buff_ar);
				unset($buff_ar);
				// カテゴリ指定、表示OK、ワード一致
				$where = "`category_id` IN (".$category_ids." ) AND `view_status` = ".VIEW_OK." AND ( `item_id` LIKE '%".$word."%' OR `item_name` LIKE '%".$word."%' OR `search_word` LIKE '%".$word."%' )";
			}

		}else if( $parent_id != NULL ){
			//$where = "`category_id` = ".$category_id." AND `view_status` = ".VIEW_OK;
			$where = "`parent_id` = ".$parent_id." AND `view_status` = ".VIEW_OK." ORDER BY `parent_id` ASC";

			// 親カテゴリデータ取得
			$sql = "SELECT `category_id` FROM `child_category` WHERE `parent_id` = ".$parent_id." AND `view_status` = ".VIEW_OK." ORDER BY `category_id` ASC";
			$res = $this->db->getAllData( $sql );
			if( $res ){

				// 親カテゴリ
				$buff_ar = array();
				foreach( $res as $row ){
					$buff_ar[] = $row["category_id"];
				}
				$category_ids = implode(",",$buff_ar);
				unset($buff_ar);
				//var_dump($category_ids);
				$where = "`category_id` IN (".$category_ids." ) AND `view_status` = ".VIEW_OK." AND ( `item_id` LIKE '%".$word."%' OR `item_name` LIKE '%".$word."%' OR `search_word` LIKE '%".$word."%' ) ";

			}else{
				echo "parent data is not found.";
				exit;
			}
		}else{
			$where = "`category_id` = ".$category_id." AND `view_status` = ".VIEW_OK." AND ( `item_id` LIKE '%".$word."%' OR `item_name` LIKE '%".$word."%' OR `search_word` LIKE '%".$word."%' ) ";
		}

		// 件数の取得
		$sql = "SELECT count(*) as cnt FROM `item` WHERE ".$where;
		$res = $this->db->getData($sql);
		$cnt = $res["cnt"];
		//var_dump($cnt,$where,$parent_id);exit;
		// ページマックス
		$page_max = ceil($cnt/MAX_LIST_CNT);

		if( $page > $page_max ){
			$page = $page_max;
		}

		// ページ範囲
		$page_per_num = MAX_LIST_CNT;
		$start = $page*MAX_LIST_CNT-MAX_LIST_CNT;
		$limit = MAX_LIST_CNT;

		// ソート指定
		switch( $mode ){
			case SORT_MODE_NEW;
				$order_by = "regist_date";
				$order_type = "DESC";
				break;
			case SORT_MODE_ID;
				$order_by = "item_id";
				$order_type = "ASC";
				break;
		}

		// 指定範囲のみ取得
		$sql = "SELECT * FROM `item` WHERE ".$where." ORDER BY ".$order_by." ".$order_type." LIMIT ".$start." , ".$limit;
		$res = $this->db->getAllData($sql);

		$return_ar = array();
		$return_ar['cnt']				= $cnt;
		$return_ar['page']			= $page;
		$return_ar['page_max']	= $page_max;
		$return_ar['limit']			= $start+count($res);
		$return_ar['data']			= $res;
		$return_ar['start']			= $start+1;

		return $return_ar;
	}

}