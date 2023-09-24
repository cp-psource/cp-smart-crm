<?php
if ( ! defined( 'ABSPATH' ) ) exit;
  $oggi=date("d-m-Y");
?>
<form id="new_activity" class="modal_form">
	<div class="col-md-12 panel panel-primary _flat" style="padding:0!important">
		<div class="panel-body" style="padding:20px">
			<div class="row" style="padding-bottom:10px;padding-top:6px;border-bottom:1px solid #ccc">
				<div class="col-md-12 form-group">
					<label>
						<?php _e( 'Date', 'cpsmartcrm' ); ?>
					</label>
					<input type="text" name="data_attivita" id='data_attivita' maxlength='10' size="10" value="<?php echo $oggi?>" class="_date" />
				</div>
			</div>
			<div class="row" style="padding-bottom:10px;padding-top:6px; solid #ccc">
				<div class="col-md-11 form-group">
					<label>
						<?php _e( 'Notes', 'cpsmartcrm' ); ?>
					</label>
					<textarea id="n_annotazioni" name="n_annotazioni" class="form-control _m"></textarea>
				</div>
			</div>
			<div class="row" style="padding:30px">
				<span class="btn btn-success _flat" id="saveActivity">
					<?php _e( 'Save', 'cpsmartcrm' ); ?>
				</span>
				<span class="btn btn-warning _flat _reset" id="configreset">
					<?php _e( 'Reset', 'cpsmartcrm' ); ?>
				</span>
			</div>
		</div>
		
	</div>
</form>