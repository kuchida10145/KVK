<?php
/**
 * 設定ファイル
 *
 */


/*
|--------------------------------------------------------------------------
| データベース設定
|--------------------------------------------------------------------------
 */
if( $_SERVER['HTTP_HOST'] == 'test.kvk01.dpg.mmrs.jp' ){
	define('DB_NAME','dpg_kvk1_test');
	define('DB_USER','dpg_kvk1');
	define('DB_PASS','tEVYgtu.');
	define('DB_HOST','db1.dpg.mmrs.jp');
	define('DB_CHARSET','utf8');
} else {
	define('DB_NAME','kvk');
	define('DB_USER','root');
	define('DB_PASS','yuki10145');
	define('DB_HOST','localhost');
	//define('DB_HOST','203.138.100.12');
	define('DB_CHARSET','utf8');
}

/*
|--------------------------------------------------------------------------
| 定数宣言
|--------------------------------------------------------------------------
 */

// 表示or非表示
define( 'VIEW_OK' , 0 );
define( 'VIEW_NG' , 1 );

// 画面ステータス
define( 'INISIAL_DISP' , 0 );
define( 'CSV_DOWNLOAD' , 1 );

// DB取込ステータス
define( 'DB_UPDATE' , 0 );
define( 'CSV_UPDATE' , 1 );

// ソートモード
define( 'SORT_MODE_NEW' , 1 );
define( 'SORT_MODE_ID' , 2 );
define( 'SORT_MODE_SEARCH' , 3 );

// 子カテゴリにおける商品の表示件数
define( 'MAX_LIST_CNT' , 10);

// ディレクトリ名（PATHではない）
define('DIR_UPLOAD', '/products/upload/');
define('DIR_MAP', '/products/upload/');
define('DIR_TORISETSU', '/products/upload/');
define('DIR_KOUSETSU', '/products/upload/');
define('DIR_BUNKAI', '/products/upload/');
define('DIR_SHOWER', '/products/upload/');
define('DIR_CATALOG', '/products/upload/catalog/');

/**
 * 共通
 * */
/** CSVヘッダー行 */
define('CSV_HEADER_LINE', '0');

/** CSVデータ最小データ数 */
define('CSV_DATA_MIN', '2');

/** エラーメッセージ取得key */
define('KEY_ERROR_MESSAGE', 'errorMessage');

/** DBチェックメッセージ取得key */
define('KEY_DB_CHECK_MESSAGE', 'dataDBCheckMessage');

/** 取込結果メッセージ取得key */
define('KEY_TORIKOMI_MESSAGE', 'torikomiMessage');

/** PDF作成開始時間取得key */
define('KEY_PDF_MAKE_TIME', 'pdfTime');

/** csv拡張子 */
define('CSV_EXTENTION', 'csv');

/** 削除フラグ（データ非表示フラグ） */
define('DELETE_FLG', '1');

/** 重複データ行数取得キー */
define('DUPLICATION_LINE', 'duplicationLine');

/** 品番（商品） */
define('COLUMN_NAME_ITEM_ID', 'item_id');

/** 希望小売価格 */
define('COLUMN_NAME_PRICE', 'price');

/** 希望小売価格（税込み） */
define('COLUMN_NAME_PRICE_ZEI', 'price_zei');

/** 表示ステータス */
define('COLUMN_NAME_VIEW_STATUS', 'view_status');

/** 備考 */
define('COLUMN_NAME_NOTE', 'note');

/** 登録日 */
define('COLUMN_NAME_REGIST_DATE', 'regist_date');

/** 更新日 */
define('COLUMN_NAME_UPDATE_DATE', 'update_date');

/** pdfファイル格納先 */
define('PDF_FILE_PATH', 'file/pdf/');

/** pdfファイルバックアップ */
define('PDF_BACKUP_PATH', 'file/backup/pdf/');

/** 文字コード（sjis-win） */
define('CSV_CODE', 'sjis-win');

/** 文字コード（UTF-8） */
define('SYSTEM_CODE', 'utf-8');

/** pdf作成ステータス(0：未作成) */
define('PDF_STATUS_MISAKUSEI', '0');

/** pdf作成ステータス(1：作成済み) */
define('PDF_STATUS_ZUMI', '1');

/** JOBコマンド */
define('JOB_COMMAND', 'ls > /products/system/controller/page/makepdf/ExecuteMakePdfFile.php');

/**
 * カテゴリ系
 */
