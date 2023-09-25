<?php if ( ! defined( 'ABSPATH' ) ) exit;
is_multisite() ? $filter=get_blog_option(get_current_blog_id(), 'active_plugins' ) : $filter=get_option('active_plugins' );
if ( in_array( 'wp-smart-crm-advanced/wp-smart-crm-advanced.php', apply_filters( 'active_plugins', $filter) ) ) {
	$p="dashboard-scheduler.php";
}
else{
	$p="dashboard.php";
}
is_multisite() ? $filter=get_blog_option(get_current_blog_id(), 'active_plugins' ) : $filter=get_option('active_plugins' );
if ( in_array( 'wp-smart-crm-agents/wp-smart-crm-agents.php', apply_filters( 'active_plugins', $filter) ) ) {
    $agent_obj=new AGsCRM_agent();
    $privileges=$agent_obj->getAllPrivileges();
}
else 
    $privileges=null;
//var_dump($privileges);
$document_options=get_option('CRM_documents_settings');

?>
<script>
<?php
if ( in_array( 'wp-smart-crm-agents/wp-smart-crm-agents.php', apply_filters( 'active_plugins', $filter) ) ) {
    $agent_obj=new AGsCRM_agent();
?>
    var privileges = <?php echo json_encode($agent_obj->getAllPrivileges()) ?>;
<?php
} else{?>
    var privileges=null;
<?php } ?>
    </script>
<div class="wrap">
<h1 style="text-align:center">CP Smart CRM & INVOICES<?php if(! isset($_GET['p'])){ ?><?php } ?></h1>
		<?php include("c_menu.php")?>
	<?php
    if(isset($_GET['p']))
		$p=$_GET['p'];

    include(plugin_dir_path(__FILE__ ))."$p";
    echo '<small style="text-align:center;top:30px;position:relative">Developed by Webmaster-Community N3rds@Work <a href="https://smart-cms.n3rds.work">https://smart-cms.n3rds.work</a></small></div>';
	?>

<!--CUSTOM POPUP EDITOR TEMPLATE-->
<script type="text/x-kendo-template" id="customEditor" style="width:960px;height:760px!important">
    #var $format = "<?php echo WPsCRM_DATETIMEFORMAT ?>"#
    #if (typeof disabled != 'undefined' && disabled==1) {
        var readonly="readonly";
        var dataroletxt="data-role=''";
    }
    else {
        var readonly="";
        var dataroletxt="data-role='editor'";
    }#
    <form name="form_insert" style="max-width:940px">
    <section class="eventPopup container-fluid" >
        <div class="row" style="/*padding-left:0*/">
            <label class="col-md-1"><?php _e('Customer','cpsmartcrm') ?></label>
            #var id_cliente;#
            <div data-container-for="customers" class="col-md-3">
                <select id="customers" name="customers"
                        data-bind="value:id_cliente"
                        data-source="customersDatasource"
                        data-text-field="ragione_sociale"
                        data-value-field="ID_clienti"
                        data-role="dropdownlist"
                        data-option-label="Select"
			            #=readonly#	/>
            </div>
