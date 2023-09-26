<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$delete_nonce = wp_create_nonce( "delete_customer" );
$update_nonce= wp_create_nonce( "update_customer" );
$scheduler_nonce = wp_create_nonce( "update_scheduler" );
$ID = isset($_REQUEST["ID"])?$_REQUEST["ID"]:0;
$table = WPsCRM_TABLE."clienti";
$ID_azienda = "1";
$email="";
$where = "FK_aziende=$ID_azienda";
$current_user = wp_get_current_user();
$agent_disabled="";
$style_disabled="";
is_multisite() ? $filter=get_blog_option(get_current_blog_id(), 'active_plugins' ) : $filter=get_option('active_plugins' );
if ( in_array( 'wp-smart-crm-agents/wp-smart-crm-agents.php', apply_filters( 'active_plugins', $filter) ) ) {
    $agent_obj=new AGsCRM_agent();
    if ($agent_obj->isAgent){
        $agent_disabled="disabled='disabled'";
        $style_disabled="style='display:none'";        
    }
}
else {
    if ( WPsCRM_is_agent() && ! WPsCRM_agent_can() )
    {
        $agent_disabled="disabled='disabled'";
        $style_disabled="style='display:none'";
    }
}
if ( $ID )
{
	$sql = "select * from $table where ID_clienti=$ID";
	//echo $sql;
    $riga = $wpdb->get_row($sql, ARRAY_A);
    $agente = $riga["agente"];
	$cliente = $riga["ragione_sociale"] ? $riga["ragione_sociale"] : $riga["nome"]." ".$riga["cognome"];
	$cliente = stripslashes( $cliente );
	$email = $riga['email'];
	$custom_tax = maybe_unserialize( $riga['custom_tax'] );
}

if ( ! empty ( $custom_tax ) )
	$_tax=json_encode($custom_tax);
else{
	$_tax=json_encode("");
    $custom_tax="";
    }
?>
<script>
    <?php
    if ( in_array( 'wp-smart-crm-agents/wp-smart-crm-agents.php', apply_filters( 'active_plugins', $filter) ) ) {
    $agent_obj=new AGsCRM_agent();
    ?>
    var privileges = <?php echo json_encode($agent_obj->getCustomerPrivileges($ID, "array")) ?>;
    <?php

    } else{?> 
    var privileges=null;
    <?php } ?>


	var customerTax = JSON.parse('<?php echo $_tax ?>');
	var $format = "<?php echo WPsCRM_DATEFORMAT ?>";
	var $formatTime = "<?php echo WPsCRM_DATETIMEFORMAT ?>";
</script>
<div id="dialog_todo" style="display:none;" data-from="clienti" data-fkcliente="<?php echo $ID?>">
	<?php
	include ( WPsCRM_DIR."/inc/crm/clienti/form_todo.php" )
    ?>
</div>
<?php 
include (WPsCRM_DIR."/inc/crm/clienti/script_todo.php" )
?>
<div id="dialog_appuntamento" style="display:none;" data-from="clienti" data-fkcliente="<?php echo $ID?>">
	<?php
	include (WPsCRM_DIR."/inc/crm/clienti/form_appuntamento.php" )
    ?>
</div>
<?php 
include (WPsCRM_DIR."/inc/crm/clienti/script_appuntamento.php" )
?>
<div id="dialog_attivita" style="display:none;"  data-from="clienti" data-fkcliente="<?php echo $ID?>">
	<?php
	include (WPsCRM_DIR."/inc/crm/clienti/form_attivita.php" )
    ?>
</div>
<?php
include (WPsCRM_DIR."/inc/crm/clienti/script_attivita.php" )
?>
<?php if (isset($email) && $email!="") { ?>
	<div id="dialog_mail" style="display:none;" data-from="clienti" data-fkcliente="<?php echo $ID?>">
		<?php
		include (WPsCRM_DIR."/inc/crm/clienti/form_mail.php" )
		?>    
	</div>
	<?php
		include (WPsCRM_DIR."/inc/crm/clienti/script_mail.php" );
	}
