

<?php


	echo "<li id=\"gn1\"><a href=\"/products/\">ホーム</a></li>";
	
	echo $cls_product->getGlobalMenu();

	echo "<li id=\"gn8\"><a href= \"".$_SERVER[“SERVER_NAME”]."/contact/index.html\" ?>>お問い合わせ</a></li>";
?>