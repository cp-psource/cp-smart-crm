<?php
if ( ! defined( 'ABSPATH' ) ) exit;
  $tipo_agenda=2;
  $giorno=date("d");
  $mese=date("m");
  $anno=date("Y");
  $ora_i=date("H");
  $minuto_i=date("i");
  $ora_f=date("H");
  $minuto_f=date("i");
  $oggi=date("d-m-Y");
  list($giorno,$mese,$anno) = explode("-",$oggi);
  $data_scadenza=date("d-m-Y");
  $data_agenda=date("d-m-Y");

//echo $data_agenda;
?>

<form id="new_appointment" class="modal_form">
    <div class="col-md-12 panel panel-primary _flat" style="padding:0!important">
        <div class="panel-body" style="padding:20px">
            <div class="row" style="padding-bottom:2px;padding-top:0px;border-bottom:1px solid #ccc">

                    <label class="col-sm-1 control-label"><?php _e( 'Start', 'cpsmartcrm'); ?></label>
                    <div class="col-md-3">
                        <input name="a_data_scadenza_inizio" id='a_data_scadenza_inizio' value="<?php echo $data_scadenza?>">
                    </div>
                    <label class="col-sm-1 control-label"><?php _e( 'End', 'cpsmartcrm'); ?></label>
                    <div class="col-md-3">
                        <input name="a_data_scadenza_fine" id='a_data_scadenza_fine' value="<?php echo $data_scadenza?>">
                    </div>
				<div class="clear"></div>
                    <label class="col-sm-1 control-label"><?php _e('Priority','cpsmartcrm')?></label>
                    <div class="col-sm-3">
                        <?php WPsCRM_priorita()?>
                    </div>
            </div>
            <div class="row" style="padding-bottom:2px;padding-top:0px;">
                <div class="col-md-5 form-group">
                    <label><?php _e( 'Object', 'cpsmartcrm'); ?> </label><input type="text" name="a_oggetto" id='a_oggetto' class="form-control _m k-textbox" placeholder="<?php _e('Type an object for this Item','cpsmartcrm')?>">
                </div>
                <div class="col-md-6 form-group">
                    <label><?php _e( 'Notes', 'cpsmartcrm'); ?></label><textarea id="a_annotazioni" name="a_annotazioni" class="form-control _m k-textbox _flat" style="height:30px"></textarea>

                </div>

            </div>

            <div class="row" style="background:#e2e2e2;padding-bottom:10px;margin-bottom:10px">
                <div class="col-md-11"><h3><?php _e( 'Notifications rules for this Appointment', 'cpsmartcrm'); ?> </h3></div>

            </div>

            <div style="padding-bottom:2px;padding-top:0px;border-bottom:1px solid #ccc">
                <div class="col-md-4 form-group">
                    <label><?php _e( 'Days in advance', 'cpsmartcrm'); ?></label>
                    <select class="form-control _m ruleActions k-dropdown _flat" style="width:100px" id="a_ruleStep" name="a_ruleStep">
                        <option value=""><?php _e( 'Select', 'cpsmartcrm'); ?></option>
                        <?php for($k=0;$k<61;$k++){echo '<option value="'.$k.'">'.$k.'</option>'.PHP_EOL; } ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <label><?php _e('Send also instant notification','cpsmartcrm')?></label>
                    <input type="checkbox" class="ruleActions " id="instantNotification" name="instantNotification" />
                    <small style="line-height:.8em">An email will be sent immediately to all selected users/groups if the option "send mail to recipients" below is active</small>
                </div>

            </div>
			<div class="clear"></div>
            <div class="row" style="padding-bottom:2px;padding-top:0px;border-bottom:1px solid #ccc">
                <div class="col-md-6">
                    <label><?php _e( 'Select Account for this appointment', 'cpsmartcrm'); ?></label>
                    <input class="ruleActions" id="a_remindToUser" name="a_remindToUser" />
                </div>
                <div class="col-md-4">
                    <label>
                        <?php _e( 'Publish on Account dashboard', 'cpsmartcrm'); ?>?<br />
                        <input type="checkbox" class="ruleActions" name="a_userDashboard" id="a_userDashboard" />
                    </label>
                </div>
            </div>
            <div class="row" style="padding-bottom:2px;padding-top:0px;border-bottom:1px solid #ccc;display:none">
                <div class="col-md-6">
                    <label><?php _e( 'Send to Group', 'cpsmartcrm'); ?></label>
                    <input class="ruleActions" id="a_remindToGroup" name="a_remindToGroup">
                </div>
                <div class="col-md-4">
                    <label>
                        <?php _e( 'Publish on group dashboard', 'cpsmartcrm'); ?>?<br />
                        <input type="checkbox" class="ruleActions" name="a_groupDashboard" id="a_groupDashboard" />
                    </label>
                </div>
            </div>
            <div class="row" style="background:#f7f2d9;padding-bottom:4px">
                    <div class="col-md-6">
                        <label>
                            <?php _e( 'Send mail to customer', 'cpsmartcrm'); ?><br />
                            <input type="checkbox" class="ruleActions col-sm-2 alignright" id="a_remindToCustomer" name="a_remindToCustomer" />
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label>
                            <?php _e( 'Send mail to selected Accounts', 'cpsmartcrm'); ?><br />
                            <input type="checkbox" class="ruleActions" id="a_mailToRecipients" name="a_mailToRecipients" />
                        </label>
                    </div>

            </div>
            <div class="row" style="padding:16px">
                <span class="btn btn-success _flat" id="a_saveStep"><?php _e( 'Save', 'cpsmartcrm'); ?></span>
                <span class="btn btn-warning _flat _reset" id="a_configreset"><?php _e( 'Reset', 'cpsmartcrm'); ?></span>
            </div>
        </div>
        
    </div>
    <input type="hidden" id="a_selectedUsers" name="a_selectedUsers"  class="ruleActions"value=""/>
    <input type="hidden" id="a_selectedGroups" name="a_selectedGroups"  class="ruleActions"value=""/>
    <input type="submit"  id="submit_a_form" style="display:none"/>
    <input type="reset"  id="reset_a_form" style="display:none"/>
</form>