/** カテゴリーデータCSVのカラム数 */
define('HEADER_COUNT_CATEGORY', '5');

/** カラム番号：カテゴリID */
define('CATEGORY_ID_COLUMN_CATEGORY', '0');

/** カラム番号：カテゴリ名 */
define('CATEGORY_NAME_COLUMN_CATEGORY', '1');

/** カラム番号：親カテゴリID */
define('PARENT_ID_COLUMN_CATEGORY', '2');

/** カラム番号：イメージ画像 */
define('IMAGE_COLUMN_CATEGORY', '3');

/** カラム番号：削除フラグ */
define('DELETE_COLUMN_CATEGORY', '4');

/** 親カテゴリテーブル */
define('TABLE_NAME_PARENT_CATEGORY', 'parent_category');

/** 親カテゴリID */
define('COLUMN_NAME_PARENT_ID', 'parent_id');

/** 親カテゴリ名 */
define('COLUMN_NAME_PARENT_NAME', 'parent_name');

/** 親カテゴリ画像 */
define('COLUMN_NAME_PARENT_IMAGE', 'parent_image');

/** 子カテゴリテーブル */
define('TABLE_NAME_CHILD_CATEGORY', 'child_category');

/** 子カテゴリID */
define('COLUMN_NAME_CATEGORY_ID', 'category_id');

/** 子カテゴリ名 */
define('COLUMN_NAME_CATEGORY_NAME', 'category_name');

/** 子カテゴリ画像 */
define('COLUMN_NAME_CATEGORY_IMAGE', 'category_image');

/**
 * 部品系
 */
/** 部品データCSVのカラム数 */
define('HEADER_COUNT_PARTS', '9');

/** カラム番号：番号（部品表示順） */
define('NO_COLUMN_PARTS', '0');

/** カラム番号：品番 */
define('PARTS_ID_COLUMN_PARTS', '1');

/** カラム番号：品名 */
define('PARTS_NAME_COLUMN_PARTS', '2');

/** カラム番号：希望小売価格（税抜き） */
define('PRICE_COLUMN_PARTS', '3');

/** カラム番号：希望小売価格（税込み） */
define('PRICE_ZEI_COLUMN_PARTS', '4');

/** カラム番号：品番（紐付く商品） */
define('ITEM_COLUMN_PARTS', '5');

/** カラム番号：ファイル名 */
define('FILE_COLUMN_PARTS', '6');

/** カラム番号：備考 */
define('NOTE_COLUMN_PARTS', '7');

/** カラム番号：削除フラグ */
define('DELETE_COLUMN_PARTS', '8');

/** 部品図テーブル */
define('TABLE_NAME_PARTS_LIST', 'parts_list');

/** ID */
define('COLUMN_NAME_ID', 'id');

/** 番号（部品表示順） */
define('COLUMN_NAME_NO', 'parts_no');

/** 品番（パーツ） */
define('COLUMN_NAME_PARTS_ID', 'parts_id');

/** 品名（パーツ） */
define('COLUMN_NAME_PARTS_NAME', 'parts_name');

/** ファイル名 */
define('COLUMN_NAME_FILE_NAME', 'file_name');

/**
 * 商品系
 */
/** カテゴリーデータCSVのカラム数 */
define('HEADER_COUNT_ITEM', '28');

/** カラム番号：品番 */
define('ITEM_ID_COLUMN_ITEM', '0');

/** カラム番号：品名 */
define('ITEM_NAME_COLUMN_ITEM', '1');

/** カラム番号：写真 */
define('ITEM_PHOTO_COLUMN_ITEM', '2');

/** カラム番号：図面 */
define('MAP_COLUMN_ITEM', '3');

/** カラム番号：取説 */
define('TORISETSU_COLUMN_ITEM', '4');

/** カラム番号：施工 */
define('SEKOU_COLUMN_ITEM', '5');

/** カラム番号：分解図 */
define('BUNKAI_COLUMN_ITEM', '6');

/** カラム番号：シャワー */
define('SHOWER_COLUMN_ITEM', '7');

/** カラム番号：購入リンク */
define('BUY_STATUS_COLUMN_ITEM', '8');

/** カラム番号：価格 */
define('PRICE_COLUMN_ITEM', '9');

/** カラム番号：価格（税込） */
define('PRICE_ZEI_COLUMN_ITEM', '10');

/** カラム番号：備考 */
define('NOTE_COLUMN_ITEM', '11');

