<?php
include(dirname(__FILE__).'/system/Management.php');

$manager = Management::getInstance();
$manager->setCore('pdf');


$param = array(
	'parts_no'  =>'00000000000000',
	'item_id'   =>'11111111111111',
	'parts_id'   =>'00000000000000',
	'parts_name'=>'テストパーツテストパーツテストパーツ',
	'price_zei' =>'120000',
	'price' =>'120000',
	'note' =>'',
	'file_name'=>'test150',
);

$data =array();
for($i = 1;$i <= 50; $i++){
	$param['parts_no'] = $i;
	$data[] = $param;
	if($i == 40){
		$data[] = array(
			'parts_no'  =>'',
			'item_id'   =>'',
			'parts_id'   =>'',
			'parts_name'=>'テストパーツテストパーツテストパーツあああ',
			'price_zei' =>'',
			'price' =>'',
			'note' =>'',
			'file_name'=>'test150',
		);
	}
}

$manager->pdf->makePdf($data);