#!/usr/local/bin/php
<?php
	$outfile = dirname(__FILE__) . "/output.txt";
	
	file_put_contents($outfile, "test");
?>
