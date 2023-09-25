<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
	jQuery(document).ready(function ($) {
		$("#dialog_appuntamento").kendoWindow({
			width: "86%",
			height: "80%",
			title: "<?php _e('Add appointment for Customer:','cpsmartcrm') ?>",
			visible: false,
			modal: true,
			draggable: false,
			resizable:false,
			pinned: true,
			actions: [

				"Close"
			]
			, close: function () { setTimeout(function () { $('.k-overlay').hide() }, 100); }
		});
		var a_userSource = new kendo.data.DataSource({

			transport: {
				read: function (options) {
					$.ajax({
						url: ajaxurl,
						data: {
							'action': 'WPsCRM_get_CRM_users',

						},
						success: function (result) {
							//console.log(result);
							$("#a_remindToUser").data("kendoMultiSelect").dataSource.data(result);
						},
						error: function (errorThrown) {
							console.log(errorThrown);
						}
					})
				}
			}
		});
		var a_roleSource = new kendo.data.DataSource({

			transport: {
				read: function (options) {
					$.ajax({
						url: ajaxurl,
						data: {
							'action': 'WPsCRM_get_registered_roles',
						},
						success: function (result) {
							//console.log(result);
							$("#a_remindToGroup").data("kendoMultiSelect").dataSource.data(result.roles);
						},
						error: function (errorThrown) {
							console.log(errorThrown);
						}
					})
				}
			}
		});
	$("#a_data_scadenza_inizio").kendoDateTimePicker({
		value: new Date(), format: $formatTime
	});
	$("#a_data_scadenza_fine").kendoDateTimePicker({
		value: new Date(), format: $formatTime
	});
	var a_users = $('#a_remindToUser').kendoMultiSelect({
		placeholder: "<?php _e( 'Select user', 'cpsmartcrm'); ?>...",
		dataTextField: "display_name",
		dataValueField: "ID",
		autoBind: false,
		dataSource: a_userSource,
		change: function (e) {
			var selectedUsers = (this.value()).clean("");
			$('#a_selectedUsers').val(selectedUsers)
		},
		dataBound: function (e) {
			var selectedUsers = (this.value()).clean("");
			$('#a_selectedUsers').val(selectedUsers)
		}
	}).data("kendoMultiSelect")

	$('#a_remindToGroup').kendoMultiSelect({
		placeholder: "<?php _e( 'Select group', 'cpsmartcrm'); ?>...",
		dataTextField: "name",
		dataValueField: "role",
		autoBind: false,
		dataSource: a_roleSource,
		change: function (e) {
			var a_selectedGroups = (this.value()).clean("");
			$('#a_selectedGroups').val(a_selectedGroups)
		},
		dataBound: function (e) {
			var a_selectedGroups = (this.value()).clean("");
			$('#a_selectedGroups').val(a_selectedGroups)
		}

	});
	a_users.value([<?php echo wp_get_current_user()->ID ?>]);
	function saveAppointment(){
		var opener = $('#dialog_appuntamento').data('from')

		if(opener =="clienti")
			id_cliente ='<?php if (isset($ID)) echo $ID?>'
		else if (opener == 'documenti')
			id_cliente = '<?php if (isset($fk_clienti)) echo $fk_clienti?>';
		else if (opener == 'list')
			id_cliente = $('#dialog_appuntamento').data('fkcliente');
        tipo_agenda = '2';
        scadenza_inizio = $("#a_data_scadenza_inizio").val();
        scadenza_fine = $("#a_data_scadenza_fine").val();
        scadenzaTimestamp = $("#a_data_scadenza_inizio").data('kendoDateTimePicker').value();
        annotazioni = $("#a_annotazioni").val();
        oggetto = $("#a_oggetto").val();
        priorita = $("#priorita").val();
        users = $("#a_selectedUsers").val();
        groups = $("#a_selectedGroups").val();
        days = $("#a_ruleStep").val();
        var s = "[";
        s += '{"ruleStep":"' + days + '" ,"remindToCustomer":';
        if ($('#a_remindToCustomer').prop('checked'))
            s += '"on"';
        else
            s += '""';
        s += ',"selectedUsers":"' + users + '"';
        s += ',"selectedGroups":"' + groups + '"';
        s += ',"userDashboard":';
        if ($('#a_userDashboard').prop('checked'))
            s += '"on"';
        else
            s += '""';
        s += ',"groupDashboard":';
        if ($('#a_groupDashboard').prop('checked'))
            s += '"on"';
        else
            s += '""';
        s += ',"mailToRecipients":';
        if ($('#a_mailToRecipients').prop('checked'))
            s += '"on"';
        else
            s += '""';
        s += '}'
        s += ']';
        var grid = $('#grid').data("kendoGrid");
        $('.modal_loader').show();
        $.ajax({
            url: ajaxurl,
            data: {
            'action': 'WPsCRM_save_todo',
            'id_cliente': id_cliente,
            tipo_agenda: tipo_agenda,
            scadenza_inizio: scadenza_inizio,
            scadenza_fine: scadenza_fine,
            scadenza_timestamp: scadenzaTimestamp,
            annotazioni: annotazioni,
            oggetto: oggetto,
            priorita: priorita,
            'steps': encodeURIComponent(s),
			'security':'<?php echo $scheduler_nonce; ?>'
			},
            type: "POST",
            success: function (response) {
            if (opener == "clienti") {//ricarico la grid solo se aperto da form clienti
              	var newDatasource = new kendo.data.DataSource({
              		transport: {
              			read: function (options) {
              				jQuery.ajax({
              					url: ajaxurl,
              					data: {
              						'action': 'WPsCRM_get_client_scheduler',
              						'id_cliente': id_cliente
              					},
              					success: function (result) {
              						console.log(result);
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
              		$("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab("#tab4");
              	}, 500);

              	//
              	setTimeout(function () {
              		grid.setDataSource(newDatasource);
              		grid.dataSource.read();
              	}, 600);

              	setTimeout(function () { grid.refresh() }, 700);
            }
            else {
				noty({
	                text: "<?php _e('Appointment has been added','cpsmartcrm')?>",
	                layout: 'center',
	                type: 'success',
	                template: '<div class="noty_message"><span class="noty_text"></span></div>',
	                //closeWith: ['button'],
	                timeout: 1000
				});
            }
            $("#dialog_appuntamento").data('kendoWindow').close();

            $('#new_appointment').find(':reset').click();

            }
        })
	}

	var a_validator = $("#new_appointment").kendoValidator({
            rules: {
                hasClients: function (input) {
                    if (input.is("[name=fk_clienti]")) {

                        var kb = $("#fk_clienti").data("kendoDropDownList").value();
                        if (kb.length == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }

                    }

                    return true;
                },
            	hasExpiration: function (input) {
            		if (input.is("[name=a_data_scadenza_inizio]")) {

            			var kb = $("#a_data_scadenza_inizio").val();
						if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }
					}
            	if (input.is("[name=a_data_scadenza_fine]")) {

            		var kb = $("#a_data_scadenza_fine").val();
						if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }

					}
            		if (input.is("[name=t_data_scadenza]")) {

            			var kb = $("#t_data_scadenza").val();
						if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }

					}
                    return true;
                },
                hasObject: function (input) {
                    if (input.is("[name=a_oggetto]")) {
                        var kb = $("#a_oggetto").val();
                        if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }
                    }
                    if (input.is("[name=t_oggetto]")) {
                            var kb = $("#t_oggetto").val();
                            if (kb == "") {

								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                                return false;
                            };
                    }
                    return true;
                },
				hasDays: function (input) {
					if (input.is("[name=a_ruleStep]")) {

						var kb = $("#a_ruleStep").val();
						if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }

					}
				if (input.is("[name=t_ruleStep]")) {

						var kb = $("#t_ruleStep").val();
						if (kb == "") {

							jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
                            return false;
                        }

					}

                    return true;
				},
				hasNoty: function (input) {
					if (input.is("[name=a_remindToUser]") || input.is("[name=a_remindToGroup]")) {
	            			var kb = jQuery("#a_remindToUser").data("kendoMultiSelect").value();
	            			var kb1 = jQuery("#a_remindToGroup").data("kendoMultiSelect").value();

	            			if (kb == "" && kb1 == "") {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");

	            				return false;
	            			}

	            	}

					if (input.is("[name=t_remindToUser]") || input.is("[name=t_remindToGroup]") ) {
	            			var kb = jQuery("#t_remindToUser").data("kendoMultiSelect").value();
	            			var kb1 = jQuery("#t_remindToGroup").data("kendoMultiSelect").value();

	            			if (kb == "" && kb1 == "") {
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2");

	            				return false;
	            			}

	            	}

	            	return true;
	            },

            },

        	messages: {
				hasDays:"<?php _e('You should select how many days in advance to activate the notification','cpsmartcrm')?>",
				hasNoty:"<?php _e('You should select a user or a group of users to notify to','cpsmartcrm')?>",
                hasClients: "<?php _e('You should select a client','cpsmartcrm')?>",
        		hasObject: "<?php _e('You should type a subject for this item','cpsmartcrm')?>",
				hasExpiration:"<?php _e('You should select  date for this event','cpsmartcrm')?>"

            }
	}).data("kendoValidator");
		$("#a_saveStep").click(function () {
			if (a_validator.validate())
				saveAppointment();
		});
		$('._reset').click(function () {
			$("#dialog_appuntamento").data('kendoWindow').close();
		})
})
</script>
