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
<dt><a href="#">商品検索の方法</a></dt>
<dt><a href="#">電子カタログ</a></dt>
<dt><a href="#">以前の商品</a></dt>
<dt><a href="#">水栓の仕組み</a></dt>
<dt><a href="#">用語集</a></dt>
<dt><a href="#">メンテナンス情報</a></dt>
<dt><a href="#">業者様向けメンテナンス情報</a></dt>
<dt><a href="#">よくあるご質問（Q＆A）</a></dt>
<dt><a href="#">水まわりのご提案</a></dt>
<dt><a href="#">リフォーム水栓</a></dt>
<dt><a href="#">水栓のお手入れ方法</a></dt>
<dt><a href="#">主要部品一覧</a></dt>
<dt><a href="#">お近くの支社・営業所</a></dt>
</dl>