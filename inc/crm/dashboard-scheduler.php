<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$view=$_GET["view"] ? $_GET["view"] : "day";
$update_nonce= wp_create_nonce( "update_activity" );
$delete_nonce= wp_create_nonce( "delete_activity" );
?>


<h4 class="page-header"><?php _e('Quick Menu','cpsmartcrm')?><!--<span class="crmHelp" data-help="quick-menu"></span>--></h4>
<div class="col-md-12" style="border-bottom:8px solid #337ab7;margin-bottom:30px">
<ul class="quick_menu" style="float: left;width: 100%;margin-bottom:0">
    <!--<li onClick="location.href='<?php echo admin_url()?>?page=smart-crm&p=scheduler/form.php&tipo_agenda=1';return false;">
        <i class="glyphicon glyphicon-tag"></i><br /><b ><?php _e('New Todo','cpsmartcrm')?><small></small></b>
    </li>-->
    <li onClick="createScheduler('TODO');return false;">
        <i class="glyphicon glyphicon-tag"></i><br /><b><?php _e('New Todo','cpsmartcrm')?><small></small></b>
    </li>
    <!--<li onClick="location.href='<?php echo admin_url('admin.php?page=smart-crm&p=scheduler/form.php&tipo_agenda=2')?>';return false;">
        <i class="glyphicon glyphicon-pushpin"></i><br /><b ><?php _e('New appointment','cpsmartcrm')?><small></small></b>
    </li>-->
    <li onClick="createScheduler('Appuntamento');return false;">
        <i class="glyphicon glyphicon-pushpin"></i><br /><b><?php _e('New appointment','cpsmartcrm')?><small></small></b>
    </li>
	<li onclick="location.href='<?php echo admin_url('admin.php?page=smart-crm&p=clienti/form.php')?>';return false;">
        <i class="glyphicon glyphicon-user"></i><br /><b ><?php _e('New Customer','cpsmartcrm')?><small></small></b>
    </li>
    <li onClick="location.href='<?php echo admin_url('admin.php?page=smart-crm&p=documenti/form_quotation.php&type=1')?>';return false;">
		<i class="glyphicon glyphicon-circle-arrow-right"></i>
		<br />
		<b>
			<?php _e('New quotation','cpsmartcrm')?>
			<small></small>
		</b>
	</li>
	<li onclick="location.href='<?php echo admin_url('admin.php?page=smart-crm&p=documenti/form_invoice.php&type=2')?>';return false;">
        <i class="glyphicon glyphicon-open-file"></i><br /><b ><?php _e('New Invoice','cpsmartcrm')?><small></small></b>
    </li>
    <?php
	if(current_user_can('manage_options') ){
?>

	<li onclick="location.href='<?php echo admin_url('admin.php?page=smartcrm_settings&tab=CRM_general_settings')?>';return false;">
		<i class="glyphicon glyphicon-cog"></i>
		<br />
		<b>
			<?php _e('Settings','cpsmartcrm')?>
			<small></small>
		</b>
	</li>
	<?php }?>
</ul>
	
</div>
<h3 style="text-align:center">CP Smart CRM SCHEDULER</h3>
<div id="kscheduler"></div>

<div id="schedulerWindow" style="text-align:center"><button onclick='onCopy()' class="btn btn-sm _flat"><i class="glyphicon glyphicon-copy"></i> <?php _e('Copy Event','cpsmartcrm')?></button> <?php _e('OR','cpsmartcrm')?> <button class="btn btn-sm _flat" onclick='onEdit()'><i class="glyphicon glyphicon-pencil"></i> <?php _e('Edit Event','cpsmartcrm')?></button></div>
<style>
    .k-scheduler-toolbar{position:initial}
