<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once($plugin_dir.'/inc/gateways/vendor/autoload.php');
$stripeOptions=get_option('CRM_services_settings');
$mode=$stripeOptions['stripe_mode'];

if($mode=="test_mode"){//SANDBOX
    $secret=$stripeOptions['stripe_test_secret_key'];
    $public=$stripeOptions['stripe_test_publishable_key'];
}
elseif($mode=="live_mode"){
    $secret=$stripeOptions['stripe_live_secret_key'];
    $public=$stripeOptions['stripe_live_publishable_key'];
}

$stripe = array(
  "secret_key"      => $secret,
  "publishable_key" => $public
);

//SANDBOX
//$stripe = array(
//  "secret_key"      => "sk_test_OceaNHEcjaJ5fOzcIdWsJZkT",
//  "publishable_key" => "pk_test_6WhealBsxcpckGzZrhd9pKDA"
//);

//LIVE
//$stripe = array(
//  "secret_key"      => "sk_live_7Du0F9A5oVcwrjfyEqGDFlR8",
//  "publishable_key" => "pk_live_dGivRB5PNr7UxUYlXMTmezFS"
//);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>