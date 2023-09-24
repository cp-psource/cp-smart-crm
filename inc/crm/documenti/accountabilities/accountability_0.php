<!-- accountability standard -->
<div class="row form-group">
	<div class="col-md-12">
		<span id="btn_manual" class="btn btn-sm btn-add_desc_row _flat" onclick="add_manual_row();" style="margin-left:0px">
			<?php _e('Add row with price','cpsmartcrm')?> &raquo;
		</span>
		<span id="btn_descriptive" class="btn btn-sm btn-add_desc_row _flat" onclick="add_descriptive_row();">
			<?php _e('Add descriptive row','cpsmartcrm')?> &raquo;
		</span>
		<?php do_action('WPsCRM_advanced_rows')?>
		<?php 
		if($ID)
			do_action('WPsCRM_Einvoice',$ID);
		?>
	</div>
</div>
<div class="row form-group">

<table class="table table-striped table-bordered col-md-11" id="t_art" style="width:95%!important">
	<thead>
		<tr>
			<th>
				<?php _e('Code','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Description','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Rule','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Q.ty','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Price','cpsmartcrm')?>
			</th>
			<th style="min-width:68px">
				<?php _e('Discount','cpsmartcrm')?><br />
				% <input type="radio" name="tipo_sconto" id="tipo_sconto0" value="0" <?php echo ((isset($riga) && $riga["tipo_sconto"]==0) || !isset($riga))?"checked":""?> onchange="aggiornatot();">
				&euro; <input type="radio" name="tipo_sconto" id="tipo_sconto1" value="1" <?php echo (isset($riga) && $riga["tipo_sconto"]==1)?"checked":""?> onchange="aggiornatot();">
			</th>
			<th>
				<?php _e('VAT','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Total','cpsmartcrm')?>
			</th>
			<th>
				<?php _e('Actions','cpsmartcrm')?>
			</th>
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
			<td colspan="8">
				<input type="hidden" size="10" name="idd_<?php echo $i?>" id="idd_<?php echo $i?>" value="<?php echo $rigad["id"]?>" />
				<input type="hidden" size="10" name="id_<?php echo $i?>" id="id_<?php echo $i?>" value="<?php echo $art_id?>" />
				<input type="hidden" size="10" name="tipo_<?php echo $i?>" id="tipo_<?php echo $i?>" value="<?php echo $tipo_riga?>" />
				<textarea style="width:93%" name="descrizione_<?php echo $i?>" id="descrizione_<?php echo $i?>" class="descriptive_row">
					<?php echo $descrizione?>
				</textarea>
			</td>
			<td>
				<a href="#" onclick="elimina_riga(<?php echo $rigad["id"]?>, <?php echo $i?>);return false;">
					<?php _e('Delete','cpsmartcrm')?>
				</a>
			</td>
		</tr>
		<?php
				}
				else
				{
        ?>
		<tr class="riga" id="r_<?php echo $i?>">
			<td>
				<input type="hidden" size="10" name="idd_<?php echo $i?>" id="idd_<?php echo $i?>" value="<?php echo $rigad["id"]?>" />
				<input type="hidden" size="10" name="id_<?php echo $i?>" id="id_<?php echo $i?>" value="<?php echo $art_id?>" />
				<input type="hidden" size="10" name="tipo_<?php echo $i?>" id="tipo_<?php echo $i?>" value="<?php echo $tipo_riga?>" />
				<input type="text" size="10" name="codice_<?php echo $i?>" id="codice_<?php echo $i?>" value="<?php echo $code?>" />
			</td>
			<td>
				<textarea name="descrizione_<?php echo $i?>" id="descrizione_<?php echo $i?>" style="width:93%"><?php echo $descrizione?></textarea>
			</td>
			<td>
				<?php
					if ($rigad["fk_subscriptionrules"])
					{
						$sql="SELECT * FROM ".WPsCRM_TABLE."subscriptionrules WHERE ID=".$rigad["fk_subscriptionrules"];
						$rule=$wpdb->get_row($sql)->name;
						$id_rule=$wpdb->get_row($sql)->ID;

                        echo $rule;
                        echo "<input type='hidden' name='subscriptionrules_".$i."' id='subscriptionrules_".$i."' value='".$id_rule."'>";
					}
                ?>
			</td>
			<td>
				<input type="text" size="3" name="qta_<?php echo $i?>" id="qta_<?php echo $i?>" value="<?php echo $rigad["qta"]?>" oninput="aggiornatot();" onblur="aggiornatot()" />
			</td>
			<td>
				<input type="text" size="10" name="prezzo_<?php echo $i?>" id="prezzo_<?php echo $i?>" value="<?php echo $rigad["prezzo"]?>" oninput="aggiornatot()" onblur="aggiornatot()" />
			</td>
			<td>
				<input type="text" name="sconto_<?php echo $i?>" id="sconto_<?php echo $i?>" value="<?php echo $rigad["sconto"]?>" size="5" oninput="aggiornatot()" onblur="aggiornatot()" />
			</td>
			<td>
				<input type="text" name="iva_<?php echo $i?>" id="iva_<?php echo $i?>" value="<?php echo $rigad["iva"]?>" size="5" oninput="aggiornatot()" onblur="aggiornatot()" />
			</td>
			<td>
				<input type="text" size="10" name="totale_<?php echo $i?>" id="totale_<?php echo $i?>" value="<?php echo $rigad["totale"]?>" />
			</td>
			<td>
				<button type="button" onclick="elimina_riga(<?php echo $rigad["id"]?>, <?php echo $i?>)">
					<?php _e('Delete','cpsmartcrm')?>
				</button>
			</td>
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
	<label class="col-sm-1 control-label">
		<?php _e('Total Net','cpsmartcrm')?>
	</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" name="totale_imponibile" id='totale_imponibile' value="<?php if (isset($tot_imp)) echo $tot_imp?>" readonly />
	</div>
	<label class="col-sm-1 control-label">
		<?php _e('Total Tax','cpsmartcrm')?>
	</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" name="totale_imposta" id='totale_imposta' value="<?php if (isset($totale_imposta)) echo $totale_imposta?>" readonly />
	</div>
	<label class="col-sm-1 control-label">
		<?php _e('Total','cpsmartcrm')?>
	</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" name="totale" id='totale' value="<?php if (isset($totale)) echo $totale?>" readonly />
	</div>
</div>
<script>
var def_iva="<?php if (isset($def_iva)) echo $def_iva?>", cassa="<?php if (isset($cassa)) echo $cassa?>", rit_acconto="<?php if (isset($rit_acconto)) echo $rit_acconto?>";
jQuery(document).ready(function($){
	$('#calculate').on('click',function(){
		var amount = $('#reverseAmount').val();
		var refund = $('#reverseRefund').val();
		var tipo_cliente = $('#tipo_cliente').val();
		if(amount !="")
		{
			jQuery.ajax({
                url: ajaxurl,
                data: {
                	action: 'WPsCRM_reverse_invoice',
                	amount: amount,
                	refund: refund,
                	accountability: 0,
                	tipo_cliente: tipo_cliente
                },
                success: function (result) {
                    console.log(result);
					var n_row=jQuery('#t_art > tbody > tr').length+1;
					jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="2"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value=""></td><td><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td></td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()" value="1"></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value="'+result+'"  onblur="aggiornatot()"  oninput="aggiornatot()"></td><td><input type="text" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiornatot()"  oninput="aggiornatot()"></td><td><input type="text" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + def_iva + '" size="5"  onblur="aggiornatot()" oninput="aggiornatot()"></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '" value=""></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
					aggiornatot();
					window.sessionStorage.setItem('ref_row',n_row);
					if (refund)
					{
						var n_row=jQuery('#t_art > tbody > tr').length+1;
						jQuery('#t_art').append('<tr class="riga riga_refund" id="r_' + n_row + '"><td colspan="3"><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="4"><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()" value="1"></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '"  value="'+refund+'" onblur="aggiornatot()"  oninput="aggiornatot()"></td><td></td><td></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '" value="'+refund+'"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
					}
					window.sessionStorage.setItem('tmp_amount',amount);
					$('#reverseCalculator').data('kendoWindow').close();
					aggiornatot();
				},
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            })
		}
	})
})
function addRow(id, codice, descrizione, iva, prezzo, arr_rules, rule)
	{
		var n_row=jQuery('#t_art > tbody > tr').length+1;
		var s_select = '<select name="subscriptionrules_' + n_row + '" id="subscriptionrules_' + n_row + '"><option value=""></option>';
		if(arr_rules != null)
			for (i=0;i<arr_rules.length;i++)
			{
				if (rule!=0 && arr_rules[i].ID==rule)
				is_sel="selected";
				else
				is_sel="";
				s_select+='<option value="'+arr_rules[i].ID+'" '+is_sel+'>'+arr_rules[i].name+'</option>';
			}
		s_select+='</select>';
		jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" id="tipo_' + n_row + '" name="tipo_' + n_row + '" value="1"><input type="hidden" name="id_' + n_row + '" value="' + id + '"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value="' + codice + '"></td><td><textarea  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" style="width:93%" class="descriptive_row">' + descrizione + '</textarea></td><td>' + s_select + '</td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '"  onblur="aggiornatot()" value="1" /></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value="' + prezzo + '"  onblur="aggiornatot()"></td><td><input type="text" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiornatot()"></td><td><input type="text" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5"  onblur="aggiornatot()"></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
		aggiornatot();
	}

	function aggiorna(riga) {
	    //	alert(riga);
		//debugger;
		if (jQuery('#tipo_sconto0').is(':checked'))
			var tipo_sconto=0;
		else
			var tipo_sconto=1;
	    var qta = document.getElementById("qta_" + riga).value;
	    var pre = document.getElementById("prezzo_" + riga).value;
		if (document.getElementById("sconto_" + riga))
			var sconto = document.getElementById("sconto_" + riga).value;
		if (document.getElementById("iva_" + riga))
			var iva = document.getElementById("iva_" + riga).value;
	    var tot = document.getElementById("totale_" + riga);
	    if (sconto > 0)
		{
			if (tipo_sconto==0)
				var pre_sc = pre - (pre * sconto / 100);
			else
				var pre_sc = pre - sconto;
		}
		else
			var pre_sc=pre;
	    var totale = qta * pre_sc;
	    if (parseInt(iva) > 0)
	        totale = totale + (totale * iva / 100);
	    tot.value = Math.round(totale * 100) / 100;
        //aggiornatot();
	}

	function aggiornatot() {
		//debugger;	
        var n_row=jQuery('#t_art > tbody > tr').length;
        var form = document.forms["form_insert"];
	    var totaleimp = 0; var totale = 0; var totale_imposta = 0;
		var totale_rimborso=0;
		if (jQuery('#tipo_sconto0').is(':checked'))
			var tipo_sconto=0;
		else
			var tipo_sconto=1;
	    for (i=1;i<=n_row;i++) {
	        if (tot = document.getElementById("totale_" + i)) {
	            aggiorna(i);
	            var tipo = document.getElementById("tipo_" + i).value;
	            var qta = document.getElementById("qta_" + i).value;
	            var pre = document.getElementById("prezzo_" + i).value;
				if (document.getElementById("iva_" + i))
					var iva = document.getElementById("iva_" + i).value;
				if (document.getElementById("sconto_" + i))
					var sconto = document.getElementById("sconto_" + i).value;
				if (sconto > 0)
				{
					if (tipo_sconto==0)
						var pre_sc = pre - (pre * sconto / 100);
					else
						var pre_sc = pre - sconto;
				}
				else
				{				
					var pre_sc=pre;
				}
	            var totaleriga = qta * pre_sc;
	            if (tipo!=4)
	            {
    	            totale_imposta = totale_imposta + parseFloat(totaleriga * iva / 100);
    	            totaleimp = totaleimp + parseFloat(totaleriga);
    	        }
    	        else
    	            totale_rimborso = totale_rimborso + parseFloat(totaleriga);
				
	        }
	    }
	    totale = totaleimp;
        totale = Math.round((totale) * 100) / 100;
		totale_imposta=Math.round((totale_imposta) * 100) / 100;
		var totalone=Math.round((totale + totale_imposta+totale_rimborso) * 100) / 100;
		totalone=Math.round((totalone) * 100) / 100;
		if (tmp_amount=sessionStorage.getItem("tmp_amount"))
		{
			var diff=Math.round((totalone-tmp_amount) * 100) / 100;
			//console.log(diff);
			if (diff>0)
			{
				totale=Math.round((totale-diff) * 100) / 100;
				totalone=tmp_amount;
				if (ref_row=sessionStorage.getItem("ref_row"))
				{
					var val_input=jQuery('#prezzo_'+ref_row).val();
					var newval=Math.round((val_input-diff) * 100) / 100;
					console.log(val_input);
					console.log(newval);
					jQuery('#prezzo_'+ref_row).val(newval);
				}
			}
			else if (diff<0)
			{
				//console.log(totale_imposta);
				totale_imposta=totale_imposta-diff;
				totalone=tmp_amount;
			}
		}
		totaleimp=Math.round(totaleimp * 100) / 100;
        totale_imposta=Math.round((totale_imposta) * 100) / 100;
        if (totaleimp)
        {
            form.elements["totale_imponibile"].value = totaleimp.toFixed(2);
            form.elements["totale_imposta"].value = totale_imposta.toFixed(2);
            form.elements["totale"].value = totalone.toFixed(2);
        }
        else
        {
            form.elements["totale_imponibile"].value = 0;
            form.elements["totale_imposta"].value = 0;
            form.elements["totale"].value = 0;
        }
	}

    function elimina_riga(id_art, riga) {
        if (!confirm("<?php _e('Confirm delete? Deletion will not have effect until you save the document','cpsmartcrm')?>"))
            return false;
        jQuery('#r_' + riga).remove();
        jQuery('#totale_' + riga).remove();
        if (id_art) {
            jQuery.ajax({
                url: ajaxurl,
                data: {
                	action: 'WPsCRM_delete_document_row',
                	row_id: id_art,
					security:'<?php echo $delete_nonce ?>'
                },
                success: function (result) {
                    console.log(result);
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            })
        }
            aggiornatot();
    }

	function add_manual_row()
    {
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
                var arr_rules = parseData[0].arr_rules;
                //console.log(arr_rules);
                var n_row=jQuery('#t_art > tbody > tr').length+1;
                var s_select='<select name="subscriptionrules_'+n_row+'" id="subscriptionrules_'+n_row+'"><option value=""></option>';
				if(arr_rules != null)
                for (i = 0; i < arr_rules.length; i++)
                {
                  s_select+='<option value="'+arr_rules[i].ID+'">'+arr_rules[i].name+'</option>';
                }
                s_select+='</select>';
                jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '"  id="tipo_' + n_row + '" value="2"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value=""></td><td><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td>' + s_select + '</td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()"></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value=""  onblur="aggiornatot()"  oninput="aggiornatot()"></td><td><input type="text" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiornatot()"  oninput="aggiornatot()"></td><td><input type="text" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5"  onblur="aggiornatot()" oninput="aggiornatot()"></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        })
    }
  function add_descriptive_row()
    {
      var n_row=jQuery('#t_art > tbody > tr').length+1;
      jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td colspan="7"><input type="hidden" name="tipo_' + n_row + '" value="3"><textarea  rows="1" style="width:93%" name="descrizione_' + n_row + '" id="descrizione_' + n_row + '"  class="descriptive_row"></textarea></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
    }
	function annulla()
	{
		location.href="<?php echo admin_url('admin.php?page=smart-crm&p=documenti/list.php') ?>";
	}
	function elimina()
	{
	    if (!confirm("<?php _e('Confirm delete','cpsmartcrm')?>?"))
			return;
	    location.href = "index2.php?page=clienti/elimina.php&ID=<?php echo $ID?>";
	}
	function add_refund_row(){
		var n_row=jQuery('#t_art > tbody > tr').length+1;
        jQuery('#t_art').append('<tr class="riga riga_refund" id="r_' + n_row + '"><td colspan="3"><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="4"><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td><input type="text" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()"></td><td><input type="text" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value=""  onblur="aggiornatot()"  oninput="aggiornatot()"></td><td></td><td></td><td><input type="text" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete','cpsmartcrm')?></button></td></tr>');
	}

</script>
