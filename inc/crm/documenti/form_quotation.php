<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$delete_nonce = wp_create_nonce( "delete_document" );
$update_nonce= wp_create_nonce( "update_document" );
$scheduler_nonce= wp_create_nonce( "update_scheduler" );

global $document;
$generalOptions=get_option('CRM_general_settings');
$documentOptions=get_option('CRM_documents_settings');
//echo $documentOptions['default_vat'];
if (isset($_GET["id_invoice"]) && ($ID=$_GET["id_invoice"]))
{

	
//	include(WPsCRM_DIR."/inc/crm/mpdf/mpdf.php");

//	$stylesheet = file_get_contents(WPsCRM_DIR.'/css/documents/pdf_documents.css');

//	include(WPsCRM_DIR."/inc/templates/print_invoice.php");
}
else
{

$ID=isset($_REQUEST["ID"])?$_REQUEST["ID"]:0;
//$type=$_REQUEST["type"];
$d_table=WPsCRM_TABLE."documenti";
$dd_table=WPsCRM_TABLE."documenti_dettaglio";
$c_table=WPsCRM_TABLE."clienti";
if ($ID)
{
	$sql="select * from $d_table where id=$ID";
    $riga=$wpdb->get_row($sql, ARRAY_A);
	$type=$riga["tipo"];
	$data=WPsCRM_culture_date_format($riga["data"]);
	$data_scadenza=WPsCRM_culture_date_format($riga["data_scadenza"]);
	$oggetto=$riga["oggetto"];
	$iva=$riga["iva"];
	$tot_imp=sprintf("%01.2f", $riga["totale_imponibile"]);
	$totale_imposta=sprintf("%01.2f", $riga["totale_imposta"]);
	$totale=$riga["totale"];
	$FK_contatti=$riga["FK_contatti"];
  if ($fk_clienti=$riga["fk_clienti"])
  {
  	$sql="select ragione_sociale, nome, cognome, indirizzo, cap, localita, provincia from $c_table where ID_clienti=".$fk_clienti;
    $rigac=$wpdb->get_row($sql, ARRAY_A);
  	$cliente=$rigac["ragione_sociale"]?$rigac["ragione_sociale"]:$rigac["nome"]." ".$rigac["cognome"];
	$cliente=stripslashes($cliente);
	$indirizzo=$rigac["indirizzo"];
	$cap=$rigac["cap"];
	$localita=$rigac["localita"];
	$provincia=$rigac["provincia"];
	$cod_fis=$rigac["cod_fis"];
	$p_iva=$rigac["p_iva"];
  }
  if ($riga["FK_contatti"])
  {
  	$sql="select concat(nome,' ', cognome) as contatto from ana_contatti where ID_contatti=".$riga["FK_contatti"];
    $rigac=$wpdb->get_row($sql, ARRAY_A);
  	$contatto=$rigac["contatto"];
  }
	$wpdb->update(
	  $dd_table,
	  array('eliminato' => 0),
	array('fk_documenti'=>$ID),
	  array('%d')
	);
	$sql="select * from $dd_table where fk_documenti=$ID order by n_riga";
    $qd=$wpdb->get_results( $sql, ARRAY_A);
}
else
{
	$data=date("d-m-Y");
	$oggetto=__("Quote","cpsmartcrm");
	$iva=$documentOptions['default_vat'];

	$FK_clienti=0;
	$FK_contatti=0;
}?>

<?php
//$where="FK_aziende=$ID_azienda";

?>
<script>
	var $format = "<?php echo WPsCRM_DATEFORMAT ?>";
	var $formatTime = "<?php echo WPsCRM_DATETIMEFORMAT ?>";
	var cliente = "<?php if (isset($cliente))echo $cliente ?>";
</script>
<form name="form_insert" action="<?php echo admin_url('admin.php?page=smart-crm&p=documenti/insert.php&type=1&ID='.$ID.'&security='.$update_nonce); echo isset($_REQUEST['layout']) ? "&layout=".$_REQUEST['layout'] : null?>" method="post" id="form_insert">
        <input type="hidden" name="num_righe" id="num_righe" value="">

        <h1 style="text-align:center"><?php _e('CREATE/EDIT QUOTE','cpsmartcrm')?> <i class="glyphicon glyphicon-send"></i></h1>
<div id="tabstrip">
    <ul>
        <li id="tab1"><?php _e('DOCUMENT','cpsmartcrm')?></li>
        <!--<li><?php _e('BODY','cpsmartcrm')?></li>-->
        <?php 

        //if($generalOptions['services']==1){?>
        <!--<li><?php _e('SERVICES/PRODUCTS','cpsmartcrm')?></li>-->
        <?php // } ?>
        <li  id="tab2" onclick="aggiornatot();"><?php _e('COMMENTS AND INTERNAL DATA','cpsmartcrm')?></li>
    </ul>
    <!--PRIMO TAB -->
    <div>
        
        <h4 class="page-header" style="margin: 10px 0 20px;"><?php _e('DOCUMENT DATA','cpsmartcrm')?><span class="crmHelp" data-help="document-data"></span>
	        <span style="float:right;margin-top: -7px;">
                <label class="col-sm-2 control-label"><?php _e('Number','cpsmartcrm')?></label><span class="col-sm-2"><input name="progressivo" class="form-control" data-placement="bottom" title="<?php _e('Number','cpsmartcrm')?>" value="<?php if (isset($riga)) echo $riga["progressivo"]?>" readonly disabled/>
	        </span></span></h4>

        <div class="row form-group">
	        <label class="col-sm-1 control-label"><?php _e('Date','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input name="data" id="data" class="_m" data-placement="bottom" title="<?php _e('Date','cpsmartcrm')?>" value="<?php echo $data?>" style="border:none"/>
	        </div>
            <!--<div class="col-sm-2 hide_sm"></div>-->
            <label class="col-sm-2 control-label" style="color:firebrick"><?php _e('Expiration date','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input type="text" class="_m" name="data_scadenza" value="<?php if (isset($data_scadenza)) echo $data_scadenza?>"  id='data_scadenza' style="border:none">
	        </div>
            <label class="control-label" style="margin-left:20px"><?php _e('Accepted','cpsmartcrm')?>?</label>
            <div class="col-sm-1">
                <input type="checkbox" name="pagato" value="1" <?php if (isset($riga)) echo $riga["pagato"]?"checked":""?>>
            </div>
        </div>
        <div class="row form-group">
	        <label class="col-sm-1 control-label"><?php _e('Subject','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input type="text" class="form-control col-md-10" name="oggetto" id="oggetto"  value="<?php if (isset($riga)) echo $oggetto?>">
	        </div>
                <!--<div class="col-sm-2 hide_sm"></div>-->
            <label class="col-sm-2 control-label"><?php _e('Reference','cpsmartcrm')?></label>
	        <div class="col-sm-4"><input type="text" class="form-control" name="riferimento" id="riferimento" maxlength='55' value="<?php if (isset($riga)) echo $riga["riferimento"]?>">
	        </div>
        </div>
        <div class="row form-group">
            <label class="col-sm-2 control-label"><?php _e('Notes','cpsmartcrm')?></label><br />
	        <div class="col-sm-8"><textarea id="annotazioni" style="width:100%" name="annotazioni" rows="5"><?php if (isset($riga)) echo stripslashes($riga["annotazioni"])?></textarea></div>
        </div>
    <h4 class="page-header"><?php _e('CUSTOMER DATA','cpsmartcrm')?><span class="crmHelp" data-help="customer-data"></span>
             <?php
    if (isset($fk_clienti))
    {
        echo "<a href=\"".admin_url('admin.php?page=smart-crm&p=clienti/form.php&ID='.$fk_clienti)."\" target=\"_blank\"><span class=\"header_customer\" >".$cliente."</span></a>";
    }$fk_clienti
             ?>
            <ul class="select-action" style="float:right;transform:scale(.8);background-color:transparent;margin-top:-10px;width:inherit">
		<?php if ($ID) {?>
        <li class="btn _edit _white btn-sm _flat"><i class="glyphicon glyphicon-pencil"></i>
            <b> <?php _e('EDIT CUSTOMER DETAILS','cpsmartcrm')?></b>
        </li>
        <li  style="display:none" class="btn btn-danger _quitEdit btn-sm _flat"><i class="glyphicon glyphicon-close"></i>
            <b> <?php _e('QUIT EDITING','cpsmartcrm')?></b>
        </li>
        <li class="btn"><i class="_tooltip glyphicon glyphicon-menu-right"></i></li>
        <li class="btn btn-info btn-sm _flat btn_todo" style="margin-left:10px" title="<?php _e('NEW TODO','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-tag"></i>
            <b> </b>
        </li>
        <li class="btn  btn-sm _flat btn_appuntamento" title="<?php _e('NEW APPOINTMENT','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-pushpin"></i>
            <b> </b>
        </li>
        <li class="btn btn-primary btn-sm _flat btn_activity" title="<?php _e('NEW ANNOTATION','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-option-horizontal"></i>
            <b> </b>
        </li>
            <?php do_action('WpsCRM_advanced_document_buttons');?>
<?php }?>        
    </ul>
        </h4>
		<div class="customer_data_partial" data-customer="<?php if (isset($fk_clinti)) echo $fk_clienti?>">
            <div class="row form-group">
                <label class="col-sm-1 control-label"><?php _e('Customer','cpsmartcrm')?></label>
                <div class="col-sm-3">
                    <?php
					if (isset($fk_clienti))
					{
						$disabled="disabled readonly";
					}
					else
						$disabled="";
                    ?>
                        <select id="fk_clienti" name="fk_clienti"></select>
						<input type="hidden" name="hidden_fk_clienti" value="<?php if (isset($fk_clienti)) echo $fk_clienti?>">

                </div>
                <!--<label class="col-sm-1 control-label"><?php _e('Contact','cpsmartcrm')?></label>
                <div class="col-sm-3">
                    <input type="hidden" id="FK_contatti" name="FK_contatti" value="<?php if (isset($FK_contatti)) echo $FK_contatti?>" data-value="<?php if (isset($FK_contatti)) echo $FK_contatti?>" />
                </div>-->
                <div class="col-sm-2">
                    <input type="button" class="btn btn-sm btn-success _flat" id="save_client_data" name="save_client_data" value="<? _e('Save','cpsmartcrm')?>" style="display:none" />
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-1 control-label"><?php _e('Address','cpsmartcrm')?></label>
                <div class="col-sm-2 col-md-2 col-lg-3">

                    <input type="text" class="form-control _editable" name="indirizzo" id="indirizzo" maxlength='50' value="<?php if (isset($indirizzo)) echo $indirizzo?>" <?php echo $disabled?> data-value="<?php if (isset($indirizzo)) echo $indirizzo?>" />

                </div>
                <label class="col-sm-1 control-label"><?php _e('ZIP code','cpsmartcrm')?></label>
                <div class="col-sm-2">

                    <input type="text" class="form-control _editable" name="cap" id="cap" maxlength='10' value="<?php if (isset($cap)) echo $cap?>" <?php echo $disabled?> data-value="<?php if (isset($cap)) echo $cap?>">

                </div>
                <label class="col-sm-1 control-label"><?php _e('C.F.','cpsmartcrm')?></label>
                <div class="col-md-2">
                    <input type="text" class="form-control _editable" name="cod_fis" id="cod_fis" maxlength='5' value="<?php if (isset($cod_fis)) echo $cod_fis?>" <?php echo $disabled?> data-value="<?php if (isset($cod_fis)) echo $cod_fis?>">
                </div>

            </div>
            <div class="row form-group">
                <label class="col-sm-1 control-label"><?php _e('Town','cpsmartcrm')?></label>
                <div class="col-sm-2 col-md-2 col-lg-3">

                    <input type="text" class="form-control _editable" name="localita" id="localita" maxlength='50' value="<?php if (isset($localita)) echo $localita?>" <?php echo $disabled?> data-value="<?php if (isset($localita)) echo $localita?>">

                </div>
                <label class="col-sm-1 control-label"><?php _e('State/Prov.','cpsmartcrm')?></label>
                <div class="col-sm-2">

                    <input type="text" class="form-control _editable" name="provincia" id="provincia" maxlength='5' value="<?php if (isset($provincia)) echo $provincia?>" <?php echo $disabled?> data-value="<?php if (isset($provincia)) echo $provincia?>">

                </div>
                <label class="col-sm-1 control-label"><?php _e('VAT code','cpsmartcrm')?></label>
                <div class="col-md-2">
                    <input type="text" class="form-control _editable" name="p_iva" id="p_iva" maxlength='5' value="<?php if (isset($p_iva)) echo $p_iva?>" <?php echo $disabled?> data-value="<?php if (isset($p_iva)) echo $p_iva?>">
                </div>

            </div>
        </div>
    
        <div class="meta-box-sortables ui-sortable">
         <div class="postbox">
             <!--<button type="button" class="handlediv button-link" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>-->

	    <h4 class="page-header"><?php _e('QUOTE TEXT','cpsmartcrm')?> <span class="crmHelp" data-help="quotation-text"></span></h4>
            <div class="inside">
            <?php
            $content = isset($riga["testo_libero"])?$riga["testo_libero"]:"";
            $editor_id = 'mycustomeditor';
            $settings = array( 'media_buttons' => false, 'quicktags' => true, 'textarea_name' => 'testo_libero', 'wpautop' => false, 'textarea_rows' => 10  );
            wp_editor( stripslashes( $content ), $editor_id, $settings );
            ?>
            </div>
         </div>
        </div>
        <!-- </div>
        
        <div>-->

        <h4 class="page-header"><?php _e('Add Products to quote','cpsmartcrm')?><span class="crmHelp" data-help="quotation-products"></span>
            <?php do_action("WPsCRM_show_WOO_products");?>
		</h4>
        <div class="row form-group">
            <div class="col-md-6">
                <span  id="btn_manual" class="btn btn-sm  btn-add_desc_row _flat" onclick="add_manual_row();" style="margin-left:0px"><?php _e('Add row with price','cpsmartcrm')?> &raquo;</span>
                <span id="btn_descriptive" class="btn btn-sm  btn-add_desc_row _flat" onclick="add_descriptive_row();"><?php _e('Add descriptive row','cpsmartcrm')?> &raquo;</span>
            </div>
        </div>
        <div class="row form-group">

	        <table class="table table-striped table-bordered col-md-11" id="t_art" style="width:90%!important">
            <thead>
            <tr>
	            <th><?php _e('Code','cpsmartcrm')?></th>
	            <th><?php _e('Description','cpsmartcrm')?></th>
	            <th><?php _e('Q.ty','cpsmartcrm')?></th>
	            <th><?php _e('Price','cpsmartcrm')?></th>
	            <th><?php _e('Discount','cpsmartcrm')?></th>
	            <th><?php _e('VAT','cpsmartcrm')?></th>
	            <th><?php _e('Total','cpsmartcrm')?></th>
	            <th><?php _e('Actions','cpsmartcrm')?></th>
            </tr>
            </thead>
            <tbody>

            <?php
        if ($ID)
        {
            $i=1;
            foreach ( $qd as $rigad )

            {
                $art_id=$rigad["fk_articoli"];
                $descrizione=$rigad["descrizione"];
                $code=$rigad["codice"];

                if ($tipo_riga=$rigad["tipo"]==3)
                {
            ?>
 		            <tr class="riga" id="r_<?php echo $i?>">
			            <td colspan="7"><input type="hidden" size="10" name="idd_<?php echo $i?>" id="idd_<?php echo $i?>" value="<?php echo $rigad["id"]?>" /><input type="hidden" size="10" name="id_<?php echo $i?>" id="id_<?php echo $i?>" value="<?php echo $art_id?>" /><input type="hidden" size="10" name="tipo_<?php echo $i?>" id="tipo_<?php echo $i?>" value="<?php echo $tipo_riga?>" /><textarea style="width:93%" name="descrizione_<?php echo $i?>" id="descrizione_<?php echo $i?>" class="descriptive_row"><?php echo $descrizione?></textarea></td>
			            <td><button  onclick="elimina_riga(<?php echo $rigad["id"]?>, <?php echo $i?>);return false;"><?php _e('Delete','cpsmartcrm')?></button></td>
		            </tr>
            <?php		}
                else
                {
            ?>
 		            <tr class="riga" id="r_<?php echo $i?>">
			            <td><input type="hidden" size="10" name="idd_<?php echo $i?>" id="idd_<?php echo $i?>" value="<?php echo $rigad["id"]?>" /><input type="hidden" size="10" name="id_<?php echo $i?>" id="id_<?php echo $i?>" value="<?php echo $art_id?>" /><input type="hidden" size="10" name="tipo_<?php echo $i?>" id="tipo_<?php echo $i?>" value="<?php echo $tipo_riga?>" /><input type="text" size="10" name="codice_<?php echo $i?>" id="codice_<?php echo $i?>" value="<?php echo $code?>" /></td>
			            <td><textarea name="descrizione_<?php echo $i?>" id="descrizione_<?php echo $i?>" style="width:93%" class="descriptive_row"><?php echo $descrizione?></textarea></td>
			            <td ><input type="text" size="3" name="qta_<?php echo $i?>" id="qta_<?php echo $i?>" value="<?php echo $rigad["qta"]?>" onblur="aggiorna(<?php echo $i?>)" /></td>
			            <td><input type="text" size="10" name="prezzo_<?php echo $i?>" id="prezzo_<?php echo $i?>" value="<?php echo $rigad["prezzo"]?>" onblur="aggiorna(<?php echo $i?>)" /></td>
			            <td><input type="text" name="sconto_<?php echo $i?>" id="sconto_<?php echo $i?>" value="<?php echo $rigad["sconto"]?>" size="5" onblur="aggiorna(<?php echo $i?>)" /></td>
			            <td><input type="text" name="iva_<?php echo $i?>" id="iva_<?php echo $i?>" value="<?php echo $rigad["iva"]?>" size="5" onblur="aggiorna(<?php echo $i?>)" /></td>
			            <td><input type="text" size="10" name="totale_<?php echo $i?>" id="totale_<?php echo $i?>" value="<?php echo $rigad["totale"]?>"  readonly /></td>
			            <td><button  onclick="elimina_riga(<?php echo $rigad["id"]?>, <?php echo $i?>);return false;"><?php _e('Delete','cpsmartcrm')?></button></td>
		            </tr>
            <?php
                }
	            $i++;
            }
        }
            ?>
            </tbody>
            </table>

	        </div>

        <div class="row form-group">

        </div>

        <div class="row form-group">
	        <label class="col-sm-1 control-label"><?php _e('Total Net','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input type="text" class="form-control"  name="totale_imponibile" id='totale_imponibile' value="<?php if (isset($tot_imp)) echo $tot_imp?>" readonly>
	        </div>
	        <label class="col-sm-1 control-label"><?php _e('Total Tax','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input type="text" class="form-control"  name="totale_imposta" id='totale_imposta' value="<?php if (isset($totale_imposta)) echo $totale_imposta?>" readonly>
	        </div>
	        <label class="col-sm-1 control-label"><?php _e('Total','cpsmartcrm')?></label>
	        <div class="col-sm-2"><input type="text" class="form-control"   name="totale" id='totale' value="<?php if (isset($totale)) echo $totale?>" readonly>
	        </div>
        </div>
    </div>
    <!-- fine PRIMO TAB-->
    <!-- SECONDO TAB-->
    <div>
        <div class="row form-group">
            <label class="col-sm-2 control-label"><?php _e('Quote value (required)','cpsmartcrm')?> *</label>
             <div class="col-sm-6">
                 <input type="text" id="quotation_value" name="quotation_value" value="<?php if (isset($riga)) echo $riga["valore_preventivo"]?>" />

             </div>
        </div>
        <div class="row form-group">
	        <label class="col-sm-2 control-label"><?php _e('Comments','cpsmartcrm')?></label>
	        <div class="col-sm-6"><textarea class="_form-control" id="commento" name="commento" rows="10" cols="50"><?php if (isset($riga)) echo stripslashes($riga["commento"])?></textarea>
	        </div>
        </div>
        <div class="row form-group">
	        <label class="col-sm-2 control-label"><?php _e('Forecast success percentage','cpsmartcrm')?> % </label>
	        <div class="col-sm-4">
                <select name="perc_realizzo">
	                <option value=""></option>
	                <option value="0-25" <?php if (isset($riga)) echo $riga["perc_realizzo"]=="0-25"?"selected":""?>>0-25</option>
	                <option value="25-50" <?php if (isset($riga)) echo $riga["perc_realizzo"]=="25-50"?"selected":""?>>25-50</option>
	                <option value="50-75" <?php if (isset($riga)) echo $riga["perc_realizzo"]=="50-75"?"selected":""?>>50-75</option>
	                <option value="75-100" <?php if (isset($riga)) echo $riga["perc_realizzo"]=="75-100"?"selected":""?>>75-100</option>
	            </select>
	        </div>

        </div>
</div>

</div>

    

</form>    
<ul class="select-action">

    <li onClick="aggiornatot();return false;" class="btn btn-sm btn-success _flat" id="_submit">
        <i class="glyphicon glyphicon-floppy-disk"></i>
        <b> <?php _e('Save','cpsmartcrm')?></b>
    </li>
    <li class="btn btn-warning btn-sm _flat" onClick="annulla();return false;">
        <i class="glyphicon glyphicon-floppy-remove"></i>
        <b> <?php _e('Reset','cpsmartcrm')?></b>
    </li>
    <?php
  if ($ID){
    ?>
    <li class="btn btn-sm btn-info _flat" onclick="location.replace('<?php echo admin_url('admin.php?page=smart-crm&p=documenti/document_print.php&id_invoice='.$ID)?>')">
        <i class="glyphicon glyphicon-print"></i>

        <b> <?php _e('Printable version','cpsmartcrm')?></b>
    </li>
    <?php }?>
</ul>
<div id="dialog"></div>
<div id="dialog_todo" style="display:none;" data-from="documenti" data-fkcliente="<?php if (isset($fk_clienti)) echo $fk_clienti?>">
	<?php
	include ( WPsCRM_DIR."/inc/crm/clienti/form_todo.php" )
    ?>
</div>
<?php 
	include (WPsCRM_DIR."/inc/crm/clienti/script_todo.php" )
?>
<div id="dialog_appuntamento" style="display:none;" data-from="documenti" data-fkcliente="<?php if (isset($fk_clienti)) echo $fk_clienti?>">
	<?php
	include (WPsCRM_DIR."/inc/crm/clienti/form_appuntamento.php" )
    ?>
</div>
<?php 
	include (WPsCRM_DIR."/inc/crm/clienti/script_appuntamento.php" )
?>
<div id="dialog_attivita" style="display:none;"  data-from="documenti" data-fkcliente="<?php if (isset($fk_clienti)) echo $fk_clienti?>">
	<?php
	include (WPsCRM_DIR."/inc/crm/clienti/form_attivita.php" )
    ?>
</div>
<?php
	include (WPsCRM_DIR."/inc/crm/clienti/script_attivita.php" )
?>
<div id="dialog_mail" style="display:none;" data-from="documenti" data-fkcliente="<?php if (isset($fk_clienti)) echo $fk_clienti?>">
	<?php
	include (WPsCRM_DIR."/inc/crm/clienti/form_mail.php" )
    ?>    
</div>
<?php
	include (WPsCRM_DIR."/inc/crm/clienti/script_mail.php" )
?>

<script type="text/javascript">

	jQuery(document).ready(function ($) {
	$("._tooltip").kendoTooltip({
    	//autoHide: false,
    	animation: {
			close: {
				duration: 1000,
			}
		},
		position:"top",
    	content: "<h4><?php _e('BUTTONS LEGEND','cpsmartcrm')?>:</h4>\n\
	<ul>\n\
		<li class=\"no-link\">\n\
			<span class=\"btn btn-info _flat\"><i class=\"glyphicon glyphicon-tag\"></i> = <?php _e('NEW TODO','cpsmartcrm')?></span>\n\
			<span class=\"btn btn_appuntamento_1 _flat\"><i class=\"glyphicon glyphicon-pushpin\"></i> = <?php _e('NEW APPOINTMENT','cpsmartcrm')?></span>\n\
			<span class=\"btn btn-primary _flat\"><i class=\"glyphicon glyphicon-option-horizontal\"></i> = <?php _e('NEW ACTIVITY','cpsmartcrm')?></span>\n\
			<span class=\"btn btn-warning _flat\"><i class=\"glyphicon glyphicon-envelope\"></i> = <?php _e('NEW MAIL','cpsmartcrm')?></span>\n\
		</li>\n\
	</ul>"
    });
		var todayDate = kendo.toString(new Date(), $format, localCulture);

		var todayAbsoluteDate = new Date();
		$("#data").kendoDatePicker({
			<?php if(! $ID) {?>
			value: todayDate,
			<?php } ?>
			format: $format,
		})
		var issuedate = $("#data").data("kendoDatePicker");
		$("#data_scadenza").kendoDatePicker({
			<?php if($data_scadenza =="") {?>
			value: todayDate,
			<?php } ?>
			format:$format
		})
	<?php if ($ID){ ?>
		$("#fk_clienti").kendoDropDownList({
			enable: false
		});
		$('#hidden_fk_clienti').val('<?php echo $ID?>');
		<?php } ?>

		$('._edit').on('click', function () {
			var $this = $(this);
			$this.hide();
			$('._quitEdit').show();
			var dropdownlist = $("#fk_clienti").data("kendoDropDownList");
			//dropdownlist.enable(true);
			$('._editable').attr('readonly', false).attr('disabled', false);

			$('#_submit').css('visibility', 'hidden');
			$('#save_client_data').show();
			$('#save_client_data').parent().append("<br><small class=\"_notice notice notice-error \"><?php _e("You're editing the master data for this customer",'cpsmartcrm')?></small>");
			$('.customer_data_partial').addClass('edit_active');
			});

		$('._quitEdit').on('click', function () {
			var dropdownlist = $("#fk_clienti").data("kendoDropDownList");
			var $this = $(this);
			$this.hide();
			$('._notice').hide().remove();
			$('._edit').show();
			$('._editable').attr('readonly', 'readonly').attr('disabled', 'disabled');
			$('._editable').each(function (e) {
				$(this).val('');
				$(this).val($(this).data('value'));
			})
			$('#_submit').css('visibility','visible');
			$('#save_client_data').hide();
			$('.customer_data_partial').removeClass('edit_active');
			});

		$('#save_client_data').on('click', function () {
			var inputs = $('.customer_data_partial :input').serialize();

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'WPsCRM_save_client_partial',
					values: inputs,
					security:'<?php echo $update_nonce?>'
				},
				success: function (result) {
					noty({
	                    text: "<?php _e('Data has been saved','cpsmartcrm')?>",
	                    layout: 'center',
	                    type: 'success',
	                    template: '<div class="noty_message"><span class="noty_text"></span></div>',
	                    //closeWith: ['button'],
	                    timeout: 1000
					});
					setTimeout(function () {
						$('._quitEdit').hide();
						$('._notice').hide().remove();
						$('._edit').show();
						$('._editable').attr('readonly', 'readonly').attr('disabled', 'disabled');
						$('#_submit').css('visibility', 'visible');
						$('#save_client_data').hide();
						$('.customer_data_partial').removeClass('edit_active');
					}, 200)
					$('.customer_data_partial :input').each(function () {
						$(this).attr('data-value',$(this).val())
					})

				},
				error: function (errorThrown) {
					console.log(errorThrown);
				}
			})
		})
        if ($('#totale_imponibile').val()>0)
            $('#quotation_value').val($('#totale_imponibile').val());
        $('#totale_imponibile').on('change', function () {
            if ($('#totale_imponibile').val()>0)
                $('#quotation_value').val($(this).val());
        })
        var _clients = new kendo.data.DataSource({
            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                        	'action': 'WPsCRM_get_clients2',
							'self_client':1
                        },
                        success: function (result) {
                            $("#fk_clienti").data("kendoDropDownList").dataSource.data(result.clients);

                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    })
                }
            }
        });

        var clienti=$('#fk_clienti').kendoDropDownList({
            placeholder: "Select Client...",
            dataTextField: "ragione_sociale",
            dataValueField: "ID_clienti",
            filter: "contains",
            autoBind: false,
            minLength: 3,
            dataSource: _clients,
            change: function () {
            	id_clienti = this.value();
            	if (id_clienti != null && id_clienti != "" && id_clienti != undefined) {
            		$.ajax({
            			url: ajaxurl,
            			data: {
            				'action': 'WPsCRM_get_client_info',
            				'id_clienti': id_clienti
            			},
            			success: function (result) {
            				console.log(result.info);
            				var parseData = result.info;
            				JSON.stringify(parseData);
            				$("#indirizzo").val(parseData[0].indirizzo);
            				$("#cap").val(parseData[0].cap);
            				$("#localita").val(parseData[0].localita);
            				$("#provincia").val(parseData[0].provincia);
            				$("#cod_fis").val(parseData[0].cod_fis);
            				$("#p_iva").val(parseData[0].p_iva);
            				$("#tipo_cliente").val(parseData[0].tipo_cliente);
            			},
            			error: function (errorThrown) {
            				console.log(errorThrown);
            			}
            		})
            	}
            },
            width: 300
        }).data('kendoDropDownList');

		$('#fk_clienti').data('kendoDropDownList').value([<?php if (isset($fk_clienti)) echo $fk_clienti ?>]);
		<?php if ( isset($_GET['cliente'] ) ) { ?>
		$('#fk_clienti').data('kendoDropDownList').value(<?php echo $_GET['cliente']?>)
		$('#fk_clienti').data('kendoDropDownList').trigger("change");

		<?php } ?>
        var userSource = new kendo.data.DataSource({
            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                        	'action': 'WPsCRM_get_CRM_users',

                        },
                        success: function (result) {
                            $("#remindToUser").data("kendoMultiSelect").dataSource.data(result);
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    })
                }
            }
        });
        var roleSource = new kendo.data.DataSource({
            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                        	'action': 'WPsCRM_get_registered_roles',
                        },
                        success: function (result) {
                            $("#remindToGroup").data("kendoMultiSelect").dataSource.data(result.roles);
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    })
                }
            }
        });
        $('#remindToUser').kendoMultiSelect({
            placeholder: "Select User...",
            dataTextField: "display_name",
            dataValueField: "ID",
            autoBind: false,
            dataSource: userSource,
            change: function (e) {
                var selectedUsers = (this.value()).clean("");
                $('#selectedUsers').val(selectedUsers)
            },
            dataBound: function (e) {
                var selectedUsers = (this.value()).clean("");
                $('#selectedUsers').val(selectedUsers)
            }
        })
        $('#remindToGroup').kendoMultiSelect({
            placeholder: "Select Role...",
            dataTextField: "name",
            dataValueField: "name",
            autoBind: false,
            dataSource: roleSource,
            change: function (e) {
                var selectedGroups = (this.value()).clean("");
                $('#selectedGroups').val(selectedGroups)
            },
            dataBound: function (e) {
                var selectedGroups = (this.value()).clean("");
                $('#selectedGroups').val(selectedGroups)
            }
        });

  $("#tabstrip").kendoTabStrip({
        animation: {
            // fade-out current tab over 1000 milliseconds
            close: {
                duration: 500,
                effects: "fadeOut"
            },
           // fade-in new tab over 500 milliseconds
           open: {
               duration: 500,
               effects: "fadeIn"
           }
       }
    });
  	var tabToActivate = $("#tab1");
	$("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab(tabToActivate);

	//$('#data').datepicker({setDate: new Date(),dateFormat: 'dd-mm-yy'});
	//$('#data_consegna').datepicker({setDate: new Date(),dateFormat: 'dd-mm-yy'});

//modal validators


        $(document).on('click', '._reset',function () {

            $('._modal').fadeOut('fast');
            $('input[type="reset"]').trigger('click');
        })



        var _users = new kendo.data.DataSource({
            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                        	action: 'WPsCRM_get_CRM_users',
                            role: 'CRM_agent',
                            include_admin:true
                        },
                        success: function (result) {
                            $("#selectAgent").data("kendoDropDownList").dataSource.data(result);

                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    })
                }
            }
        });
        $('#selectAgent').kendoDropDownList({
            placeholder: "Select User...",
            dataTextField: "display_name",
            dataValueField: "ID",
            autoBind: true,
            dataSource: _users,
        });
        if (agente='<?php echo isset($agente)?$agente:""?>')
            $("#selectAgent").data('kendoDropDownList').value(agente);
        $('#categoria').kendoDropDownList({});


    var validator = jQuery("#form_insert").kendoValidator({
    	checker: { one: "null" },
    	rules: {
    		hasClient: function (input) {

    			if (input.is("[name=fk_clienti]")) {

    				if (jQuery('input[name="fk_clienti"]').attr('type') != "hidden")  {
    					var kb = jQuery("#fk_clienti").data("kendoDropDownList").value();
    					if (kb.length == "") {
    						jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    						jQuery('input[name="fk_clienti"]').focus();
    						return false;
    					}
    				}
    				this.options.checker.one = "passed";
    				console.log(this.options.checker.one);
    				return true;
    			}

    			return true;
    		},
    		hasValue: function (input) {
    			if (input.is('[name="quotation_value"]')) {
    				if (jQuery('input[name="quotation_value"]').val() == "0" || jQuery('input[name="quotation_value"]').val() == "" || jQuery('input[name="quotation_value"]').val() == undefined) {
    					if (this.options.checker.one =="passed") {
    						jQuery("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab(jQuery('#tab2'));
    						jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    						jQuery('input[name="quotation_value"]').focus();
    						return false;
    					}

    				}
    			}

    			return true;
    		}
    	},

    	messages: {
    		hasClient: "<?php _e('You should select a customer','cpsmartcrm')?>",
    		hasValue: "<?php _e('This quote should have a value','cpsmartcrm')?>",
    	}
    }).data("kendoValidator");

	$('#_submit').on('click', function (e) {
		aggiornatot();
		if (validator.validate()) {
			//alert();
			showMouseLoader();
			jQuery('#mouse_loader').offset({ left: e.pageX, top: e.pageY });
			var n_row = jQuery('#t_art > tbody > tr').length;
			jQuery('#num_righe').val(n_row);

			jQuery("#form_insert").submit();
		}
	})
});
function addRow(id, codice, descrizione, iva, prezzo, arr_rules, rule)
	{
		var n_row=jQuery('#t_art > tbody > tr').length+1;

		jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '" value="1"><input type="hidden" name="id_' + n_row + '" value="' + id + '"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value="' + codice + '"></td><td><textarea  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" style="width:93%">' + descrizione + '</textarea></td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '"  onblur="aggiorna(' + n_row + ')" value="1" /></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value="' + prezzo + '"  onblur="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5"  onblur="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
		aggiorna(n_row);
	}


    function add_manual_row() {
        jQuery.ajax({
            url: ajaxurl,
            data: {
            	'action': 'WPsCRM_get_product_manual_info'
            },
            success: function (result) {
                console.log(result.info);
                var parseData = result.info;
                JSON.stringify(parseData);
                var iva=parseData[0].iva;
                //console.log(arr_rules);
                var n_row=jQuery('#t_art > tbody > tr').length+1;
                jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '" value="2"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value=""></td><td><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiorna(' + n_row + ')"></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value=""  onblur="aggiorna(' + n_row + ')"  oninput="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiorna(' + n_row + ')"  oninput="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5"  onblur="aggiorna(' + n_row + ')" oninput="aggiorna(' + n_row + ')"></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        })
    }
    function add_descriptive_row() {
        var n_row = jQuery('#t_art > tbody > tr').length + 1;
        jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td colspan="7"><input type="hidden" name="tipo_' + n_row + '" value="3"><textarea  rows="1" style="width:93%" name="descrizione_' + n_row + '" id="descrizione_' + n_row + '"class="descriptive_row" ></textarea></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
    }
    function annulla() {
        location.href = "<?php echo admin_url('admin.php?page=smart-crm&p=documenti/list.php')?>";
    }

    function aggiorna(riga) {
        //	alert(riga);
        var qta = document.getElementById("qta_" + riga).value;
        var pre = document.getElementById("prezzo_" + riga).value;
        var sconto = document.getElementById("sconto_" + riga).value;
        var iva = document.getElementById("iva_" + riga).value;
        var tot = document.getElementById("totale_" + riga);
        var pre_sc = pre - (pre * sconto / 100);
        var totale = qta * pre_sc;
        if (parseInt(iva) > 0)
            totale = totale + (totale * iva / 100);
        tot.value = Math.round(totale * 100) / 100;
        aggiornatot();
    }

    function aggiornatot() {
        var n_row=jQuery('#t_art > tbody > tr').length;
        var form = document.forms["form_insert"];
	    var totaleimp = 0; var totale = 0; var totale_imposta = 0;
	    for (i=1;i<=n_row;i++) {
	        if (tot = document.getElementById("totale_" + i)) {
	            var qta = document.getElementById("qta_" + i).value;
	            var pre = document.getElementById("prezzo_" + i).value;
	            var iva = document.getElementById("iva_" + i).value;
	            var sconto = document.getElementById("sconto_" + i).value;
	            var pre_sc = pre - (pre * sconto / 100);
	            var totaleriga = qta * pre_sc;
	            totaleimp = totaleimp + parseFloat(totaleriga);
	            totale_imposta = totale_imposta + parseFloat(totaleriga * iva / 100);
	        }
	    }
        totale = totaleimp;
        totale = Math.round((totale) * 100) / 100;
        if (totaleimp)
        {
            form.elements["totale_imponibile"].value = Math.round(totaleimp * 100) / 100;
            form.elements["totale_imposta"].value = Math.round(totale_imposta * 100) / 100;
            form.elements["totale"].value = Math.round((totale + totale_imposta) * 100) / 100;
			if(totaleimp !=0)
            form.elements["quotation_value"].value = form.elements["totale_imponibile"].value;
        }
        else
        {
            form.elements["totale_imponibile"].value = 0;
            form.elements["totale_imposta"].value = 0;
            form.elements["totale"].value = 0;
            //form.elements["quotation_value"].value = 0;
        }
    }
    function apri_offerta() {
        var form = document.forms["form_insert"];
        if (!confirm("Riaprire questa offerta?"))
            return false;
        form.action = "index2.php?page=documenti/apri.php&ID=<?php echo $ID?>";
        form.submit();
    }
    function elimina_riga(id_art, riga) {
        if (!confirm("<?php _e('Confirm delete? It will still be possible to recover the deleted item ','cpsmartcrm')?>"))
            return false;
        jQuery('#r_' + riga).fadeOut("slow", function () { jQuery(this).remove(); });
        jQuery('#totale_' + riga).remove();
        if (id_art) {
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: 'WPsCRM_delete_document_row',
                    row_id: id_art,
					security:'<?php echo $delete_nonce?>'
                },
                success: function (result) {
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            })
        }
		 aggiornatot();
    }

</script>
<?php
}
?>
<style>
	<?php if(isset($_GET['layout']) && $_GET['layout']=="iframe") { ?>
	#wpadminbar, #adminmenumain, #mainMenu,.wrap h1,.btn-warning,.select-action:first-of-type  {
        display: none;
    }
	#wpcontent, #wpfooter {
    margin-left: 0;
}
	<?php } ?>
</style>
