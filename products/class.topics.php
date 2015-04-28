<?php
/**
 * トピックス用クラス
 * 主にHTMLパーツの生成を行う
 */
class Topics extends Page {

	 /**
	 * トピックス
	 *
	 * @return Array トピックス
	 */
	 public function getTopicsData(){
		 return $this->manager->db_manager->get('topics')->getAllTopics();
	 }

	 /**
	  * トピックス更新
	  * @param	bool	$delFlg	削除フラグ
	  * @param	int		$id		ID
	  * @param	text	$date	更新日
	  * @param	text	$text	内容
	  * @param	text	$link	リンク
	  * @return	bool	実行結果
	  */
	 public function reloadTopics($delFlg, $id, $date=NULL, $text=NULL, $link=NULL) {
	 	if($delFlg) {
	 		// 削除
	 		$result = $this->manager->db_manager->get('topics')->deleteTopics($id);
	 	} else {
	 		// 更新
	 		$result = $this->manager->db_manager->get('topics')->updateTopics($id, $date, $text, $link);
	 	}

	 	if($result) {
	 		return true;
	 	} else {
	 		return false;
	 	}
	 }

	 /**
	  * トピックス登録
	  * @param	text	$date	更新日
	  * @param	text	$text	内容
	  * @param	text	$link	リンク
	  * @return	bool	実行結果
	  */
	 public function registTopics($date, $text, $link) {
 		$result = $this->manager->db_manager->get('topics')->insertTopics($date, $text, $link);

	 	if($result) {
	 		return true;
	 	} else {
	 		return false;
	 	}
	 }
}