#!/usr/local/bin/php
<?php
	$outfile = dirname(__FILE__) . "/output_test.txt";
	file_put_contents($outfile, $outfile . "\n", FILE_APPEND);
	
	require_once(dirname(__FILE__) . "/../crontest/define.php");
	
	if (isset($_SERVER['HTTP_HOST'])) {
		$http_host = "ari";
	} else {
		$http_host = "nashi";
	}
	file_put_contents($outfile, "{$http_host} End. \n", FILE_APPEND);
?>
