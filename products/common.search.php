<div id="headersearch">
<i class="icon-search"></i>品番検索
<form action="/products/search/" method="get">
<select name="category_id" class="srcform2">
<option label="すべての商品" value="">全ての商品</option>
<?php echo $cls_product->getSearchMenu();?>
</select>
<input name="word" value="<?php if( isset($word) ):echo htmlspecialchars($word);endif; ?>" type="text"  />
<input type="submit" value="送信する" id="headsearhbtn" />
</form>
</div>