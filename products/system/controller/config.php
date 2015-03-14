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

define('DB_NAME','kvk');
define('DB_USER','root');
define('DB_PASS','yuki10145');
define('DB_HOST','localhost');
//define('DB_HOST','203.138.100.12');
define('DB_CHARSET','utf8');

/*
|--------------------------------------------------------------------------
| 定数宣言
|--------------------------------------------------------------------------
 */

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
define('HEADER_COUNT_PARTS', '8');

/** カラム番号：ページ */
define('PAGE_COLUMN_PARTS', '0');

/** カラム番号：番号（部品表示順） */
define('NO_COLUMN_PARTS', '1');

/** カラム番号：品番 */
define('PARTS_ID_COLUMN_PARTS', '2');

/** カラム番号：希望小売価格（税抜き） */
define('PRICE_COLUMN_PARTS', '3');

/** カラム番号：希望小売価格（税込み） */
define('PRICE_ZEI_COLUMN_PARTS', '4');

/** カラム番号：備考 */
define('NOTE_COLUMN_PARTS', '5');

/** カラム番号：ファイル名 */
define('FILE_COLUMN_PARTS', '6');

/** カラム番号：削除フラグ */
define('DELETE_COLUMN_PARTS', '7');

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
define('HEADER_COUNT_ITEM', '23');

/** カラム番号：品番 */
define('ITEM_ID_COLUMN_ITEM', '0');

/** カラム番号：品名 */
define('ITEM_NAME_COLUMN_ITEM', '1');

/** カラム番号：写真 */
define('ITEM_ID_COLUMN_ITEM', '2');

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

/** カラム番号：購入フラグ */
define('BUY_STATUS_COLUMN_ITEM', '8');

/** カラム番号：代替品 */
define('DAIGAE_COLUMN_ITEM', '9');

/** カラム番号：価格 */
define('PRICE_COLUMN_ITEM', '10');

/** カラム番号：価格（税込） */
define('PRICE_ZEI_COLUMN_ITEM', '11');

/** カラム番号：販売時期 */
define('SELL_KIKAN_COLUMN_ITEM', '12');

/** カラム番号：取付寸法 */
define('SUNPOU_COLUMN_ITEM', '13');

/** カラム番号：備考 */
define('NOTE_COLUMN_ITEM', '14');

/** カラム番号：商品画像 */
define('ITEM_IMAGE_COLUMN_ITEM', '15');

/** カラム番号：バリエーション名 */
define('VARIATION_NAME_COLUMN_ITEM', '16');

/** カラム番号：バリエーション表示順 */
define('VARIATION_NO_COLUMN_ITEM', '17');

/** カラム番号：カタログ年度 */
define('CATALOG_YEAR_COLUMN_ITEM', '18');

/** カラム番号：カタログページ */
define('CATALOG_PAGE_COLUMN_ITEM', '19');

/** カラム番号：検索ワード */
define('SEARCH_WORD_COLUMN_ITEM', '20');

/** カラム番号：削除 */
define('DELETE_COLUMN_ITEM', '21');

/** カラム番号：カテゴリID */
define('CATEGORY_ID_COLUMN_ITEM', '22');

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
define('COLUMN_NAME_PARENT_VARIATION', 'parent_valiation');

/** 商品イメージ画像 */
define('COLUMN_NAME_ITEM_IMAGE', 'item_image');

/** pdf作成ステータス */
define('COLUMN_NAME_PDF_STATUS', 'pdf_status');

/** バリエーション表示順 */
define('COLUMN_NAME_VARIATION_NO', 'valiation_no');

/** 商品テーブル */
define('TABLE_NAME_ITEM', 'item');

/**
 * 部品系（pdf）
 */

/** pdf部品図テーブル */
define('TABLE_NAME_PDF_PARTS_LIST', 'pdf_parts_list');

/** pdf商品テーブル */
define('TABLE_NAME_PDF_ITEM', 'pdf_item');

/**
 * メッセージ
 */
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

/*----------------------------
  アップロードディレクトリ
-----------------------------*/


?>