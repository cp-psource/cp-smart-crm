<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$options=get_option('CRM_general_settings');
if(isset($options['services']) && $options['services']==1)
{
    $gateways=get_option('CRM_services_settings');
    if(isset($gateways['gateways']) && $gateways['gateways']=="STRIPE")
		add_filter('single_template', 'WPsCRM_smartcrm_services_template');
    add_filter('archive_template', 'WPsCRM_smartcrm_services_archive_template');
}

function WPsCRM_smartcrm_services_template($single) {
    global $wp_query, $post;

    /* Checks for single template by post type */
    if ($post->post_type == "services"){
        return dirname(__FILE__) . '/templates/single-service-stripe.php';
    }
    return $single;
}
function WPsCRM_smartcrm_services_archive_template( $archive_template ) {
    global $post;

    if ( is_post_type_archive ( 'services' ) ) {
        return dirname(__FILE__) . '/templates/archive-services.php';
    }
    return $archive_template;
}

if(get_option('CRM_user_page') ){
	add_filter('page_template', 'WPsCRM_smartcrm_user_template');
}

function WPsCRM_smartcrm_user_template($page) {
    $CRM_user_page=get_option('CRM_user_page');
    global $wp_query, $post;
    if(is_page($CRM_user_page ) )
        return dirname(__FILE__) . '/templates/CRM_user.php';

    return $page;
}