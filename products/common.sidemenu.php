<?php
/**
 * サイドメニューを出力
 */
if( !isset($key) ){
	$key=NULL;
}
if( !isset($value) ){
	$value=NULL;
}
?>
<h2 class="sidecategory"><strong>商品カテゴリー</strong></h2>
<dl>
<?php
// サイドメニューを取得して出力
echo $cls_product->getSidemenu( $key,$value );
?>
</dl>

<h2 class="sideguide"><strong>ガイド</strong></h2>
<dl>
<dt><a href= "/e_catalog/search/index.html" >商品検索の方法</a></dt>
<dt><a href= "/e_catalog/index.html" >電子カタログ</a></dt>
<dt><a href= "/e_catalog/oldsearch/index.html" >以前の商品</a></dt>
<dt><a href= "/e_catalog/point/index.htm" >水栓の仕組み</a></dt>
<dt><a href= "/e_catalog/word/index.html" >用語集</a></dt>
<dt><a href= "/support/maintenance/index.html" >メンテナンス情報</a></dt>
<dt><a href= "/support/professional/index.html" >業者様向けメンテナンス情報</a></dt>
<dt><a href= "/support/maintenance/faq/index.html" >よくあるご質問（Q＆A）</a></dt>
<dt><a href= "/support/example/index.html" >水まわりのご提案</a></dt>
<dt><a href= "/support/reform/index.htm" >リフォーム水栓</a></dt>
<dt><a href= "/support/maintenance/after/index.html" >水栓のお手入れ方法</a></dt>
<dt><a href= "/support/shop/parts.html" >主要部品一覧</a></dt>
<dt><a href= "/corporate/map/network.html" >お近くの支社・営業所</a></dt>
</dl>