/** カラム番号：商品画像 */
define('ITEM_IMAGE_COLUMN_ITEM', '12');

/** カラム番号：バリエーション名 */
define('VARIATION_NAME_COLUMN_ITEM', '13');

/** カラム番号：バリエーション表示順 */
define('VARIATION_NO_COLUMN_ITEM', '14');

/** カラム番号：カタログ年度 */
define('CATALOG_YEAR_COLUMN_ITEM', '15');

/** カラム番号：カタログページ */
define('CATALOG_PAGE_COLUMN_ITEM', '16');

/** カラム番号：検索ワード */
define('SEARCH_WORD_COLUMN_ITEM', '17');

/** カラム番号：分岐金具_1 */
define('BUNKI_KANAGU_1_COLUMN_ITEM', '18');

/** カラム番号：分岐金具_2 */
define('BUNKI_KANAGU_2_COLUMN_ITEM', '19');

/** カラム番号：分岐金具_3 */
define('BUNKI_KANAGU_3_COLUMN_ITEM', '20');

/** カラム番号：販売時期 */
define('SELL_KIKAN_COLUMN_ITEM', '21');

/** カラム番号：代替品 */
define('DAIGAE_COLUMN_ITEM', '22');

/** カラム番号：取付寸法 */
define('SUNPOU_COLUMN_ITEM', '23');

/** カラム番号：ピッチ */
define('PITCH_COLUMN_ITEM', '24');

/** カラム番号：シャワー取付寸法 */
define('SHOWER_SUNPOU_COLUMN_ITEM', '25');

/** カラム番号：削除 */
define('DELETE_COLUMN_ITEM', '26');

/** カラム番号：カテゴリID */
define('CATEGORY_ID_COLUMN_ITEM', '27');

/** 品名 */
define('COLUMN_NAME_ITEM_NAME', 'item_name');

/** 商品ステータス */
define('COLUMN_NAME_ITEM_STATUS', 'item_status');

/** 図面データ */
define('COLUMN_NAME_MAP_DATA', 'map_data');

/** 取説データ */
define('COLUMN_NAME_TORISETSU_DATA', 'torisetsu_data');

/** 工説データ */
define('COLUMN_NAME_KOUSETSU_DATA', 'kousetsu_data');

/** 分解図データ */
define('COLUMN_NAME_BUNKAI_DATA', 'bunkai_data');

/** シャワーデータ */
define('COLUMN_NAME_SHOWER_DATA', 'shower_data');

/** 購入フラグ */
define('COLUMN_NAME_BUY_STATUS', 'buy_status');

/** カタログへのリンク */
define('COLUMN_NAME_CATALOG_LINK', 'catalog_link');

/** バリエーション親品番 */
define('COLUMN_NAME_PARENT_VARIATION', 'parent_variation');

/** 商品イメージ画像 */
define('COLUMN_NAME_ITEM_IMAGE', 'item_image');

/** pdf作成ステータス */
define('COLUMN_NAME_PDF_STATUS', 'pdf_status');

/** バリエーション表示順 */
define('COLUMN_NAME_VARIATION_NO', 'variation_no');

/** 検索ワード */
define('COLUMN_NAME_SEARCH_WORD', 'search_word');

/** 分岐金具1 */
define('COLUMN_NAME_BUNKI_KANAGU_1', 'bunki_kanagu_1');

/** 分岐金具2 */
define('COLUMN_NAME_BUNKI_KANAGU_2', 'bunki_kanagu_2');

/** 分岐金具3 */
define('COLUMN_NAME_BUNKI_KANAGU_3', 'bunki_kanagu_3');

/** 販売時期 */
define('COLUMN_NAME_SELL_TIME', 'sell_time');

/** 代替品 */
define('COLUMN_NAME_SUB_ITEM', 'sub_item');

/** 本体取付穴 */
define('COLUMN_NAME_SUNPOU', 'sunpou');

/** ピッチ */
define('COLUMN_NAME_PITCH', 'pitch');

/** シャワー取付穴 */
define('COLUMN_NAME_SHOWER_SUNPOU', 'shower_sunpou');

/** 商品テーブル */
define('TABLE_NAME_ITEM', 'item');

/**
 * 部品系（pdf）
 */

/** pdf作成用部品図テーブル */
define('TABLE_NAME_PDF_PARTS_LIST', 'onetime_pdf_parts');

/** pdf作成用商品テーブル */
define('TABLE_NAME_PDF_ITEM', 'onetime_pdf_item');

