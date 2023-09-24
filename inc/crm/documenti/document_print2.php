<?php
ob_start();
require_once(WPsCRM_DIR.'/inc/classes/html2pdf-4.4.0/html2pdf.class.php');
if ( ! defined( 'ABSPATH' ) ) exit;
$content=WPsCRM_generate_document_HTML($_GET['id_invoice']);
ob_end_clean();
ob_clean();
$html2pdf = new HTML2PDF('P', 'A4', 'it');
//$html2pdf->setModeDebug();
$html2pdf->pdf->SetDisplayMode('fullpage');
try
{
	$html2pdf->writeHTML($content, false);
	$html2pdf->Output('invoice.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
?>