</style>
<script>

	var $format = "<?php echo WPsCRM_DATEFORMAT ?>";
	var $formatDateTime = "<?php echo WPsCRM_DATETIMEFORMAT ?>";
	var $formatTime = "<?php echo WPsCRM_TIMEFORMAT ?>";

	var time, eventHolder;

	function createScheduler(type) {
		var typeID;
		if (type == "TODO")
			typeID = 1
		if (type == "Appuntamento")
			typeID = 2;
		var scheduler = jQuery("#kscheduler").data("kendoScheduler");
		scheduler.addEvent({
			title: "<?php _e('New','cpsmartcrm')?> " + type ,
			tipo_agenda: typeID,
			esito:"",

		});
	}

	//recurring events are not handled
	function onCopy() {
		var dialog = jQuery("#schedulerWindow").data("kendoWindow");
		var scheduler = jQuery("#kscheduler").data("kendoScheduler");
		dialog.close();

		var copy = eventHolder.event.toJSON();
		copy.start = eventHolder.start;
		copy.end = eventHolder.end;
		copy.id_agenda = 0;
		delete copy.uid;

		scheduler.dataSource.add(copy);
		scheduler.dataSource.sync();
		eventHolder = null;
	}

	function onEdit() {
		var dialog = jQuery("#schedulerWindow").data("kendoWindow");
		var scheduler = jQuery("#kscheduler").data("kendoScheduler");
		dialog.close();

		eventHolder.event.set("start", eventHolder.start);
		eventHolder.event.set("end", eventHolder.end);

		scheduler.dataSource.sync();
		eventHolder = null;
	}
	var agentsDatasource = new kendo.data.DataSource({
		transport: {
			read: function (options) {
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'WPsCRM_get_CRM_users'
					},
					success: function (result) {
						console.log(result);
						options.success(result);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			}
		}
	});
	var groupsDatasource = new kendo.data.DataSource({
		transport: {
			read: function (options) {
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'WPsCRM_get_registered_roles'
					},
					success: function (result) {
						console.log(result);
						options.success(result.roles);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			}
		}
	});
	var customersDatasource = new kendo.data.DataSource({
		transport: {
			read: function (options) {
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'WPsCRM_get_clients2'
					},
					success: function (result) {
						console.log(result);
						options.success(result.clients);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			}
		}
	});