/**
 * メッセージ
 */
/** システムステータスエラーメッセージ */
define('ERROR_MSG_STATUS_ERROR', 'PDF作成中のため実行できません。<br>');

/** csvデータ数不足エラーメッセージ */
define('ERROR_MSG_NODATA', 'CSVファイルにデータがありません。<br>');

/** csvファイルエラーメッセージ */
define('ERROR_MSG_FILE_ERROR', 'CSVファイルを取り込んでください。<br>');

/** csvカラム数エラーメッセージ */
define('ERROR_MSG_COLUMN_ERROR', 'CSVファイルの項目数が一致しません。<br>');

/** csv型チェックエラーメッセージ（required） */
define('ERROR_MSG_FORM_ERROR', '必須入力項目です。');

/** csv型チェックエラーメッセージ（numeric/digit/pnumeric） */
define('ERROR_MSG_NUM_ERROR', '数値のみ有効な項目です。');

/** csvチェック成功メッセージ */
define('MSG_CHECK_OK', 'CSV取込可能データです。<br>');

/** DBエラーメッセージ */
define('ERROR_MSG_DB', 'データベースエラーが発生しました。<br>');

/** pdfデータ作成失敗メッセージ（商品データなし） */
define('ERROR_MSG_NO_ITEM', 'pdfファイル作成対象データがありません。<br>');

/** pdfデータ作成失敗メッセージ（部品データなし） */
define('ERROR_MSG_NO_PARTS', '部品データがありません。品番：');

/** pdfデータ作成失敗メッセージ */
define('ERROR_MSG_MAKE_PDF', 'pdfファイルの作成に失敗しました。<br>');

/** 実行結果メッセージ（成功） */
define('RESULT_MSG_OK', '成功<br>');

/** 実行結果メッセージ（失敗） */
define('RESULT_MSG_NG', '失敗<br>');

/** PDF作成時間設定エラーメッセージ */
define('PDF_TIME_NG', 'PDF作成開始時間が不正です。<br>');

/** PDF作成時間設定エラーメッセージ2 */
define('PDF_TIME_NG_KAKO', 'PDF作成開始時間が過去の日付です。<br>');

/** 未入力エラーメッセージ */
define('MINYURYOKU_NG', '未入力項目があります。<br>');

/** PDF作成完了エラーメッセージ */
define('PDF_FINISH_NG', '作成済みのデータがあります。データ更新もしくはデータ破棄を行って下さい。<br>');

/** PDF作成開始時間設定変更失敗 */
define('ERROR_SET_PDF_TIME', '作成開始時間の変更に失敗しました。<br>');

/** PDF作成開始時間設定変更成功 */
define('MESSAGE_SET_PDF_TIME', '作成開始時間の変更に成功しました。<br>');

/** PDF作成中止失敗 */
define('ERROR_STOP_PDF', '作成開始時間の変更に失敗しました。<br>');

/** PDF作成中止成功 */
define('MESSAGE_STOP_PDF', '作成開始時間の変更に成功しました。<br>');

/** PDF作成予約解除失敗 */
define('ERROR_UNSET_PDF', 'PDF作成予定のキャンセルに失敗しました。<br>');

/** PDF作成予約解除成功 */
define('MESSAGE_UNSET_PDF', 'PDF作成予定をキャンセルしました。<br>');

/** 部品データ更新失敗 */
define('MESSAGE_FAIL_UPDATE_PARTS', '部品データの更新に失敗しました。品番：');

/** 商品データ更新失敗 */
define('MESSAGE_FAIL_UPDATE_ITEM', '商品データの更新に失敗しました。品番：');

/** 画面初期表示失敗 */
define('MESSAGE_FAIL_PAGE_INITIAL', '画面の初期表示に失敗しました。<br>');

/**
 * 商品ステータス
 */
/** 商品ステータスCSVカラム数 */
define('HEADER_COUNT_ITEM_STATUS', '37');

/** 品番 */
define('ITEM_ID_COLUMN_ITEM_STATUS', '0');

/** JIS */
define('JIS_COLUMN_ITEM_STATUS', '1');

/** 第三者認証登録品 */
define('NINSYO_TOUROKU_COLUMN_ITEM_STATUS', '2');

/** 第三者認証品 */
define('NINSYO_ITEM_COLUMN_ITEM_STATUS', '3');

/** 寒冷地用 */
define('KANREITI_COLUMN_ITEM_STATUS', '4');

