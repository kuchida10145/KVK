<?php
	include_once('/../AbstractImportCsv.php');
	class ImportCsvItemStatus extends AbstractImportCsv {
		function __construct() {
			parent::__construct();
			// 品番
			$this->manager->validationColumns->setRule(ITEM_ID_COLUMN_ITEM_STATUS, 'required');
			// JIS
			$this->manager->validationColumns->setRule(JIS_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 第三者認証登録品
			$this->manager->validationColumns->setRule(NINSYO_TOUROKU_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 第三者認証品
			$this->manager->validationColumns->setRule(NINSYO_ITEM_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 寒冷地用
			$this->manager->validationColumns->setRule(KANREITI_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 逆止弁付
			$this->manager->validationColumns->setRule(GYAKUSHIBEN_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 保証書
			$this->manager->validationColumns->setRule(HOSYOUSYO_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 新商品
			$this->manager->validationColumns->setRule(NEW_ITEM_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// スーパーサーモ
			$this->manager->validationColumns->setRule(SUPER_SARMO_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// eシャワー
			$this->manager->validationColumns->setRule(E_SHOWER_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// やさしさ推奨品
			$this->manager->validationColumns->setRule(YASASHISA_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// お湯ぴた
			$this->manager->validationColumns->setRule(OYUPITA_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// サーモスタット式水栓
			$this->manager->validationColumns->setRule(SARMO_STAT_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 自閉式
			$this->manager->validationColumns->setRule(JIHEISHIKI_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// ミキシング式
			$this->manager->validationColumns->setRule(MIXING_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 一次止水付
			$this->manager->validationColumns->setRule(SHISUITSUKI_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// ソーラー用
			$this->manager->validationColumns->setRule(SORLAR_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// セラミックシングル
			$this->manager->validationColumns->setRule(SELAMIC_SINGLE_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// eレバー水栓
			$this->manager->validationColumns->setRule(E_LEVER_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// マルチ水栓
			$this->manager->validationColumns->setRule(MALUTI_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// センサー付
			$this->manager->validationColumns->setRule(SENSOR_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// スーパーシングル
			$this->manager->validationColumns->setRule(SUPER_SINGLE_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// シングルレバー式水栓
			$this->manager->validationColumns->setRule(SINGLE_LEVER_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// NSFシャワー
			$this->manager->validationColumns->setRule(NSF_SHOWER_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 泡沫吐水
			$this->manager->validationColumns->setRule(AWA_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 省施工仕様
			$this->manager->validationColumns->setRule(SHOSEKOU_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 上施工方式（省施工仕様）
			$this->manager->validationColumns->setRule(JOUSEKOU_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 浄水器付
			$this->manager->validationColumns->setRule(JOSUIKI_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// エコこま水栓
			$this->manager->validationColumns->setRule(ECO_KOMA_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// プチエコ水栓
			$this->manager->validationColumns->setRule(PUCHI_ECO_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 緊急止水機能付
			$this->manager->validationColumns->setRule(STOP_WATER_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯A1
			$this->manager->validationColumns->setRule(SETSUYU_A1_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯B1
			$this->manager->validationColumns->setRule(SETSUYU_B1_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯C1
			$this->manager->validationColumns->setRule(SETSUYU_C1_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯A
			$this->manager->validationColumns->setRule(SETSUYU_A_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯B
			$this->manager->validationColumns->setRule(SETSUYU_B_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');
			// 節湯AB
			$this->manager->validationColumns->setRule(SETSUYU_AB_COLUMN_ITEM_STATUS, 'numeric|digit|pnumeric');

			// エラーメッセージ作成用配列
			$this->msg_rules = array(
										'required'=>ERROR_MSG_FORM_ERROR,
										'numeric'=>ERROR_MSG_NUM_ERROR,
										'digit'=>ERROR_MSG_NUM_ERROR,
										'pnumeric'=>ERROR_MSG_NUM_ERROR
									);
			// csvヘッダー項目数
			$this->headerCount = HEADER_COUNT_ITEM_STATUS;
		}

	/**
	 * DBチェック
	 * @param  $checkData	csvデータ
	 * @return $result		チェック結果
	 */
	protected function dataDBCheck($checkData, $line_count) {
		$deleteFlg = "";
		$getItemArray = "";
		$table = TABLE_NAME_ITEM;
		$itemArray = array();
		$result = true;
		$limit = "";
		$order = "";

		// 商品IDから商品データを取得
		$getItemArray = COLUMN_NAME_ITEM_ID.' = "'.$checkData[ITEM_ID_COLUMN_ITEM_STATUS].'"';
		$itemArray = $this->manager->db_manager->get($table)->search($getItemArray, $limit, $order);
		if(count($itemArray) == 0) {
			$result = false;
		}

		if(!$result) {
			$this->{KEY_DB_CHECK_MESSAGE} = "対象の商品データが存在しません。 {$line_count}行目<br>";
		}
		return $result;
	}

	/**
	 * cavデータ重複データチェック
	 * @param	$checkData	チェック対象データ
	 * @param	$lineCount	現在のcsvの行数
	 * @return	$result		チェック結果
	 */
	protected function dataPrimaryCheck($checkData, $lineCount) {
		$result = true;
		// キー項目が前にチェックしたデータにあったかチェックする
		if ($this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]} != null) {
			$this->{DUPLICATION_LINE} = $this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]};
			$result = false;
		} else {
			$this->{$checkData[ITEM_ID_COLUMN_ITEM_STATUS]} = $lineCount;
		}
		return $result;
	}

	/**
	 * DB処理実行
	 * @param	$targetArray	DB処理対象データ
	 * @return	$result			チェック結果
	 */
	protected function runDB($targetArray) {
		$dataArray = array();		// 更新データ格納用の配列
		$itemArray = array();		// 商品データ検索結果
		$itemRow = array();			// 商品データ（1行）
		$dbCheck = "";				// DB動作結果
		$table = TABLE_NAME_ITEM;	// テーブル名
		$where = "";				// SQL実行用のwhere句
		$key = "";					// DB検索用Key
		$arrayCount = 0;			// 配列要素カウント用
		$itemStatus = "";			// 商品テーブル.商品ステータス
		$limit = "";
		$order = "";

		// DB更新データ（商品ステータスデータ作成）
		foreach ($targetArray as $status) {
			if($status === "1") {
				$itemStatus = $itemStatus.$arrayCount.",";
			}
			$arrayCount = $arrayCount + 1;
		}
		$itemStatus = substr($itemStatus, 0, -1);   //最後の「,」を削除

		// 商品IDから商品データを取得
		$getItemArray = COLUMN_NAME_ITEM_ID.' = "'.$targetArray[ITEM_ID_COLUMN_ITEM_STATUS].'"';
		$itemArray = $this->manager->db_manager->get($table)->search($getItemArray, $limit, $order);
		$itemRow = $itemArray[0];

		// 商品データ更新
		$itemRow[COLUMN_NAME_ITEM_STATUS] = $itemStatus;			// 商品ステータス
		$itemRow[COLUMN_NAME_UPDATE_DATE] = date("Y-m-d H:i:s");	// 更新日

		// where句生成
		$where = COLUMN_NAME_ITEM_ID." = '".$itemRow[COLUMN_NAME_ITEM_ID]."' AND ".COLUMN_NAME_CATEGORY_ID." = '".$itemRow[COLUMN_NAME_CATEGORY_ID]."'";

		$dbCheck = $this->manager->db_manager->get($table)->update($itemRow, $where);

		return $dbCheck;
	}
}

?>
