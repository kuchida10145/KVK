<?php
/**
 * PDF用プログラム
 *
 */
require(dirname(__FILE__).'/../../plugin/pdf/tcpdf/tcpdf.php');
require(dirname(__FILE__).'/../../plugin/pdf/fpdi/fpdi.php');
class Pdf{

	/**
	 * @param array $pdfArray ＰＤＦ作成部品データ
	 * @return bool
	 */
	public function makePdf($pdfArray){

		//PDFセーブパス
		$save_path = PDF_SAVE_DIR;

		//画像イメージ
		$image = '';

		$objPdf = new FPDI('L','mm','A4');
		$objPdf->setPrintHeader(false);
		$objPdf->AddPage();
		$objPdf->setSourceFile(PDF_TEMPLATE);
		$iIndex = $objPdf->importPage(1);
		$objPdf->useTemplate($iIndex);
		$objPdf->SetFont("kozgopromedium", "", 16);

		if(!is_array($pdfArray)){
			return false;
		}
		//画像取得

		$file_name = getParam($pdfArray[0],'file_name');
		if($file_name == ''){
			return false;
		}

		//分解図名
		$name = getParam($pdfArray[0],'item_id');
		if($name == ''){
			return false;
		}
		$objPdf->SetXY(45.0, 21.0);
		$objPdf->Write(4,$name);

		$objPdf->SetFont("kozgopromedium", "", 5);

		$width_ar =array(
				'parts_no'  =>5,
				'parts_id'   =>20,
				'parts_name'=>40,
				'price_zei' =>24,
				'note'      =>15);
		$objPdf->SetXY(180.0, 30.0);
		$line = 3;

		$objPdf->Cell($width_ar['parts_no'],  $line, '番号', 1,0, 'C');
		$objPdf->Cell($width_ar['parts_id'],   $line, '品番', 1,0, 'C');
		$objPdf->Cell($width_ar['parts_name'],$line, '品名', 1,0, 'C');
		$objPdf->Cell($width_ar['price_zei'], $line, '希望小売価格(税込み)', 1,0, 'C');
		$objPdf->Cell($width_ar['note'],      $line, '備考', 1,1, 'C');




		foreach($pdfArray as $key => $parts){
			$objPdf->SetX(180.0);
			if($parts['parts_no'] != ''){
				$price = '￥'.number_format($parts['price']).'(税込￥'.number_format($parts['price_zei']).')';
				$objPdf->Cell($width_ar['parts_no']  , $line, $parts['parts_no'] , 1,0, 'C');
				$objPdf->Cell($width_ar['parts_id']   , $line, $parts['parts_id'] , 1);
				$objPdf->Cell($width_ar['parts_name'], $line, $parts['parts_name'] , 1);
				$objPdf->Cell($width_ar['price_zei'] , $line, $price , 1);
				$objPdf->Cell($width_ar['note'] ,      $line, $parts['note'] , 1,1);
			}
			else{
				$objPdf->Cell(104, $line, $parts['parts_name'], 1,1);
			}
		}



		$objPdf->Image(PDF_IMAGE_DIR.'/'.$file_name.'.png', 20, 30,150);

		$objPdf->Output(PDF_SAVE_DIR.'/'.$file_name.'.pdf', 'F');

		return true;
	}
}