/** 逆止弁付 */
define('GYAKUSHIBEN_COLUMN_ITEM_STATUS', '5');

/** 保証書 */
define('HOSYOUSYO_COLUMN_ITEM_STATUS', '6');

/** 新商品 */
define('NEW_ITEM_COLUMN_ITEM_STATUS', '7');

/** スーパーサーモ */
define('SUPER_SARMO_COLUMN_ITEM_STATUS', '8');

/** eシャワー */
define('E_SHOWER_COLUMN_ITEM_STATUS', '9');

/** やさしさ推奨品 */
define('YASASHISA_COLUMN_ITEM_STATUS', '10');

/** 定量止水 */
define('OYUPITA_COLUMN_ITEM_STATUS', '11');

/** サーモスタット式水栓 */
define('SARMO_STAT_COLUMN_ITEM_STATUS', '12');

/** 自閉式 */
define('JIHEISHIKI_COLUMN_ITEM_STATUS', '13');

/** ミキシング式 */
define('MIXING_COLUMN_ITEM_STATUS', '14');

/** 一次止水付 */
define('SHISUITSUKI_COLUMN_ITEM_STATUS', '15');

/** ソーラー用 */
define('SORLAR_COLUMN_ITEM_STATUS', '16');

/** セラミックシングル */
define('SELAMIC_SINGLE_COLUMN_ITEM_STATUS', '17');

/** eレバー水栓 */
define('E_LEVER_COLUMN_ITEM_STATUS', '18');

/** マルチ水栓 */
define('MALUTI_COLUMN_ITEM_STATUS', '19');

/** センサー付 */
define('SENSOR_COLUMN_ITEM_STATUS', '20');

/** スーパーシングル */
define('SUPER_SINGLE_COLUMN_ITEM_STATUS', '21');

/** シングルレバー式水栓 */
define('SINGLE_LEVER_COLUMN_ITEM_STATUS', '22');

/** NSFシャワー */
define('NSF_SHOWER_COLUMN_ITEM_STATUS', '23');

/** 泡沫吐水 */
define('AWA_COLUMN_ITEM_STATUS', '24');

/** 省施工仕様 */
define('SHOSEKOU_COLUMN_ITEM_STATUS', '25');

/** 上施工方式（省施工仕様） */
define('JOUSEKOU_COLUMN_ITEM_STATUS', '26');

/** 浄水器付 */
define('JOSUIKI_COLUMN_ITEM_STATUS', '27');

/** エコこま水栓 */
define('ECO_KOMA_COLUMN_ITEM_STATUS', '28');

/** プチエコ水栓 */
define('PUCHI_ECO_COLUMN_ITEM_STATUS', '29');

/** 緊急止水機能付 */
define('STOP_WATER_COLUMN_ITEM_STATUS', '30');

/** 節湯A1 */
define('SETSUYU_A1_COLUMN_ITEM_STATUS', '31');

/** 節湯B1 */
define('SETSUYU_B1_COLUMN_ITEM_STATUS', '32');

/** 節湯C1 */
define('SETSUYU_C1_COLUMN_ITEM_STATUS', '33');

/** 節湯A */
define('SETSUYU_A_COLUMN_ITEM_STATUS', '34');

/** 節湯B */
define('SETSUYU_B_COLUMN_ITEM_STATUS', '35');

/** 節湯AB */
define('SETSUYU_AB_COLUMN_ITEM_STATUS', '36');

/**
 * 商品アイコンマスタ
 */
/** 商品ステータスマスタCSVカラム数 */
define('HEADER_COUNT_STATUS_MASTER', '3');

/** ステータスID */
define('STATUS_ID_COLUMN_STATUS_MASTER', '0');

/** ステータス名 */
define('STATUS_NAME_COLUMN_STATUS_MASTER', '1');

/** ステータスアイコン */
define('STATUS_ICON_COLUMN_STATUS_MASTER', '2');

/** 商品アイコンテーブル */
define('TABLE_NAME_STATUS_LIST', 'item_icon');

/** アイコンID */
define('COLUMN_NAME_STATUS_ID', 'icon_id');

/** アイコン名 */
define('COLUMN_NAME_STATUS_NAME', 'icon_name');

/** アイコンファイル名 */
define('COLUMN_NAME_ICON', 'icon_file');

/**
 * システムステータス
 */
/** システムステータステーブル */
define('TABLE_NAME_SYSTEM_STATUS', 'system_status');

