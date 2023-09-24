<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$customer=$_GET['customer'];
$token=$_POST['stripeToken'];

if($form="new")
    {
        $email=$_POST['stripeEmail'];
        $name=$_POST['stripeBillingName'];
        $address=$_POST['stripeBillingAddressLine1'];
        $town=$_POST['stripeBillingAddressCity'];
        $name=explode(" ",$name);
        $first_name=$name[0];
        $k=array_shift($name);
        $last_name=implode(' ', $name);
?>
    <form id="CRM_newCustomer">
        <h2><?php _e('Confirm your data','cpsmartcrm')?></h2>
        <label><?php _e('First Name','cpsmartcrm')?></label><input type="text" id="CRM_firstname" name="CRM_firstname" value="<?php echo $first_name ?>" required /><br />
        <label><?php _e('Last Name','cpsmartcrm')?></label><input type="text" id="CRM_lastname" name="CRM_lastname" value="<?php echo $last_name ?>" required/><br />
        <label><?php _e('Email','cpsmartcrm')?></label><input type="email" id="CRM_email" name="CRM_email" value="<?php echo $email ?>" required /><br />
        <label><?php _e('Address','cpsmartcrm')?></label><input type="text" name="CRM_address" value="<?php echo $address ?>"/><br />
        <label><?php _e('Town','cpsmartcrm')?></label><input type="text" name="CRM_town" value="<?php echo $town ?>"/><br />
        <label><?php _e('Customer Type','cpsmartcrm')?></label><label><?php _e('Private','cpsmartcrm')?></label><input type="radio" name="CRM_client_type" value="privato" />
        <label><?php _e('Business','cpsmartcrm')?></label><input type="radio" name="CRM_client_type" value="azienda"/><br />
        <label>C.F.</label><input type="text" id="CRM_client_CF" name="CRM_client_CF" /><br />
        <label><?php _e('VAT code','cpsmartcrm')?></label><input type="text" id="CRM_client_IVA" name="CRM_client_IVA" /><br />
        <hr />
        <label><?php _e('Choose a username','cpsmartcrm')?></label><input name="CRM_username" type="text" />
        <label><?php _e('Choose a Password','cpsmartcrm')?></label><input type="password" name="CRM_password" id="CRM_password" />
        <button type="submit"><?php _e('Confirm','cpsmartcrm')?></button>
    </form>
<script>
    jQuery(document).ready(function ($) {

        $("#CRM_password").strength({
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter',
            strengthButtonClass: 'button_strength',
            strengthButtonText: 'Show password',
            strengthButtonTextToggle: 'Hide Password'
        });

    });
</script>
    <?php
    }
else{
    
}