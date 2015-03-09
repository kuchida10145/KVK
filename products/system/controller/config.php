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

/** csv拡張子 */
define('CSV_EXTENTION', 'csv');

/** 削除フラグ（データ非表示フラグ） */
define('DELETE_FLG', '1');

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

/** 取込結果メッセージ取得key */
define('KEY_TORIKOMI_MESSAGE', 'torikomiMessage');

/** 親カテゴリテーブル */
define('TABLE_NAME_PARENT_CATEGORY', 'parent_category');

/** 親カテゴリID */
define('COLUMN_NAME_PARENT_ID', 'parent_id');

/** 親カテゴリ名 */
define('COLUMN_NAME_PARENT_NAME', 'parent_name');

/** 親カテゴリ画像 */
define('COLUMN_NAME_PARENT_IMAGE', 'parent_image');

/** 表示ステータス */
define('COLUMN_NAME_VIEW_STATUS', 'view_status');

/** 子カテゴリテーブル */
define('TABLE_NAME_CHILD_CATEGORY', 'child_category');

/** 子カテゴリID */
define('COLUMN_NAME_CATEGORY_ID', 'category_id');

/** 子カテゴリ名 */
define('COLUMN_NAME_CATEGORY_NAME', 'category_name');

/** 子カテゴリ画像 */
define('COLUMN_NAME_CATEGORY_IMAGE', 'category_image');

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

/** 実行結果メッセージ（成功） */
define('RESULT_MSG_OK', '成功<br>');

/** 実行結果メッセージ（失敗） */
define('RESULT_MSG_NG', '失敗<br>');

/*----------------------------
  アップロードディレクトリ
-----------------------------*/


?>