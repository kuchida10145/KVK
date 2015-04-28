#!/usr/local/bin/php
<?php
include(dirname(__FILE__).'/../../Page.php');
class MakePDF extends Page{

	public function testTxt() {
		$outfile = dirname(__FILE__) . "/output.txt";
		file_put_contents($outfile, "test");
	}
}

$makePDF = new MakePDF();
$makePDF->testTxt();
?>
