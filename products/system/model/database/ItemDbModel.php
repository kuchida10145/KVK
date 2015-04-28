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
				'parent_id',
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

	/**
	 * 対象のカテゴリIDに該当する商品件数を取得する
	 *
	 * @param	int $category_id	カテゴリID
	 * @return	int $itemCount		商品件数
	 */
	public function findByCategoryCount($category_id)
	{
		$itemArray = array();

		$category_id = $this->escape_string($category_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM item WHERE category_id ='{$category_id}' AND view_status = 0";

		$itemArray = $this->db->getAllData($sql);

		return count($itemArray);
	}

	/**
	 * 対象の親カテゴリIDに該当する商品件数を取得する
	 *
	 * @param	int $parent_id	親カテゴリID
	 * @return	int $itemCount	商品件数
	 */
	public function findByParentCount($parent_id)
	{
		$itemArray = array();

		$parent_id = $this->escape_string($parent_id);

		$field = implode(',',$this->getField());

		$sql = "SELECT * FROM item WHERE parent_id ='{$parent_id}' AND view_status = 0";

		$itemArray = $this->db->getAllData($sql);

		return count($itemArray);
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
		//$sql = "SELECT * FROM `parts_list` WHERE `item_id` = '".$item_id."' AND `file_name` = '".$file."' AND `view_status` = ".VIEW_OK." ORDER BY `parts_no` ASC";
		$sql = "SELECT * FROM `parts_list` WHERE `item_id` LIKE '"."%".$item_id."%"."' AND `view_status` = ".VIEW_OK." ORDER BY `id` ASC";
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

	/**
	 * データチェック
	 * @param	$where	データ検索用where句
	 * @return	$result	検索結果(true：データあり false：データなし)
	 */
	public function checkData($where) {
		$table = TABLE_NAME_ITEM;
		$result = true;
		$sql = "";
		$dataCount = array();

		$sql = "SELECT id FROM {$table} WHERE ".$where." limit 0, 1";

		$dataCount = $this->db->getData($sql);

		if(!$dataCount) {
			$result = false;
		}
		return $result;
	}

	/**
	 * 取込データをDBに登録する。
	 *
	 * @param	array	$targetArray	csvから取り込んだ親カテゴリ情報
	 * @return	boolean	$insert_result	DB追加結果
	 */
	public function insertDB($targetArray) {
		$table = TABLE_NAME_ITEM;

		$this->db->startTran();				// トランザクション開始

		$insert_result = $this->db->insert($table, $targetArray);

		$this->db->endTran($insert_result);	// トランザクション終了

		return $insert_result;
	}

	/**
	 * 対象データをチェックして変更箇所を特定する。
	 *
	 * @param	array	$targetData	チェック対象データ
	 * @param	string	$where		DB検索用where句
	 * @return	array	$updateClm	更新対象データ
	 */
	public function updateCheck($targetData, $where) {
		$dbRow = array();
		$updateClm = array();
		$table = TABLE_NAME_ITEM;
		$sql = "SELECT * FROM {$table} WHERE $where";

		$dbRow = $this->db->getData($sql);

		$updateClm = array_diff($targetData, $dbRow);

		return $updateClm;
	}

	/**
	 * データの差異をチェックする。
	 *
	 * @param  array	$dbData		DBから取得したデータ
	 * @param  array	$targetData	チェック対象データ
	 * @return array	$updateClm	更新対象データ
	 */
	protected function compareData($dbData, $targetData) {
		$updateClm = array();

		foreach ($dbData as $key => $value) {
			// 取込データにid,登録日,更新日は無い
			if($key == 'id'){
				$updateClm[$key] = $value;
				continue;
			} elseif($key == COLUMN_NAME_PDF_STATUS) {
				$updateClm[$key] = $value;
				continue;
			} elseif ($key == COLUMN_NAME_REGIST_DATE) {
				$updateClm[$key] = $value;
				continue;
			} elseif ($key == COLUMN_NAME_UPDATE_DATE) {
				$updateClm[$key] = $value;
				continue;
			}

			// 値が違えば更新対象データ
			if($value != $targetData[$key]){
				$updateClm[$key] = $targetData[$key];
			}
		}

		return $updateClm;
	}

}