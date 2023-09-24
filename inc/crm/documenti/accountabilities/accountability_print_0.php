<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$sql="select fk_articoli, n_riga, prezzo, totale, qta, codice, descrizione, sconto, tipo, iva from $dd_table where fk_documenti=$ID order by $dd_table.n_riga";
$qd=$wpdb->get_results( $sql, ARRAY_A);
$body="";
if ($qd)
{
	$_header="<th class=\"WPsCRM_items_header WPsCRM_cod\">".__('Code','cpsmartcrm')."</th><th class=\"WPsCRM_items_header WPsCRM_desc\">".__('Description','cpsmartcrm')."</th><th class=\"WPsCRM_items_header WPsCRM_qty\">".__('Quantity','cpsmartcrm')."</th><th class=\"WPsCRM_items_header WPsCRM_price\">".__('Price','cpsmartcrm')."</th><th class=\"WPsCRM_items_header WPsCRM_discount\">".__('Discount','cpsmartcrm')."</th><th class=\"WPsCRM_items_header WPsCRM_total\">".__('Total','cpsmartcrm')."</th>";
	$t_articoli='
		<table class="table table-bordered WPsCRM_document-table"><thead>
		<tr class="WPsCRM_header-row">'.$_header.'
		</tr>
		</thead><tbody>';

	$totale_imposta=0;
	$totale_righe=0;
	$index_riga=0;
	foreach ( $qd as $rigaa )  	{
		$tipo_riga=$rigaa["tipo"];
		$code="";
		$art_id=$rigaa["fk_articoli"];

		$code=$rigaa["codice"];
		$descrizione=$rigaa["descrizione"];
		$descrizione_length=strlen($descrizione);
		$prezzo=$rigaa["prezzo"];
		$iva=$rigaa["iva"];
		$lordo=$rigaa["totale"];
		$sconto=$rigaa["sconto"];

		$pre_sc = $prezzo - ($prezzo * $sconto / 100);
		$tot_riga=$pre_sc*$rigaa["qta"];

		if ($tipo_riga==4)
		{
		    $totale_rimborso+=$tot_riga;
		}
		else
		{
		    $imposta=$tot_riga*$iva/100;
		    $totale_imposta+=$imposta;
		    $totale_righe+=$tot_riga;
		}
		$tot_riga = sprintf("%01.2f", $tot_riga);
		if ($tipo_riga==3)
		{
			$body.='<tr class="_item"><td colspan="6">'.$descrizione.'</td>';
		}
		elseif ($tipo_riga!=4)
		{
			$prezzo=number_format($prezzo, 2, ',', '.');
			$tot_riga_lordo=$lordo * $rigaa["qta"];
			$lordo=number_format($lordo, 2, ',', '.');

			$body.='<tr id="riga-'.$index_riga.'" class="WPsCRM_item" data-net="'.$prezzo.'" data-desc-lenght="'.$descrizione_length.'" data-gros="'.$lordo.'" data-totalgros="'.$tot_riga_lordo.'" data-totalnet="'.number_format($tot_riga, 2, ',', '.').'">
				<td class="WPsCRM_cod">'.$code.'</td>
				<td class="WPsCRM_desc">'.$descrizione.'</td>
				<td class="WPsCRM_qty" align="right">'.$rigaa["qta"].'</td>
				<td class="WPsCRM_price" align="right">'.WPsCRM_get_currency()->symbol.' <span class="row_amount">'.$prezzo.'</span></td>
				<td class="WPsCRM_discount" align="right">'.$sconto.'</td>
				<td class="WPsCRM_total" align="right">'.WPsCRM_get_currency()->symbol.' <span class="tot_riga">'.number_format($tot_riga, 2, ',', '.').'</span></td>
				</tr>';
		}
		if($index_riga > 3 && $index_riga % 3 == int){
			//$body.='<td class="page-break"></td>';
		}

		$index_riga ++;
	}
	$t_articoli.=$body.'</tbody></table>';
}



if ($totale_righe)
{
	$totale_righe = sprintf("%01.2f", $totale_righe);
	$totale_imponibile=$riga["totale_imponibile"];
	$totale_imposta=$riga["totale_imposta"];
	$totale_netto=$riga["totale_netto"];
	$tab_tot="
  	<tr class='total_net'><td>".__("Total Net",'cpsmartcrm')."</td><td align='right'>".WPsCRM_get_currency()->symbol." ".number_format($totale_imponibile, 2, ',', '.')."</td></tr>
	<tr class='total_gros' style='display:none'><td>".__("Price",'cpsmartcrm')."</td><td align='right'>".WPsCRM_get_currency()->symbol." ".number_format($totale_netto, 2, ',', '.')."</td></tr>
  	<tr class=\"print_tax\"><td >".__("Total Tax",'cpsmartcrm')."</td><td align='right'>".WPsCRM_get_currency()->symbol." ".number_format($totale_imposta, 2, ',', '.')."</td></tr>";
	if (isset($totale_rimborso) && $totale_rimborso!=0)
		$tab_tot.="<tr class=\"WPsCRM_rowRefund\"><td>".__("Refund",'cpsmartcrm')."</td><td align='right'>".WPsCRM_get_currency()->symbol." ".number_format($totale_rimborso, 2, ',', '.')."</td></tr>
  	";
	$tab_tot.="<tr class=\"WPsCRM_grandTotal\"><td><h4>".__("Grand Total",'cpsmartcrm')."</h4></td><td align='right'><h4>".WPsCRM_get_currency()->symbol." ".number_format($totale_netto, 2, ',', '.')."</h4></td></tr>
  ";
}
