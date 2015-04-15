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
<dt><a href="#">浴室用水栓(334)</a></dt>
<dt><a href="#">浴室用水栓(334)</a></dt>
<dt><a href="#">浴室用水栓(334)</a></dt>
<dt><a href="#">浴室用水栓(334)</a></dt>
</dl>