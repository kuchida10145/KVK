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

/** カテゴリ */
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
/** DBチェックメッセージ取得key */
define('KEY_DB_CHECK_MESSAGE', 'dataDBCheckMessage');
/** エラーメッセージ取得key */
define('KEY_ERROR_MESSAGE', 'errorMessage');
define('ERROR_MSG_NODATA', 'csvファイルにデータがありません。');
define('ERROR_MSG_HEADER_COUNT_CATEGORY', 'csvファイルの項目数が足りません。');
/*----------------------------
  アップロードディレクトリ
-----------------------------*/


?>