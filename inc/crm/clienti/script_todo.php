<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<script>
	jQuery(document).ready(function ($) {
		$("#dialog_todo").kendoWindow({
			width: "86%",
			height: "80%",
			title: "<?php _e('Aufgaben für den Kunden hinzufügen:','cpsmartcrm') ?>",
			visible: false,
			modal: true,
			draggable: false,
			resizable:false,
			pinned: true,
			actions: [

				"Close"
			],
			close: function () {
				//setTimeout(function () { $('.k-overlay').hide() }, 100);
				this.title("<?php _e('Aufgaben für den Kunden hinzufügen:','cpsmartcrm') ?>");
				$('.modal_loader').hide();
			}
		})
		var t_userSource = new kendo.data.DataSource({

			transport: {
				read: function (options) {
					$.ajax({
						url: ajaxurl,
						data: {
							'action': 'WPsCRM_get_CRM_users',

						},
						success: function (result) {
							//console.log(result);
							$("#t_remindToUser").data("kendoMultiSelect").dataSource.data(result);
						},
						error: function (errorThrown) {
							console.log(errorThrown);
						}
					})
				}
			}
		});
		var t_roleSource = new kendo.data.DataSource({

			transport: {
				read: function (options) {
					$.ajax({
						url: ajaxurl,
						data: {
							'action': 'WPsCRM_get_registered_roles',
						},
						success: function (result) {
							//console.log(result);
							$("#t_remindToGroup").data("kendoMultiSelect").dataSource.data(result.roles);
						},
						error: function (errorThrown) {
							console.log(errorThrown);
						}
					})
				}
			}
		});
		$("#t_data_scadenza").kendoDateTimePicker({
			value: new Date(),
			format: $formatTime
		});
		var t_users = jQuery('#t_remindToUser').kendoMultiSelect({
			placeholder: "<?php _e( 'Benutzer wählen', 'cpsmartcrm'); ?>...",
			dataTextField: "display_name",
			dataValueField: "ID",
			autoBind: false,
			dataSource: t_userSource,
			change: function (e) {
				var t_selectedUsers = (this.value()).clean("");
				jQuery('#t_selectedUsers').val(t_selectedUsers)
			},
			dataBound: function (e) {
				var t_selectedUsers = (this.value()).clean("");
				jQuery('#t_selectedUsers').val(t_selectedUsers)
			}
		}).data("kendoMultiSelect")

		jQuery('#t_remindToGroup').kendoMultiSelect({
			placeholder: "<?php _e( 'Wähle Gruppe', 'cpsmartcrm'); ?>...",
			dataTextField: "name",
			dataValueField: "role",
			autoBind: false,
			dataSource: t_roleSource,
			change: function (e) {
				var t_selectedGroups = (this.value()).clean("");
				jQuery('#t_selectedGroups').val(t_selectedGroups)
			},
			dataBound: function (e) {
				var t_selectedGroups = (this.value()).clean("");
				jQuery('#t_selectedGroups').val(t_selectedGroups)
			}
		});

        t_users.value([<?php echo wp_get_current_user()->ID ?>]);
		function saveTodo(){
			var opener = $('#dialog_todo').data('from');
			if(opener =="clienti")
				id_cliente ='<?php if (isset($ID)) echo $ID?>'
			else if (opener == 'documenti')
				id_cliente = '<?php if (isset($fk_clienti)) echo $fk_clienti?>';
			else if (opener == 'list')
				id_cliente = $('#dialog_todo').data('fkcliente');
			tipo_agenda = '1';
			scadenza_inizio = $("#t_data_scadenza").val();
			scadenza_fine = $("#t_data_scadenza").val();
			scadenzaTimestamp = $("#t_data_scadenza").data('kendoDateTimePicker').value();
			annotazioni = $("#t_annotazioni").val();
			oggetto = $("#t_oggetto").val();
			priorita = $("#priorita").val();
			users = $("#t_selectedUsers").val();
			groups = $("#t_selectedGroups").val();
			days = $("#t_ruleStep").val();
			var s = "[";
			s += '{"ruleStep":"' + days + '" ,"remindToCustomer":';
			if (jQuery('#t_remindToCustomer').prop('checked'))
				s += '"on"';
			else
				s += '""';
			s += ',"selectedUsers":"' + users + '"';
			s += ',"selectedGroups":"' + groups + '"';
			s += ',"userDashboard":';
			if (jQuery('#t_userDashboard').prop('checked'))
				s += '"on"';
			else
				s += '""';
			s += ',"groupDashboard":';
			if (jQuery('#t_groupDashboard').prop('checked'))
				s += '"on"';
			else
				s += '""';
			s += ',"mailToRecipients":';
			if (jQuery('#t_mailToRecipients').prop('checked'))
				s += '"on"';
			else
				s += '""';
			s += '}'
			s += ']';
			var grid = $('#grid').data("kendoGrid");
			$('.modal_loader').show();

			$.ajax({
				url: ajaxurl,
				data: { 'action': 'WPsCRM_save_todo',
					id_cliente: id_cliente,
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
							$("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab("#tab4");
						}, 500);
						setTimeout(function () {
							grid.setDataSource(newDatasource);
							grid.dataSource.read();
						}, 600);

						setTimeout(function () { grid.refresh() }, 700);
					}
					else {
						noty({
							text: "<?php _e('TODO wurde hinzugefügt','cpsmartcrm')?>",
							layout: 'center',
							type: 'success',
							template: '<div class="noty_message"><span class="noty_text"></span></div>',
							//closeWith: ['button'],
							timeout: 1000
						});
					}
					$("#dialog_todo").data('kendoWindow').close();

					$('#new_todo').find(':reset').click();
				}
			})//end ajax

		};


		var t_validator = $("#new_todo").kendoValidator({
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
				hasDays:"<?php _e('Du solltest auswählen, wie viele Tage im Voraus Du die Benachrichtigung aktivieren möchtest','cpsmartcrm')?>",
				hasNoty:"<?php _e('Du solltest einen Benutzer oder eine Gruppe von Benutzern auswählen, die benachrichtigt werden sollen','cpsmartcrm')?>",
				hasClients: "<?php _e('Du solltest einen Kunden auswählen','cpsmartcrm')?>",
				hasObject: "<?php _e('Du solltest einen Betreff für dieses Element eingeben','cpsmartcrm')?>",
				hasExpiration:"<?php _e('Du solltest ein Datum für diese Veranstaltung auswählen','cpsmartcrm')?>"

			}
		}).data("kendoValidator");
		$("#t_saveStep").click(function (e) {
			if (t_validator.validate()) {
				//showMouseLoader();
				//jQuery('#mouse_loader').offset({ left: e.pageX, top: e.pageY });
				saveTodo();
			}
		});
		$('._reset').click(function () {
			$("#dialog_todo").data('kendoWindow').close();
		})
		setTimeout(function () {
			$('.modal_loader').hide()
		},200)
	})
</script>