<!--        </div>
        <div class="col-md-8" style="padding-left:0">-->
            <label class="col-md-1" for="Title"><?php _e('Subject','cpsmartcrm') ?></label>
            <div data-container-for="title" class="col-md-5" style="padding-left:0">
                <input class="k-textbox col-md-12" data-bind="value:title" name="Title" id="Title" type="text" #=readonly# />
            </div>
        </div>
        #if (tipo_agenda==1 || tipo_agenda==6){#
            <!--<div class="col-md-12" id="_model" style="padding-left:0">-->
                <div class="row">
                <label class="col-md-1" for="Start">#if (tipo_agenda==1){#<?php _e('Date','cpsmartcrm') ?>#}else{#<?php _e('Date','cpsmartcrm') ?>#}#</label>
                <div class="col-md-3" data-container-for="start">
                    <input name="start" required="required" style="z-index: inherit;" type="datetime"
                           data-bind="value:start"
                           data-format="#=$format#"
                           data-role="datetimepicker" 
			               #=readonly# />
                    <!--<input name="start" required="required" type="date" style="z-index: inherit;"
                           data-bind="value:start"
                           data-format="#=$format#"
                           data-role="datepicker" 
						   />-->
                    <span data-bind="text: startTimezone"></span>
                    <span data-for="start" class="k-invalid-msg"></span>
                </div>
                </div>
          <!--  </div>-->
        #} else if (tipo_agenda==2){#
       <!-- <div class="col-md-12" id="_model" style="padding-left:0">-->
                <div class="row">
                <label class="col-md-1" for="Start"><?php _e('Start','cpsmartcrm') ?></label>
                <div class="col-md-3" data-container-for="start">
                    <input name="start" id="dateTimeStart" required="required" style="z-index: inherit;" type="datetime"
                           data-bind="value:start"
                           data-format="#=$format#"
                           data-role="datetimepicker"
                           data-change="onChange" 
                            #=readonly#			   />
                    <!--<input name="start" id="dateStart" required="required" type="date" style="z-index: inherit;"
                           data-bind="value:start,visible:isAllDay"
                           data-format="#=$format#"
                           data-role="datepicker"
                           data-change="onChange" 
						   />-->
                    <span data-bind="text: startTimezone"></span>
                    <span data-for="start" class="k-invalid-msg"></span>
                </div>
                <label class="col-md-1" for="End"><?php _e('End','cpsmartcrm') ?></label>
                <div class="col-md-5" data-container-for="end">
                    <input name="end" id="dateTimeEnd" required="required" style="z-index: inherit;" type="datetime"
                           data-bind="value: end"
                           data-format="#=$format#"
                           data-role="datetimepicker" 
                           #=readonly#			   />

                    <!--<input name="end" id="dateEnd" required="required" type="date" style="z-index: inherit;"
                           data-bind="value:end"
                           data-format="#=$format#"
                           data-role="datepicker" 
						   />-->
                    <span data-bind="text: endTimezone"></span>
                    <span data-for="end" class="k-invalid-msg"></span>
                </div>
            </div>
       <!-- </div>-->
        #}#
       <!-- </div>-->
        <div class="row">
            <label for="description" class="col-md-1"><?php _e('Description','cpsmartcrm') ?></label>
            <div data-container-for="description" class="k-edit-field col-md-11">
                <textarea class="k-textbox" cols="20" data-bind="value:description" #=dataroletxt# id="description" name="description" rows="2" #=readonly#></textarea>
            </div>
        </div>
        <!--Rules-->
        <div class="col-md-12" style="padding-left:0">
        <h4 class="page-header"><?php _e('Notification rules','cpsmartcrm')?></h4>
            <div class="row">
                <label for="rulestep" class="col-md-2"><?php _e('Days in advance','cpsmartcrm') ?></label>
                <div data-container-for="rulestep" class="col-md-2">
                        <select class="form-control _m ruleActions k-dropdown _flat col-md-2" id="ruleStep" name="ruleStep" data-bind="value:rulestep" #=readonly# >
                                <option value=""><?php _e( 'Select', 'cpsmartcrm'); ?></option><?php for($k=0;$k<61;$k++){echo '<option value="'.$k.'">'.$k.'</option>'.PHP_EOL; } ?>
                        </select>
                </div>
                #if (tipo_agenda==2){#
                <label class="col-md-2"><?php _e('Send mail to customer','cpsmartcrm') ?></label>
                <div class="col-md-4"><input type="checkbox" #=readonly?"onclick='return false;'":""# name="remindToCustomer" id="remindToCustomer" data-bind="checked:remind_to_customer" /></div>

                #}#
            </div>
            <div class="row">
                <label class="col-md-4"><?php _e('Send mail to recipients','cpsmartcrm') ?> </label> 
                <div class="col-md-1">
                    <input type="checkbox" #=readonly?"onclick='return false;'":""# name="mailToRecipients" id="mailToRecipients" data-bind="checked:mail_to_recipients" />
                </div>
                <div class="col-md-4" style="line-height:.8em">
                    <div class="row">
                        <label class="col-sm-8 control-label"><?php _e('Send also instant notification','cpsmartcrm')?></label>
                        <div class="col-md-1">
                            <input type="checkbox" #=readonly?"onclick ='return false;' ":" "# class="ruleActions " id="instantNotification" name="instantNotification" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small><?php _e('An email will be sent immediately to all selected users/groups if the option "send mail to recipients" below is active','cpsmartcrm');?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-md-2" for="users" style="float:left"><?php _e('Notify to users','cpsmartcrm') ?></label>
                <div data-container-for="users" class="col-md-3">
                    <select id="users" name="users"
                                    data-bind="value:users"
                                    data-source="agentsDatasource"
                                    data-text-field="display_name"
                                    data-value-field="ID"
                                    data-option-label="Select"
                                    data-role="multiselect" #=readonly#/>
                </div>
                <label class="col-md-2" for="group" style="float:left"><?php _e('Notify to groups','cpsmartcrm') ?></label>
                <div data-container-for="group" class="col-md-3">
                    <select id="group" name="group"
                                    data-bind="value:group"
                                    data-source="groupsDatasource"
                                    data-text-field="name"
                                    data-value-field="role"
                                    data-option-label="Select"
                                    data-role="multiselect" #=readonly#/>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="padding-left:0">
        <h4 class="page-header"><?php _e('Result','cpsmartcrm')?></h4>
        #if (!status) status=1#
        <div class="row" style="padding-left:0">
            <div data-container-for="status" class="k-edit-field">
                <label style="float:left;width:30%"><?php _e('To be done','cpsmartcrm') ?><input type="radio" name="status" value="1" data-bind="checked:status" />
                </label>
                <label style="float:left;width:30%"><?php _e('Done','cpsmartcrm') ?><input type="radio" name="status" value="2" data-bind="checked:status" />
                </label>
                <label style="float:left;width:30%"><?php _e('Canceled','cpsmartcrm') ?><input type="radio" name="status" value="3" data-bind="checked:status" />
                </label>
            </div>
        </div>
        <div class="row" style="padding-left:0">
            <label for="esito" class="col-md-1"><?php _e('Annotations','cpsmartcrm') ?></label>
            <div data-container-for="esito" class="k-edit-field">
                <textarea class="k-textbox" cols="20" data-bind="value:esito" id="esito" name="esito" rows="2"></textarea>
            </div>
        </div>
	    </div>
    </section>
    </form>
</script>
<!--/CUSTOM POPUP EDITOR TEMPLATE-->
<script>
	function onChange(e) {
		var _e = jQuery('#dateTimeEnd').data('kendoDateTimePicker');
		dateEnd = new Date(e.sender._old);             
		dateEnd.setHours(dateEnd.getHours() + 1);
		_e.value(dateEnd)
    }

</script>

<style>
	.k-window>div.k-popup-edit-form {
    padding: 6px 0;
}
	.eventPopup .col-md-12{padding-right:0}
    .k-edit-form-container {
        width: 920px !important;
        height: 768px !important;
        min-height: 768px !important;
    }

    #noty_bottomRight_layout_container li, #noty_topRight_layout_container li{
        border-radius: 0 !important
    }
    .mask {
        position: absolute;
        top: 36px;
        left: 0;
        width: 100%;
        height: calc(100% - 36px);
        background-color: rgba(0,0,0,.16);
    }
    .k-window .k-edit-buttons {
    background: #fafafa;
    }
</style>
<style>
		<?php echo isset($document_options['document_custom_css']) ? $document_options['document_custom_css'] : null?>
</style>