?>
<script type="text/javascript">
    var _datasource=new kendo.data.DataSource({
            transport: {
                read: function (options) {
                    jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'WPsCRM_get_client_scheduler',
                        id_cliente: '<?php if(isset($ID)) echo $ID?>'
                    },
                    success: function (result) {
                        if(jQuery("#grid").length)
                            jQuery("#grid").data("kendoGrid").dataSource.data(result.scheduler);
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                    })
                }
            },
        schema: {
        model: {
            id: "id_agenda",
            fields: {
            tipo: { editable: false },
            oggetto: { editable: false },
            annotazioni: { editable: false },
            data_scadenza: { type: "date", editable: false,format:$format},
            }
        }
        },
        pageSize: 50,
    });
	jQuery(document).ready(function ($) {

		var tabToActivate = $("#tab1");
		<?php if(isset($_GET['tab'])){ ?>
		tabToActivate=$('#tab<?php echo $_GET['tab']?>') ;
		<?php } ?>
		$("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab(tabToActivate);

		<?php do_action('WPsCRM_grid_customer_scheduler',$delete_nonce) ?>


      var _contacts = new kendo.data.DataSource({
	    transport: {
	                read: function (options) {
	                    $.ajax({
	                        url: ajaxurl,
	                        data: {
	                        	'action': 'WPsCRM_get_client_contacts',
	                            'client_id': '<?php echo $ID?>'
	                        },
	                        success: function (result) {
                                if($("#grid_contacts").length)
	                                $("#grid_contacts").data("kendoGrid").dataSource.data(result.contacts);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
	                },
	                create: function (options) {
                      options.success(options.data);
                      $.ajax({
	                        url: ajaxurl,
	                        data: {
	                        	'action': 'WPsCRM_save_client_contact',
								'security':'<?php echo $update_nonce; ?>',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
                                if(jQuery("#grid_contacts").length)
                                    jQuery("#grid_contacts").data("kendoGrid").dataSource.data(result.scheduler);
	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
	                },
	                update: function (options) {
                        options.success(options.data);
                        $.ajax({
	                        url: ajaxurl,
	                        data: {
	                        	'action': 'WPsCRM_save_client_contact',
								'security':'<?php echo $update_nonce; ?>',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
	                        	$("#grid_contacts").data("kendoGrid").dataSource.data(result.contacts);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
                    },
                    destroy: function (options) {
                        console.log("Delete", options);
                        options.success(options.data);
                        $.ajax({
	                        url: ajaxurl,
	                        data: {
	                        	'action': 'WPsCRM_delete_client_contact',
								'security':'<?php echo $delete_nonce; ?>',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
	                        	$("#grid_contacts").data("kendoGrid").dataSource.data(result.contacts);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
                    },
                  parameterMap: function(options, operation) {
                      if (operation !== "read" && options.models) {
                          return {models: kendo.stringify(options.models)};
                      }
                      return kendo.stringify(options);
                  }
	            },
	    schema: {

	        model: {
	            id: "id",
	            fields: {
	                id: { editable: false },
	                nome: { editable: true },
	                cognome: { editable: true },
	                email: { editable: true },
	                telefono: { editable: true },
	                qualifica: { editable: true },
	                }
	            }
	        },
	    pageSize: 50,

      });
		<?php do_action('WPsCRM_grid_customer_contacts') ?>
});


	function annulla()
	{
		location.href="<?php echo admin_url('admin.php?page=smart-crm&p=clienti/list.php')?>";
	}
	function elimina()
	{
		if (!confirm("<?php _e('Löschen bestätigen? Es ist weiterhin möglich, den gelöschten Kunden wiederherzustellen ','cpsmartcrm')?>"))
			return;
		location.href="<?php echo admin_url('admin.php?page=smart-crm&p=clienti/delete.php&ID='.$ID)?>&security=<?php echo $delete_nonce?>";
	}
        function WPsCRM_ControllaCF(cf) {
            var validi, i, s, set1, set2, setpari, setdisp;
            if (cf == '') return '';
            cf = cf.toUpperCase();
            if (cf.length != 16)
                return "La lunghezza del codice fiscale non è\n"
		        + "corretta: il codice fiscale dovrebbe essere lungo\n"
		        + "esattamente 16 caratteri.\n";
            validi = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            for (i = 0; i < 16; i++) {

                if (validi.indexOf(cf.charAt(i)) == -1)
                    return "Il codice fiscale contiene un carattere non valido `" +
				        cf.charAt(i) +
				        "'.\nI caratteri validi sono le lettere e le cifre.\n";
            }
            set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
            setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
            s = 0;
            for (i = 1; i <= 13; i += 2)
                s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
            for (i = 0; i <= 14; i += 2)
                s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
            if (s % 26 != cf.charCodeAt(15) - 'A'.charCodeAt(0))
                return "Il codice fiscale non è corretto:\n" +
			        "il codice di controllo non corrisponde.\n";
            return "";
        }
        function WPsCRM_ControllaPIVA(pi) {
            if (pi == '') return '';
            if (pi.length != 11)
                return "La lunghezza della partita IVA non è\n" +
			        "corretta: la partita IVA dovrebbe essere lunga\n" +
			        "esattamente 11 caratteri.\n";
            validi = "0123456789";
            for (i = 0; i < 11; i++) {
                if (validi.indexOf(pi.charAt(i)) == -1)
                    return "La partita IVA contiene un carattere non valido `" +
				        pi.charAt(i) + "'.\nI caratteri validi sono le cifre.\n";
            }
            s = 0;
            for (i = 0; i <= 9; i += 2)
                s += pi.charCodeAt(i) - '0'.charCodeAt(0);
            for (i = 1; i <= 9; i += 2) {
                c = 2 * (pi.charCodeAt(i) - '0'.charCodeAt(0));
                if (c > 9) c = c - 9;
                s += c;
            }
            if ((10 - s % 10) % 10 != pi.charCodeAt(10) - '0'.charCodeAt(0))
                return "La partita IVA non è valida:\n" +
			        "il codice di controllo non corrisponde.\n";
            return '';
        }
        function WPsCRM_verifica(cod, el) {
        	var err = "";
            if (cod.length == 16) {
                err = WPsCRM_ControllaCF(cod);

            }
            else if (cod.length == 11){
                err = WPsCRM_ControllaPIVA(cod);

            }
            else if (cod.length > 0) {
                if(el=='p_iva')
                err = "Il codice introdotto non è valido:\n\n" +
			        "  - una partita IVA deve essere lunga 11 caratteri.\n";

            else if(el=='cod_fis'){
                err ="Il codice introdotto non è valido:\n\n" +
                " - un codice fiscale deve essere lungo 16 caratteri o essere una p.iva corretta nel caso di aziende;\n\n"
            }

            }
			if (err =="")
				return "ok";
            else
				return "ko";
            //if (err != '') {
            //	//jQuery('#' + el).closest('.form-row').removeClass('woocommerce-validated').addClass('woocommerce-invalid')
            //	//return "VALORE ERRATO\n\n" + err + "\nCorreggi e riprova!"
            //    //alert("VALORE ERRATO\n\n" + err + "\nCorreggi e riprova!");
            //    return;
            //}
            //else {
            //    //jQuery('#' + el).closest('.form-row').removeClass('woocommerce-invalid').addClass('woocommerce-validated')
            //    return true;
            //}
        }
		function save(e) {
		    var cf = jQuery("#cod_fis").val();
    		var customerValidator = jQuery("#form_insert").kendoValidator({
    			rules: {
    				hasCountry: function (input) {
    					var country = jQuery("#nazione").data("kendoDropDownList").value();
						if (input.is("[name=nazione]")) {
    						if (country == "0" || country == null) {
    							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				hasFiscalCode: function (input) {
    					var country = jQuery("#nazione").data("kendoDropDownList").value();
    					if (input.is("[name=cod_fis]")) {

    						if (jQuery('input[name="cod_fis"]').val() == "" && jQuery('input[name="p_iva"]').val() == "" && jQuery("#fatturabile_1").is(":checked")) {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
    							return false;
    						}
    						if (country == "IT" && jQuery("#fatturabile_1").is(":checked"))
    						{
    							console.log(WPsCRM_verifica(jQuery('input[name="cod_fis"]').val(), 'cod_fis'));
    							if (WPsCRM_verifica(jQuery('input[name="cod_fis"]').val(), 'cod_fis') != "ok") {
    								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
    								return false;
    							}
							}
    					}
    					//if (input.is("[name=p_iva]")) {
    					//	if (jQuery('input[name="p_iva"]').val() == "" && jQuery('input[name="cod_fis"]').val() == "" && jQuery("#fatturabile_1").is(":checked")) {
						////		jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
    					//		return false;
    					//	}
    					//	if (country == "IT") {
    					//		if (WPsCRM_verifica(jQuery('input[name="p_iva"]').val(), 'p_iva') != "ok") {
    					////			jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
    					//			return false;
    					//		}
    					//	}
    					//}
    					return true;
    				},
    				hasName: function (input) {
    					if (input.is("[name=nome]")) {
    						if (jQuery('input[name="nome"]').val() == "" && jQuery('input[name="ragione_sociale"]').val() == "") {
    							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				hasLastName: function (input) {
    					if (input.is("[name=cognome]")) {
    						if (jQuery('input[name="cognome"]').val() == "" && jQuery('input[name="ragione_sociale"]').val() == "") {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				hasBusinessName: function (input) {
    					if (input.is("[name=ragione_sociale]")) {
    						if (jQuery('input[name="ragione_sociale"]').val() == "" && (jQuery('input[name="nome"]').val() == "" || jQuery('input[name="cognome"]').val() == "")) {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				hasVAT: function (input) {
    					if (input.is("[name=p_iva]")) {
    						if (jQuery('input[name="p_iva"]').val() == "" && jQuery('input[name="cod_fis"]').val() == "" && jQuery("#fatturabile_1").is(":checked")) {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				isUser: function (input) {
    					if (input.is("[name=email]")) {
    						if ( (jQuery('input[name="username"]').val() == ""
								  || jQuery('input[name="password"]').val() == ""
								  || jQuery('input[name=email]').val() =="")
								&& jQuery('input[name="crea_utente"]:checked').length
								) {
    							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
    							return false;
    						}
    					}
    					if (input.is("[name=username]")) {
    						if ((jQuery('input[name="username"]').val() == ""
								  || jQuery('input[name="password"]').val() == ""
								  || jQuery('input[name=email]').val() == "")
								&& jQuery('input[name="crea_utente"]:checked').length
								) {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					if (input.is("[name=password]")) {
    						if ((jQuery('input[name="username"]').val() == ""
								  || jQuery('input[name="password"]').val() == ""
								  || jQuery('input[name=email]').val() == "")
								&& jQuery('input[name="crea_utente"]:checked').length
								) {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							return false;
    						}
    					}
    					return true;
    				},
    				isFiscallyDefined: function (input) {
    					if (input.is('input[name="tipo_cliente"]') ) {
    						if (jQuery('input[name="tipo_cliente"]').filter(':checked').length == 0) {

    							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");
    							jQuery('html, body').animate({
    								scrollTop: jQuery('input[name="tipo_cliente"]').offset().top-100
    							}, 300);
    							return false;
    						} else {
    							return true;
    						}
    					}
    					return true;
					}
    			},

    			messages: {
					hasCountry: "<?php _e('Du solltest das Kundenland auswählen','cpsmartcrm')?>",
    				hasFiscalCode: "<?php _e('Du solltest die GÜLTIGE Steuernummer oder Umsatzsteuer-Identifikationsnummer des Kunden eingeben','cpsmartcrm')?>",
    				hasName: "<?php _e('Du solltest den Vornamen oder den Firmennamen des Kunden eingeben.','cpsmartcrm')?>",
    				hasLastName: "<?php _e('Du solltest den Nachnamen oder den Firmennamen des Kunden eingeben.','cpsmartcrm')?>",
    				hasBusinessName: "<?php _e('Du solltest den Firmennamen oder den Vornamen und Nachnamen des Kunden eingeben','cpsmartcrm')?>",
    				hasVAT: "<?php _e('Wenn Du das Feld "Firmenname" verwendest, solltest Du die Umsatzsteuer-Identifikationsnummer eingeben','cpsmartcrm')?>",
    				isUser:"<?php _e('Um einen neuen Benutzer zu erstellen, musst Du E-Mail, Benutzername und Passwort festlegen','cpsmartcrm')?>",
    				isFiscallyDefined:"<?php _e('Bitte privat oder geschäftlich ankreuzen','cpsmartcrm')?>"
    			}
    		}).data("kendoValidator");

    		if (customerValidator.validate()) {
    			showMouseLoader();
				var form = jQuery('form');
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'WPsCRM_save_client',
						fields:form.serialize(),
						security:'<?php echo $update_nonce; ?>'

					},
					type: "POST",
					success: function (response) {
						console.log(response);
						if (response.indexOf('OK') != -1) {
							var tmp=response.split("~");
							var id_cli=tmp[1];
							hideMouseLoader();
							noty({
								text: "<?php _e('Der Kunde wurde gespeichert','cpsmartcrm')?>",
								layout: 'center',
								type: 'success',
								template: '<div class="noty_message"><span class="noty_text"></span></div>',
								//closeWith: ['button'],
								timeout: 1000
							});
							jQuery("#ID").val(id_cli);
							<?php if (! $ID) { ?>
							setTimeout(function () {
                                                            location.href="<?php echo admin_url('admin.php?page=smart-crm&p=clienti/form.php&ID=')?>" + id_cli;
							}, 1000)
							<?php } ?>

						}
						else {
							noty({
							text: "<?php _e('Etwas war falsch','cpsmartcrm')?>" + ": " + response,
							layout: 'center',
							type: 'error',
							template: '<div class="noty_message"><span class="noty_text"></span></div>',
							closeWith: ['button'],
							//timeout: 1000
						});
						}

					}
				})
    		}
		}


</script>
<script id="tooltipTemplate" type="text/x-kendo-template">
    <div style="background-color:rgba(57,57,57,.8);border:2px solid rgb(204,204,204);color:rgb(250,250,250);border-radius:6px;display:block;width:240px;height:100px">#=target.data('title')#</div>
</script>
<form name="form_insert" method="post" id="form_insert">
<input type="hidden" name="ID" id="ID" value="<?php echo $ID?>">
    
    <h3><?php if ($ID) { ?> <?php _e('Kunde','cpsmartcrm')?>: <?php echo "<span class=\"header_customer\">".stripslashes($cliente)."</span>";
			  } else{
        ?> <?php _e('Neukunde','cpsmartcrm')?> <?php } ?>
    </h3>

    <ul class="select-action">
		<li class="btn btn-sm _flat">
            <span class="crmHelp crmHelp-dark" data-help="customerForm" style="position:relative;top:-3px" data-role="tooltip"></span>
		</li>
        <?php if ($ID){?>
		<li class="btn btn-success btn-sm _flat _showLoader saveForm" onclick="save();return false;">
			<i class="glyphicon glyphicon-floppy-disk"></i>
			<b>
				<?php _e('Speichern','cpsmartcrm')?>
			</b>
		</li>
        <li onClick="annulla();return false;" class="btn btn-warning btn-sm _flat resetForm">
            <i class="glyphicon glyphicon-floppy-remove"></i>
            <b> <?php _e('Zurücksetzen','cpsmartcrm')?></b>
        </li>

        <li onClick="elimina();return false;" class="btn btn-danger btn-sm _flat deleteForm" style="margin-right:10px">
            <i class="glyphicon glyphicon-remove"></i>
            <b> <?php _e('Löschen','cpsmartcrm')?></b>
        </li>
        <li class="_tooltip"><i class="glyphicon glyphicon-menu-right"></i></li>
        <li class="btn btn-info btn-sm _flat btn_todo" style="margin-left:10px" title="<?php _e('NEUE TODO','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-tag"></i>
            <b> </b>
        </li>
        <li class="btn  btn-sm _flat btn_appuntamento" title="<?php _e('NEUER TERMIN','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-pushpin"></i>
            <b> </b>
        </li>
        <li class="btn btn-primary btn-sm _flat btn_activity" title="<?php _e('NEUE ANMERKUNG','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-option-horizontal"></i>
            <b> </b>
        </li>
        <?php do_action('WPsCRM_advanced_buttons',$email);?>
        <?php } ?>
    </ul>
    <div id="tabstrip" style="margin-top:14px">
        <ul>
            <li id="tab1"><?php _e('Stammdaten','cpsmartcrm')?></li>
            <?php
			if ($ID){
            ?>
            <li id="tab2"><?php _e('Kontakte','cpsmartcrm')?></li>
            <li id="tab3"><?php _e('Angeboten','cpsmartcrm')?></li>
            <li id="tab4"><?php _e('Zusammenfassung','cpsmartcrm')?></li>
            <?php 
				do_action('WPsCRM_add_tabs_to_customer_form');
			} ?>
        </ul>
        <!-- TAB 1 -->
        <div>
            <div id="d_anagrafica" style="position:relative">
                <div class="row form-group">
					<label class="col-sm-1 control-label">
						<?php _e('Datum','cpsmartcrm')?>
					</label>
					<div class="col-sm-2">
						<input type="text" id="data_inserimento" name="data_inserimento" />
					</div>
					<?php do_action('WPsCRM_display_anagrafiche_in_form') ?>

                </div>
				<div class="row form-group">
					<label class="col-sm-1 control-label">
						<?php _e('Rechnungspflichtig?','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">
                        <span style="margin-right:20px"><input type="radio" name="fatturabile" id="fatturabile_1" value="1" <?php if (isset($riga) && $riga["fatturabile"]==1) echo "checked"?> /><?php _e('Ja','cpsmartcrm')?></span>
                        <span><input type="radio" name="fatturabile" id="fatturabile_2" value="0" <?php if ((isset($riga) && $riga["fatturabile"]==0) || !isset($riga)) echo "checked"?> /><?php _e('Nein','cpsmartcrm')?></span>
					</div>

					<label class="col-sm-1 control-label">
						<?php _e('Typ','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">
						<input type="radio" name="tipo_cliente" value="1" <?php if (isset($riga) && $riga["tipo_cliente"]==1) echo "checked"?> /><?php _e('Privat','cpsmartcrm')?>
						<input type="radio" name="tipo_cliente" value="2" <?php if (isset($riga) && $riga["tipo_cliente"]==2) echo "checked"?> /><?php _e('Business','cpsmartcrm')?>
					</div>
				</div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Land','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <select data-nazione="<?php if(isset($riga)) echo $riga["nazione"]?>" id="nazione" name="nazione" size="20" maxlength='50'><?php if(isset($riga['nazione'])) echo stripslashes( WPsCRM_get_countries($riga["nazione"]) ); else echo stripslashes( WPsCRM_get_countries('IT'))?></select>
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Firmenname','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="ragione_sociale" maxlength='250' value="<?php if(isset($riga)) echo stripslashes($riga["ragione_sociale"])?>" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Steuer-Code','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="cod_fis" id="cod_fis" value="<?php if(isset($riga)) echo $riga["cod_fis"]?>" class="form-control _toCheck"  readonly title="<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Umsatzsteuer-Identifikationsnummer','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="p_iva" id="p_iva" value="<?php if(isset($riga)) echo $riga["p_iva"]?>" class="form-control _toCheck"  readonly title="<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Vorname','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="nome" value="<?php if(isset($riga)) echo stripslashes($riga["nome"])?>" class="form-control">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Nachname','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="cognome" value="<?php if(isset($riga)) echo stripslashes($riga["cognome"])?>" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Addresse','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="indirizzo" size="50" maxlength='50' value="<?php if(isset($riga)) echo stripslashes($riga["indirizzo"])?>" class="form-control">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('PLZ','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="cap" size="10" maxlength='10' value="<?php if(isset($riga)) echo $riga["cap"]?>" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Stadt','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="localita" size="50" maxlength='55' value="<?php if(isset($riga)) echo stripslashes($riga["localita"])?>" class="form-control">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Staat/Prov.','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="provincia" size="5" maxlength='5' value="<?php if(isset($riga)) echo $riga["provincia"]?>" class="form-control">
                    </div>
                </div>
                <div class="row form-group">

                    <label class="col-sm-1 control-label"><?php _e('Telefon','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="telefono1" size="20" maxlength='50' value="<?php if(isset($riga)) echo $riga["telefono1"]?>" class="form-control">
                    </div>
					<label class="col-sm-1 control-label">
						<?php _e('Fax','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">
						<input type="text" name="fax" size="20" maxlength='50' value="<?php if(isset($riga)) echo $riga["fax"]?>" class="form-control" />
					</div>
                   
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Mobil','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="telefono2" size="20" maxlength='50' value="<?php if(isset($riga)) echo $riga["telefono2"]?>" class="form-control">
                    </div>
					<label class="col-sm-1 control-label">
						<?php _e('Email','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">
						<input type="text" name="email" size="20" maxlength='50' value="<?php if(isset($riga)) echo $riga["email"]?>" class="form-control" />
					</div>
                    
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Geburtsort','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="luogo_nascita" size="20" maxlength='50' value="<?php if(isset($riga)) echo stripslashes($riga["luogo_nascita"] )?>" class="form-control">
                    </div>
					<label class="col-sm-1 control-label">
						<?php _e('Skype','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">
						<input type="text" name="skype" size="20" maxlength='100' value="<?php if(isset($riga)) echo $riga["skype"]?>" class="form-control" />
					</div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Geburtsdatum','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" id="data_nascita" name="data_nascita" value="<?php if(isset($riga)) echo WPsCRM_inverti_data($riga["data_nascita"])?>">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Kategorie','cpsmartcrm')?></label>
                    <div class="col-sm-4">
						<input id="customerCategory"  name="customerCategory" value="<?php if(isset($riga)) echo $riga["categoria"]?>" /> 
                        <?php
						$cats=get_terms('WPsCRM_customersCat',array('hide_empty'=>false));
                        ?>
                    </div>
                    <script>

						<?php
                    	echo "var cats = [";
						if( ! empty($cats) ){

							foreach($cats as $cat)
								echo '{text:"'.$cat->name.'",id:"'.$cat->term_id.'"},';

						 } 
							echo "];".PHP_EOL;
                        ?>
                    	jQuery(document).ready(function ($) {
							$('#customerCategory').kendoMultiSelect({
								dataTextField: "text",
								dataValueField: "id",
								dataSource: cats,
								placeholder: "<?php _e('Wählen','cpsmartcrm')?>",
								noDataTemplate: '<?php _e('Keine Kategorien; Erstelle Kategorien in den CRM-Einstellungen ->Seite „Kundeneinstellungen“.','cpsmartcrm')?>',
								change: function () {
									$('input[name="customerCategory"]').val($('#customerCategory').data("kendoMultiSelect").value())
								}
							})
							var categories = $("#customerCategory").data("kendoMultiSelect")
							categories.value([<?php if(isset($riga)) echo $riga["categoria"]?>]);
                    	})
						
					</script>
                </div>
                <div class="row form-group">
					<label class="col-sm-1 control-label"><?php _e('Webseite','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="sitoweb" size="20" maxlength='50' value="<?php if(isset($riga)) echo $riga["sitoweb"]?>" class="form-control">
                    </div>
					<label class="col-sm-1 control-label">
						<?php _e('Interessen','cpsmartcrm')?>
					</label>
					<div class="col-sm-4">

						<input id="customerInterests" name="customerInterests" value="<?php if(isset($riga)) echo $riga["interessi"]?>" />
						<?php
						$ints=get_terms('WPsCRM_customersInt',array('hide_empty'=>false));
                        ?>
					</div>
					<script>
						<?php
                    	echo "var ints = [";
						if( ! empty($ints) ){

							foreach($ints as $int)
								echo '{text:"'.$int->name.'",id:"'.$int->term_id.'"},';
							
							}
						echo "];".PHP_EOL;
                        ?>
                    	jQuery(document).ready(function ($) {
                    		$('#customerInterests').kendoMultiSelect({
								dataTextField: "text",
								dataValueField: "id",
								placeholder: "<?php _e('Wählen','cpsmartcrm')?>",
								dataSource: ints,
								noDataTemplate: '<?php _e('Keine Interessen; Erstelle Interessen in den CRM-Einstellungen ->Seite „Kundeneinstellungen“.','cpsmartcrm')?>',
								change: function () {
									$('input[name="customerInterests"]').val($('#customerInterests').data("kendoMultiSelect").value())
								}
							})
                    		var interests = $("#customerInterests").data("kendoMultiSelect")
                    		interests.value([<?php if(isset($riga)) echo $riga["interessi"]?>]);
                    	})
						
					</script>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label" <?php echo $style_disabled?>><?php _e('Agent','cpsmartcrm')?>:</label>
                    <div class="col-sm-4" <?php echo $style_disabled?>>
                        <select id="selectAgent" name="selectAgent" <?php echo $agent_disabled?> style="width:54%" ></select>
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Kommt von','cpsmartcrm')?>:</label>
					<div class="col-sm-4">
						<input id="customerComesfrom" name="customerComesfrom" value="<?php if(isset($riga)) echo $riga["provenienza"]?>"  />
						<?php
						$provs=get_terms('WPsCRM_customersProv',array('hide_empty'=>false));
                        ?>
					</div>
					<script>
						<?php
                    	echo "var provs = [";
						if( ! empty($provs) ){
							foreach($provs as $prov)
								echo '{text:"'.$prov->name.'",id:"'.$prov->term_id.'"},';
						}
						echo "];".PHP_EOL;
                        ?>
                    	jQuery(document).ready(function ($) {
							$('#customerComesfrom').kendoMultiSelect({
								dataTextField: "text",
								dataValueField: "id",
								placeholder: "<?php _e('Wählen','cpsmartcrm')?>",
								dataSource: provs,
								noDataTemplate: "<?php _e('Keine Quellen; erstelle Quellen in den CRM-Einstellungen ->Kundeneinstellungen Seite','cpsmartcrm')?>",
								change: function () {
									$('input[name="customerComesfrom"]').val($('#customerComesfrom').data("kendoMultiSelect").value())
								}
							})
							var categories = $("#customerComesfrom").data("kendoMultiSelect")
							categories.value([<?php if(isset($riga)) echo $riga["provenienza"]?>]);
                    	})

					</script>
                </div>
				<div class="advanced" style="position:relative">
				<?php //if ( $ID ) do_action('WPsCRM_add_rows_to_customer_form', $custom_tax , $ID, $email );?>
				<?php do_action('WPsCRM_add_rows_to_customer_form', $custom_tax , $ID, $email );?>
				</div>
                <?
				//if ( ! $riga["user_id"])
				//{
                ?>
                <!--<div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Benutzer erstellen','cpsmartcrm')?>?</label>
                    <div class="col-sm-1">
                        <input type="checkbox" name="crea_utente" value="1">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-sm-1 control-label"><?php _e('Benutzername','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="username" size="20" maxlength='50' value="" class="form-control">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e('Passwort','cpsmartcrm')?></label>
                    <div class="col-sm-4">
                        <input type="text" name="password" size="20" maxlength='50' value="" class="form-control">
                    </div>
                </div>-->
                <?//}?>
            </div>
        </div>
        <!--END TAB 1 -->
        <!-- TAB 2 -->
        <?php if ($ID){?>
        <div>

            <div id="grid_contacts"></div>

        </div>
        <!--END TAB 2 -->
        <!--TAB 3 -->

        <div>
            <!--<h2 style="text-align:center"><?php _e('Angeboten','cpsmartcrm')?></h2>-->
            <div style="min-height: 200px">
                <div id="annotation">
                    <h3 style="text-align:center"><?php _e('Notizen-Zeitleiste','cpsmartcrm')?> 
                        <span class="btn btn-primary btn-sm _flat btn_activity" title="<?php _e('NEUE ANMERKUNG','cpsmartcrm')?>">
                            <i class="glyphicon glyphicon-option-horizontal"></i>
                        </span>
                    </h3>
                    <div>

                        <section id="cd-timeline" class="cd-container">
                            <?php WPsCRM_timeline_annotation($riga["annotazioni"])?>

                        </section>
                    </div>
                </div>
            </div>
        </div>

        <!-- END TAB 3 -->
        <!-- TAB 4 -->
        <div>
            <div id="grid"></div>
        </div>
        <!-- END TAB 4 -->
        <?php
			do_action('WPsCRM_add_divs_to_customer_form',$email, $ID);
              } ?>
    </div>

    <br>
    <input type="submit" style="display:none" />

    <ul class="select-action">

        <li class="btn btn-success btn-sm _flat _showLoader saveForm" onclick="save()">
            <i class="glyphicon glyphicon-floppy-disk"></i>
            <b>
                <?php _e('Speichern','cpsmartcrm')?>
            </b>
        </li>
        <li onClick="annulla();return false;" class="btn btn-warning btn-sm _flat resetForm">
            <i class="glyphicon glyphicon-floppy-remove"></i>
            <b> <?php _e('Zurücksetzen','cpsmartcrm')?></b>
        </li>
        <?php if ($ID){?>
        <li class="btn btn-danger btn-sm _flat deleteForm" style="margin-right:10px">
            <i class="glyphicon glyphicon-remove"></i>
            <b onClick="elimina();return false;"> <?php _e('Löschen','cpsmartcrm')?></b>
        </li>
        <li class="_tooltip"><i class="glyphicon glyphicon-menu-right"></i></li>
        <li class="btn btn-info btn-sm _flat btn_todo" style="margin-left:10px" title="<?php _e('NEUE TODO','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-tag"></i>
            <b> </b>
        </li>
        <li class="btn  btn-sm _flat btn_appuntamento" title="<?php _e('NEUER TERMIN','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-pushpin"></i>
            <b> </b>
        </li>
        <li class="btn btn-primary btn-sm _flat btn_activity" title="<?php _e('NEUE ANMERKUNG','cpsmartcrm')?>">
            <i class="glyphicon glyphicon-option-horizontal"></i>
            <b> </b>
        </li>
		<?php do_action('WPsCRM_advanced_buttons',$email);?>
		<?php }
		?>
    </ul>
</form>

<div id="dialog-view" style="display:none;margin: 0 auto; text-align: center; z-index: 1000; width: 100%; height: 100%; background: url('<?php echo str_replace("inc/crm/clienti/","",plugin_dir_url( __FILE__ ))?>css/img/bg_w_tr.png');position: absolute;left: 0;top:0;"  class="_modal" data-from="clienti">

</div>
<div id="createPdf"></div>
<div id="createInvoice"></div>
<div id="createQuote"></div>
<script>

    var media_uploader = null;

    function open_media_uploader_multiple_images() {
        media_uploader = wp.media({
            frame: "post",
            state: "insert",
            multiple: true
        });

        media_uploader.on("insert", function () {

            var length = media_uploader.state().get("selection").length;
            var images = media_uploader.state().get("selection").models
            console.log(images);

            for (var iii = 0; iii < length; iii++) {
                var image_url = images[iii].changed.url;
                console.log(image_url);
                jQuery('.thumbContainer').append('<img src="' + image_url.replace(".jpg", "-150x150.jpg") + '">')
                var image_caption = images[iii].changed.caption;
                var image_title = images[iii].changed.title;
            }
        });

        media_uploader.open();
    }

    jQuery(document).ready(function ($) {
    	$('._showLoader').click(function (e) {
    		$('#mouse_loader').offset({ left: e.pageX, top: e.pageY });
    	})
	var invoiceWindow = $("#createInvoice").kendoWindow({
		width: "90%",
		height: "84%",
		title: "<i class=\"glyphicon glyphicon-fire\"></i> <?php _e('Rechnung erstellen für','cpsmartcrm');if(isset($cliente)) echo " ". stripslashes($cliente)?>",
		//content:"<?php echo admin_url('admin.php?page=smart-crm&p=documenti%2Fform_invoice.php&cliente='.$ID)?>&layout=iframe",
		iframe: true,
		visible: false,
		modal: true,
		draggable: false,
		pinned: true,
		actions: [

			"Close"
		]
	}).data('kendoWindow')
		var quoteWindow = $("#createQuote").kendoWindow({
		width: "90%",
		height: "84%",
		title: "<i class=\"glyphicon glyphicon-send\"></i> <?php _e('Angebot erstellen für','cpsmartcrm');if(isset($cliente)) echo " ". stripslashes($cliente)?>",
		//content:"<?php echo admin_url('admin.php?page=smart-crm&p=documenti%2Fform_quotation.php&cliente='.$ID)?>&layout=iframe",
		iframe: true,
		visible: false,
		modal: true,
		draggable: false,
		resizable:false,
		pinned: true,
		actions: [

			"Close"
		]
		}).data('kendoWindow')

		<?php do_action('WPsCRM_menu_tooltip') ?>

		<?php if($ID){ ?>
    	$('#cd-timeline').on('click','.glyphicon-remove', function () {
    		var complete=false;
    		var $this=$(this).closest('.cd-timeline-block');
    		var index=$this.data('index');
    		$.ajax({
    			url: ajaxurl,
    			data: {'action': 'WPsCRM_delete_annotation',
    				'id_cliente': '<?php echo $ID ?>',
    				'index':index,
	                'security':'<?php echo $delete_nonce; ?>'},
    			type: "POST",
    			success: function (response) {
    				console.log(response);
    				noty({
    					text: "<?php _e('Anmerkung wurde gelöscht','cpsmartcrm')?>",
    					layout: 'center',
    					type: 'success',
    					template: '<div class="noty_message"><span class="noty_text"></span></div>',
    					//closeWith: ['button'],
    					timeout: 1000
    				});
    				complete=true;
    				$("*[data-index=" + index + "]").fadeOut(200);
    				//$this.hide();

    			}
    		})

    	})

		<?php } ?>

        var timelineBlocks = $('.cd-timeline-block'),
       offset = 0.8;

        //hide timeline blocks which are outside the viewport
        hideBlocks(timelineBlocks, offset);

        //on scolling, show/animate timeline blocks when enter the viewport
        $(window).on('scroll', function () {

            (!window.requestAnimationFrame)
                ? setTimeout(function () { showBlocks(timelineBlocks, offset); }, 100)
                : window.requestAnimationFrame(function () { showBlocks(timelineBlocks, offset); });
        });


        function hideBlocks(blocks, offset) {

            blocks.each(function () {

                ($(this).offset().top > $(window).scrollTop() + $(window).height() * offset) && $(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');

            });
        }

        function showBlocks(blocks, offset) {
            blocks.each(function () {
                ($(this).offset().top <= $(window).scrollTop() + $(window).height() * offset && $(this).find('.cd-timeline-img').hasClass('is-hidden')) && $(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
            });
        }
//validator for modal forms (appointment, todo)

//update delle activity da modale
        $(document).on('click', '#save_activity_from_modal', function () {
            var id = $(this).data('id');
            $('.modal_loader').show();
            $.ajax({
                url: ajaxurl,
                method:'POST',
                data: {
                	'action': 'WPsCRM_scheduler_update',
                    'ID': id,
                    'fatto': $('input[type="radio"][name="fatto"]:checked').val(),
                    'esito': $('#esito').val(),
                    'security':'<?php echo $scheduler_nonce; ?>'
                },
                success: function (response) {
                    var newDatasource = new kendo.data.DataSource({
                        transport: {
                            read: function (options) {
                                jQuery.ajax({
                                    url: ajaxurl,
                                    data: {
                                    	action: 'WPsCRM_get_client_scheduler',
                                        id_cliente: '<?php echo $ID?>'
                                    },
                                    success: function (result) {
                                        //console.log(result);
                                        jQuery("#grid").data("kendoGrid").dataSource.data(result.scheduler);

                                    },
                                    error: function (errorThrown) {
                                        console.log(errorThrown);
                                    }
                                })
                            }
                        },
                        schema: {
                            model: {
                                id: "id_agenda",
                                fields: {
                                    tipo: { editable: false },
                                    oggetto: { editable: false },
                                    annotazioni: { editable: false },
                                    data_scadenza: { type: "date", editable: false },
                                }
                            }
                        },
                        pageSize: 50,
                    });
                    setTimeout(function () {
                        $('.modal_loader').fadeOut('fast');
                    }, 300);
                    setTimeout(function () {
                        $('._modal').fadeOut('fast');
                    }, 500);

                    var grid = $('#grid').data("kendoGrid");
                    setTimeout(function () {
                        grid.setDataSource(newDatasource);
                        grid.dataSource.read();
                    }, 600);

                    setTimeout(function () { grid.refresh() }, 700);


                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            })

        })

		$("#data_inserimento").kendoDatePicker({
            value: new Date(), format: $format
        });
		$("#data_nascita").kendoDatePicker({format: $format
        });
		$('.btn_invoice').click(function () {
			invoiceWindow.refresh("<?php echo admin_url('admin.php?page=smart-crm&p=documenti%2Fform_invoice.php&cliente=').$ID?>" + "&layout=iframe");
			invoiceWindow.center().open();
		})
    	$('.btn_quote').click(function () {
    		quoteWindow.refresh("<?php echo admin_url('admin.php?page=smart-crm&p=documenti%2Fform_quotation.php&cliente=').$ID?>" + "&layout=iframe");
    		quoteWindow.center().open();
    	})


        var _users = new kendo.data.DataSource({

            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                        	'action': 'WPsCRM_get_CRM_users_customer'
                           // 'role': 'CRM_agent',
                           // 'include_admin':true
                        },
                        success: function (result) {
                            if($("#selectAgent").length)
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
			optionLabel: "Select Agent...",
            dataTextField: "display_name",
            dataValueField: "ID",
            dataSource: _users,
        });
        agente='<?php if(isset($agente)) echo $agente?>';
        if (agente>0)
            $("#selectAgent").data('kendoDropDownList').value(agente);
        $('#categoria').kendoDropDownList({});
        $('#provenienza').kendoDropDownList({});

    	$('#nazione').kendoDropDownList({
    		placeholder: "<?php _e('Land auswählen','cpsmartcrm') ?>...",
    	});
    	var country = jQuery("#nazione").data("kendoDropDownList").value();
    	if (country != "0") {
    		$('._toCheck').attr({ 'readonly': false, 'title': '' })
    	}
    	else {
    		$('._toCheck').attr({ 'readonly': 'readonly' , 'title': '<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...' , 'alt': '<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...' })
    	}
    	$('#nazione').on('change', function () {
    		if ($(this).val() != "0") {
    			$('._toCheck').attr({ 'readonly': false , 'title':'' })
    		}
    		else {
    			$('._toCheck').attr({ 'readonly': 'readonly' , 'title': '<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...' , 'alt': '<?php _e('Wähle zuerst das Land aus','cpsmartcrm') ?>...' })
    		}
    	})
    	setTimeout(function () {
    		var divList = $(".cd-timeline-block");
    		divList.sort(function (a, b) {
    			var date1 = $(a).data("date");
    			date1 = date1.split('-');
    			date1 = new Date(date1[0], date1[1] - 1, date1[2]);
    			var date2 = $(b).data("date");
    			date2 = date2.split('-');
    			date2 = new Date(date2[0], date2[1] - 1, date2[2]);
    			return date1 < date2;
    		}).appendTo('#_timeline');
    		$('#_timeline').fadeIn('fast')
    	}, 50)

});

</script>
<style>
    input[type=checkbox] {
        float: initial;
    }
</style>
