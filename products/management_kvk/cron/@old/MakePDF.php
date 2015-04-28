#!/usr/local/bin/php
<?php
include(dirname(__FILE__).'/../../Test.php');
class MakePDF extends Test{

	public function testTxt() {
		$outfile = dirname(__FILE__) . "/output.txt";
		file_put_contents($outfile, "test");
	}
}
echo "1";
$makePDF = new MakePDF();
echo "2";
$makePDF->testTxt();
echo "3";
$makePDF->makeTxt();
?>