/** システムステータス */
define('COLUMN_NAME_SYSTEM_STATUS', 'status');

/** pdf作成開始時間 */
define('COLUMN_NAME_PDF_TIME', 'pdf_time');

/** 商品データ取込画面ステータス */
define('COLUMN_NAME_ITEM_DISP_STATUS', 'item_disp_status');

/** 商品ステータスマスターデータ取込画面ステータス */
define('COLUMN_NAME_STATUS_MASTER_DISP_STATUS', 'status_master_disp_status');

/** 商品ステータスデータ取込画面ステータス */
define('COLUMN_NAME_STATUS_DISP_STATUS', 'status_disp_status');

/** カテゴリー取込画面ステータス */
define('COLUMN_NAME_CATEGORY_DISP_STATUS', 'category_disp_status');

/** 部品データ取込画面ステータス */
define('COLUMN_NAME_PARTS_DISP_STATUS', 'parts_disp_status');

/** システムステータス(0：通常) */
define('SYSTEM_STATUS_NORMAL', '0');

/** システムステータス(1：PDF作成待ち) */
define('SYSTEM_STATUS_PDF_WAIT', '1');

/** システムステータス(2：PDF作成中) */
define('SYSTEM_STATUS_PDF_MAKE', '2');

/** システムステータス(3：PDF作成完了) */
define('SYSTEM_STATUS_PDF_FINISH', '3');

/** システムステータス(4：PDF作成中止) */
define('SYSTEM_STATUS_PDF_STOP', '4');

/** システムステータス(0：通常) */
define('SYSTEM_STATUS_NORMAL_VAL', 'なし');

/** システムステータス(1：PDF作成待ち) */
define('SYSTEM_STATUS_PDF_WAIT_VAL', 'PDF作成待ち');

/** システムステータス(2：PDF作成中) */
define('SYSTEM_STATUS_PDF_MAKE_VAL', 'PDF作成中');

/** システムステータス(3：PDF作成完了) */
define('SYSTEM_STATUS_PDF_FINISH_VAL', 'PDF作成完了');

/** システムステータス(4：PDF作成中断) */
define('SYSTEM_STATUS_PDF_STOP_VAL', 'PDF作成中断');

/*----------------------------
 ログインユーザ
-----------------------------*/
/** ユーザテーブル */
define('TABLE_NAME_USER', 'user');

/** カラム名：ユーザ名 */
define('COLUMN_NAME_USER_NAME', 'user_name');

/** カラム名：パスワード */
define('COLUMN_NAME_PASS_WORD', 'pass_word');

/** 配列番号：ユーザ名(0) */
define('ARRAY_NO_USER_NAME', '0');

/** 配列番号：パスワード(1) */
define('ARRAY_NO_PASS_WORD', '1');

/*----------------------------
 csv出力
-----------------------------*/
/** csvファイル名（商品） */
define('CSV_FILE_NAME_ITEM', 'item_master.csv');

/** csvファイル名（部品） */
define('CSV_FILE_NAME_PARTS', 'parts_master.csv');

/** csvファイル名（商品ステータス） */
define('CSV_FILE_NAME_ITEM_STATUS', 'item_status.csv');

/** csvファイル名（商品ステータスマスタ） */
define('CSV_FILE_NAME_ITEM_STATUS_MASTER', 'item_status_master.csv');

/** csvファイル名（カテゴリマスタ） */
define('CSV_FILE_NAME_CATEGORY_MASTER', 'category_master.csv');

/** csvファイル名（PDF作成用部品csvファイル） */
define('CSV_FILE_NAME_ONETIME_PARTS', 'onetime_parst.csv');

/*----------------------------
  アップロードディレクトリ
-----------------------------*/
/** 客先ファイルアップロードフォルダ */
define('UPLOAD_FOLDER', '/htdocs/products/upload/');

/** pdfファイル保存ルートフォルダ */
define('PDF_ROOT_FOLDER', '/htdocs/products/system/savepdf/');

/** pdfファイルバックアップフォルダ */
define('BUCKUP_PDF_FOLDER', 'backup_bunkai/');

/** pdfファイル一次保存フォルダ */
define('ONETIME_PDF_FOLDER', 'onetime_bunkai/');

/** pdfファイル保存フォルダ */
define('PDF_FOLDER', 'bunkai');

/** csvアップロードフォルダ */
define('CSV_FOLDER', '../../save_csv/');

?>