jQuery(document).ready(function ($) {
	var schedulerDatasource= new kendo.data.SchedulerDataSource({
		batch: true,
		//autoSync: true,
		transport: {
			read: function (options) {
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'ADVsCRM_get_scheduler_kendo',
						self_client:'1'
					},
					success: function (result) {
						console.log(result);
						options.success(result);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			},
			update: function (options){
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'ADVsCRM_update_scheduler_kendo',
						security:'<?php echo $update_nonce ?>',
						values: options.data.models

					},
					success: function (result) {
						console.log(result);
						//options.success(result);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			},
			create: function (options){
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'ADVsCRM_update_scheduler_kendo',
						security:'<?php echo $update_nonce ?>',
						values: options.data.models

					},
					success: function (result) {
						console.log(result);
						//options.success(result);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})
			},
			destroy: function (options){
				jQuery.ajax({
					url: ajaxurl,
					data: {
						action: 'ADVsCRM_delete_scheduler_kendo',
						security:'<?php echo $update_nonce ?>',
						values: options.data.models

					},
					success: function (result) {
						console.log(result);
						//options.success(result);
					},
					error: function (errorThrown) {
						console.log(errorThrown);
					}
				})

			},
			parameterMap: function (options, operation) {
				//console.log(options);
				if (operation !== "read" && options.models) {
					return { models: kendo.stringify(options.models) };
				}
			}
		},
		change: function (data) {
			//console.log(data);
		},
		schema: {
			model: {
				id: "id_agenda",
				//title:"oggetto",
				fields: {
					id_agenda: { from: "id_agenda", type: "number" },
					title: { from: "oggetto" },
					start: { type: "date", from: "data_inizio" },
					end: { type: "date", from: "data_fine" },
					description: { from: "annotazioni" },
					users: { from: "users" },
					group: { from: "group" },
					status:{from: "status"},
					rulestep:{from: "rulestep"},
					remind_to_customer:{from: "remind_to_customer"},
					user_dashboard:{from: "user_dashboard"},
					group_dashboard:{from: "group_dashboard"},
					mail_to_recipients:{from: "mail_to_recipients"}
				}
			}
		},
		<?php if (isset($_GET['event']) ) {?>
		filter: {
				filters: [
					{ field: "id_agenda", operator: "eq", value: <?php echo $_GET['event'] ?> }
				]
			}

		<?php } ?>

	});

	$("#schedulerWindow").kendoWindow({
		visible: false,
		width:440
	});
	$("#kscheduler").kendoScheduler({
		height: 800,
		snap: true,
		editable: {
			template: $("#customEditor").html(),
			confirmation: "<?php _e('Are you sure you want to delete this event?','cpsmartcrm') ?>"
		},
		selectable:true,
		dataBound: function (e) {
			$('.k-more-events span').text('<?php _e('More events','cpsmartcrm') ?>...');

			console.log("databound", this.dataSource.filter() );
			var events = this.dataSource.view();
			console.log("view", events );
			<?php if (isset($_GET['event']) ) {?>
		if ( this.dataSource.filter().filters.length && this.dataSource.filter().filters[0].value == <?php echo $_GET['event'] ?> ) {
			var event = events[0];
			console.log(event.start);
			this.date(event.start);
			this.select([event.uid]);
			}
		<?php  } ?>
		},
		navigate: scheduler_navigate,
		toolbar:["pdf"],
		views: [
				{ type: "day", eventTemplate: $("#event-day-template").html() },
				{ type: "week", eventTemplate: $("#event-week-template").html() },
				{ type: "month", eventTemplate: $("#event-month-template").html()<?php if ( !isset($_GET['event']) ) {?> , selected: "true" <?php  } ?> },
				{ type: "agenda", eventTemplate: $("#event-agenda-template").html() <?php if (isset($_GET['event']) ) {?> , selected: "true" <?php  } ?>}
		],
		moveEnd: function (e) {
			e.preventDefault();
			eventHolder = e;
			var dialog = $("#schedulerWindow").data("kendoWindow");
			dialog.center();
			dialog.open();
			console.log(eventHolder);

		},

		timezone: "Etc/UTC",
		dataSource: schedulerDatasource,
		/*resources: [
			{
				field: "users",
				title: "Agent",
				dataValueField:'ID',
				dataTextField: "display_name",
				dataSource: agentsDatasource,
				multiple: true
			},
			{
				field: "group",
				title: "Group",
				dataValueField: 'role',
				dataTextField: "name",
				dataSource: groupsDatasource,
				multiple: true
			}
		],*/
		edit: function (e) {
			var container = e.container;
			var popupEditor = $(container).data("kendoWindow");
			popupEditor.setOptions({
				title: '<?php _e('Edit','cpsmartcrm') ?> ' + e.event.title,
				width: 940,
				height: 700
			})
			$('.k-window-titlebar').addClass('title-'+ e.event.tipo_agenda )


			/* ACTION: ADD custom button */
			var newButton = $('<a class="k-button" href="#">New button</a>');

			//wire its click event
			newButton.click(function (e) { alert("Clicked"); });

			//add the button to the container
			//var buttonsContainer = container.find(".k-edit-buttons");
			//buttonsContainer.append(newButton);

			var radioButton = $('input[type="radio"]');
			$(radioButton).each(function () {
				$(this).wrap("<span class='custom-radio'></span>");
				if ($(this).is(':checked')) {
					$(this).parent().addClass("selected");
				}
			});
			$(radioButton).click(function () {
				if ($(this).is(':checked')) {
					$(this).parent().addClass("selected");
				}
				$(radioButton).not(this).each(function () {
					$(this).parent().removeClass("selected");
				});
			});
		}
	});

	/**
	**define tooltip only navigating to monthly view
	**/
	function scheduler_navigate(e) {
		console.log(this.dataSource.filter().filters.length)
		if(this.dataSource.filter().filters.length)
			this.dataSource.filter([]);
		if (e.view == "month" || e.view == "week") {
			$("#kscheduler").kendoTooltip({
				filter: ".k-event:not(.k-event-drag-hint) > div",
				position: "bottom",
				width: 500,
				content: kendo.template($('#tooltip-template').html()),
				showAfter: 800,
			});
		}
		else
			if ($("#kscheduler").data("kendoTooltip") !=undefined)
				$("#kscheduler").data("kendoTooltip").destroy();
	}

	/**
	**define tooltip at page load if view=month
	**/
	var scheduler = $("#kscheduler").data("kendoScheduler");
	if (scheduler._selectedViewName == "month" || scheduler._selectedViewName == "week") {
		$("#kscheduler").kendoTooltip({
			filter: ".k-event:not(.k-event-drag-hint) > div",
			position: "bottom",
			width: 500,
			content: kendo.template($('#tooltip-template').html()),
			showAfter: 800,
		});
	}
	else
		if ($("#kscheduler").data("kendoTooltip") !=undefined)
			$("#kscheduler").data("kendoTooltip").destroy();

	$('#kscheduler').on('click', '.edit-event', function () {
		var scheduler = $("#kscheduler").data("kendoScheduler");
		console.log($(this).data('uid'));
		scheduler.editEvent($(this).data('uid'));
		})

	})
</script>



<!--DAILY VIEW EVENT TEMPLATE-->
<script id="event-day-template" type="text/x-kendo-template">
	#var des_tipo_agenda#
    <div class="activity-template activity-day-template  #if(des_tipo_agenda=='TODO'){# todo-event #}else{# appointment-event #}#">
        #var icon="";#
        #status==2 ? icon='<i class="glyphicon glyphicon-ok" style="color: green;font-size:1.2em"></i>' : icon='<i class="glyphicon glyphicon-bookmark" style="color: black;font-size:1.2em"></i>'#
        #esito != null ? icon +="<small>(" + esito +")</small>" : null#
        <h4 style="text-align:center;text-transform:uppercase">#=title#</h4>
        <div class="col-md-6">
            <span><strong><?php _e('Start','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(start), $formatDateTime)#</span>
            <span><strong><?php _e('End','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(end), $formatDateTime)#</span>
            <span class="event_description"><strong><?php _e('Description','cpsmartcrm') ?>:</strong> #=description#</span>
            <span><strong><?php _e('Status','cpsmartcrm') ?>:</strong> #=icon#</span>
        </div>
        <div class="col-md-4 pull-right">
            <span style="line-height:40px;height:44px;padding-bottom:3px;border-bottom:1px solid \#ccc;margin-bottom:3px;"><strong><?php _e('Customer','cpsmartcrm') ?>: </strong><a href="<?php  echo admin_url('admin.php?page=smart-crm&p=clienti%2Fform.php')?>&ID=#=id_cliente#" style="text-decoration:underline">#=cliente# <img src="#=img_cliente#" style="width:40px;height:40px;border-radius:50%;float:right;cursor:pointer;" /></a></span><br />
            <span style="line-height:40px;height:44px;padding-bottom:3px;"><strong><?php _e('Assigned to','cpsmartcrm') ?>:</strong> #=nome_agente# <img src="#=img_agente#" style="width:40px;height:40px;border-radius:50%;float:right" /></span>
        </div>
    </div>
</script>
<!--/DAILY VIEW EVENT TEMPLATE-->

<!--WEEKLY VIEW EVENT TEMPLATE-->
<script id="event-week-template" type="text/x-kendo-template">
    #var des_tipo_agenda#
    <div class="activity-template activity-day-template  #if(des_tipo_agenda=='TODO'){# todo-event #}else{# appointment-event #}#">
        <p>
            #: title # ~ #: kendo.toString(start, $formatTime) #
        </p>
    </div>
</script>
<!--/WEEKLY VIEW EVENT TEMPLATE-->

<!--AGENDA VIEW EVENT TEMPLATE-->
<script id="event-agenda-template" type="text/x-kendo-template">
	#var eventClass,iconEvent,iconStatus,des_tipo_agenda;#
    #if(des_tipo_agenda=='TODO'){ eventClass="todo-event";iconEvent='<i class="glyphicon glyphicon-tag"></i>'};if (des_tipo_agenda=='Appuntamento'){ eventClass="appointment-event";iconEvent='<i class="glyphicon glyphicon-pushpin"></i>'}#
    <div class="activity-template activity-agenda-template  #=eventClass#">

        #if(status==2) iconStatus='<i class="glyphicon glyphicon-ok" style="color: green;font-size:1.2em" title="<?php _e('Done','cpsmartcrm') ?>"></i>'; if(status==1) iconStatus='<i class="glyphicon glyphicon-bookmark" style="color: black;font-size:1.2em" title="<?php _e('To be done','cpsmartcrm') ?>"></i>'#
        #esito != null ? iconStatus +="<small>(" + esito +")</small>" : null#
        <h4 style="text-align:center;text-transform:uppercase;margin-bottom:24px"><mark style="float:left;font-size:1.3em" class="mark-#=des_tipo_agenda#">#=iconEvent#</mark><span class="btn btn-sm btn-info edit-event" data-uid="#=uid#" style="float:left;display:inline-block;width:120px;margin-left:10px"><?php _e('Edit','cpsmartcrm') ?></span>#=title#</h4>
        <div class="col-md-6">
            <span><strong><?php _e('Start','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(start), $formatDateTime)#</span>
            <span><strong><?php _e('End','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(end), $formatDateTime)#</span>
            <span class="event_description"><strong><?php _e('Description','cpsmartcrm') ?>:</strong> #=description#</span>
            <span><strong><?php _e('Status','cpsmartcrm') ?>:</strong> #=iconStatus#</span>
            <span class="btn btn-sm btn-info quick-edit-event" data-id="#=id_agenda#" data-status="#=status#" data-esito="#=esito#" style="float:left;display:inline-block;width:120px;margin-left:10px"><?php _e('Quick Edit','cpsmartcrm') ?></span>
        </div>
        <div class="col-md-4 pull-right">
            <span style="line-height:40px;height:44px;padding-bottom:3px;border-bottom:1px solid \#ccc;margin-bottom:3px;"><strong><?php _e('Customer','cpsmartcrm') ?>: </strong><a href="<?php  echo admin_url('admin.php?page=smart-crm&p=clienti%2Fform.php')?>&ID=#=id_cliente#" style="text-decoration:underline">#=cliente# <img src="#=img_cliente#" style="width:40px;height:40px;border-radius:50%;float:right;cursor:pointer;" /></a></span><br />
            <span style="line-height:40px;height:44px;padding-bottom:3px;"><strong><?php _e('Assigned to','cpsmartcrm') ?>:</strong> #=nome_agente# <img src="#=img_agente#" style="width:40px;height:40px;border-radius:50%;float:right" /></span>
        </div>
    </div>
</script>
<!-- /AGENDA VIEW EVENT TEMPLATE-->

<!-- /MONTHLY VIEW EVENT TEMPLATE-->
<script id="event-month-template" type="text/x-kendo-template">
    #var des_tipo_agenda#
    <div class="activity-template activity-month-template  #if(des_tipo_agenda=='TODO'){# todo-event #}else{# appointment-event #}#">
        <p>
            #=title # ~ #: kendo.toString(start, $formatTime) #
        </p>
    </div>
</script>
<!-- /AGENDA VIEW EVENT TEMPLATE-->

<!-- TOOLTIP EVENT TEMPLATE-->
<script id="tooltip-template" type="text/x-kendo-template">
    <div class="scheduler_tooltip" style="text-align:left">
        #var element = target.is(".k-task") ? target : target.parent();#
        #var uid = element.attr("data-uid");#
        #var scheduler = target.closest("[data-role=scheduler]").data("kendoScheduler");#
        #var model = scheduler.occurrenceByUid(uid);#
        #var icon="",view=scheduler._selectedViewName#
        #model.status==2 ? icon='<i class="glyphicon glyphicon-ok" style="color: green;font-size:1.2em"></i>' : icon='<i class="glyphicon glyphicon-bookmark" style="color: black;font-size:1.2em"></i>'#
        #model.esito != null ? icon +="<small>(" + model.esito +")</small>" : null#
        #if(model) {#
			<h4 style="text-align:center;text-transform:uppercase">#=model.title#</h4>
			<span><strong><?php _e('Start','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(model.start), $formatDateTime)#</span>
			<span><strong><?php _e('End','cpsmartcrm') ?>:</strong> #=kendo.toString(kendo.parseDate(model.end), $formatDateTime)#</span>
			<span class="event_description"><strong><?php _e('Description','cpsmartcrm') ?>:</strong> #=model.description#</span>
			<span><strong><?php _e('Status','cpsmartcrm') ?>:</strong> #=icon#</span>
			<span style="line-height:40px;height:44px;padding-bottom:3px;border-bottom:1px solid \#ccc;margin-bottom:3px;"><strong><?php _e('Customer','cpsmartcrm') ?>: </strong><a href="<?php  echo admin_url('admin.php?page=smart-crm&p=clienti%2Fform.php')?>&ID=#=model.id_cliente#" style="text-decoration:underline">#=model.cliente# <img src="#=model.img_cliente#" style="width:40px;height:40px;border-radius:50%;float:right;cursor:pointer;" /></a></span><br />
			<span style="line-height:40px;height:44px;padding-bottom:3px;"><strong><?php _e('Assigned to','cpsmartcrm') ?>:</strong> #=model.nome_agente# <img src="#=model.img_agente#" style="width:40px;height:40px;border-radius:50%;float:right" /></span>
		#} else {#
		<strong><?php _e('No event data is available','cpsmartcrm') ?></strong>
		#}#
</div>
</script>
<!-- /TOOLTIP EVENT TEMPLATE-->

<style scoped>
	mark{background:none;background-color:#ccc;border-radius:50%;margin-left:4px}

	mark .glyphicon{margin:3px 4px}
	.scheduler_tooltip span{line-height:30px;height:30px;float:left;width:100%}
	.scheduler_tooltip img{border:1px solid #ccc;opacity:.8}
	.scheduler_tooltip a:hover{text-decoration:none!important}
	.scheduler_tooltip a img:hover {border:1px solid #ff7800;opacity:1}
	.event_description{line-height:1em!important;font-size:small;height:auto!important}
	.activity-agenda-template span{line-height:20px;height:20px;float:left;width:100%}
	.activity-agenda-template img{border:1px solid #ccc;opacity:.8}
	.activity-agenda-template a:hover{text-decoration:none!important}
	.activity-agenda-template a img:hover {border:1px solid #ff7800;opacity:1}
	.activity-day-template span{line-height:20px;height:20px;float:left;width:100%}
	.activity-day-template img{border:1px solid #ccc;opacity:.8}
	.activity-day-template a:hover{text-decoration:none!important}
	.activity-day-template a img:hover {border:1px solid #ff7800;opacity:1}
	.k-scheduler-edit-form .k-edit-form-container, .k-scheduler-timezones .k-edit-form-container {
    width: 920px;
}
	._ruleRow{padding-bottom:14px}
    ._ruleRow input[type="checkbox"] {
    margin-top:12px;margin-right:4px
	}
	.k-scheduler-edit-form .k-edit-label {
    width: 13%;
}
	.k-scheduler-edit-form .k-edit-field {
    width: 80%;
}
	.title-TODO{background-color:lightsteelblue!important}
	.title-Appuntamento{background-color:lightsalmon!important}
    .k-event {
        background: none;
        border: none;
    }

    .activity-template {
		float:left;width:100%
    }
	.activity-agenda-template{}
    .k-tooltip-closable .k-tooltip-content {
        margin: 8px;
        text-align: left;
    }

    .todo-event {
        background-color: lightsteelblue;
        color: black;
    }

    .appointment-event {
        background-color: lightsalmon;
        color: #393939;
    }

    .activity-template img {
        float: left;
        margin: 0 8px;
    }

    .activity-template p {
        /*margin: 5px 0 0;*/
    }

    .activity-template h3 {
        padding: 2px;
        font-size: 12px;
    }

    .activity-template a {
        color: #ffffff;
        font-weight: bold;
        text-decoration: none;
    }

    .k-state-hover .movie-template a,
    .activity-template a:hover {
        color: #000000;
    }

    .k-scheduler-dayview .k-scheduler-table td {
		height: 140px !important;
    }
    .k-scheduler-weekview tr:nth-child(2) .k-scheduler-table td {
		height: 100px !important;
    }
    .k-scheduler-monthview .k-scheduler-table td,.k-scheduler-monthview .k-hidden {
        height: 140px !important;
        }
	.custom-radio{
		width: 16px;
		height: 16px;
		display: inline-block;
		position: relative;
		z-index: 1;
		top: 4px;
		left:8px;
		background: url("<?php echo ADVsCRM_URL?>inc/img/radio.png") no-repeat!important;
	}
	.custom-radio:hover{            
		background: url("<?php echo ADVsCRM_URL?>inc/img/radio-hover.png") no-repeat!important;
	}
	.custom-radio.selected{
		background: url("<?php echo ADVsCRM_URL?>inc/img/radio-selected.png") no-repeat!important;
	}
	.custom-radio input[type="radio"]{
		margin: 1px;
		position: absolute;
		z-index: 2;            
		cursor: pointer;
		outline: none;
		opacity: 0;
		/* CSS hacks for older browsers */
		_noFocusLine: expression(this.hideFocus=true); 
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		filter: alpha(opacity=0);
		-khtml-opacity: 0;
		-moz-opacity: 0;
	}
</style>
<div id="dialog-edit" style="/*display:none;*/">
	<?php
		do_action('WPsCRM_form_quick_edit');
    ?>
</div>
<?php
		do_action('WPsCRM_script_quick_edit');
?>
