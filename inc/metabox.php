<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPsCRM_service_Meta_Boxes
{
    function __construct()
    {
        add_action( 'admin_init', array( $this, 'SOFT_register_service_meta_boxes' ) );
    }

    /**
     * Register meta boxes
     */
    function SOFT_register_service_meta_boxes()
    {
        $meta_boxes = array();

        global $post;

        function get_subscriptions(){
            global $wpdb;
            $sql="SELECT ID, name, length from ".WPsCRM_TABLE."subscriptionrules WHERE s_specific=0 order by length ASC";
            $options[]="";
            foreach( $wpdb->get_results( $sql ) as $record)
		    {
                $options[$record->ID]=$record->name . "-- ".$record->length ." ".__('Months','meta-box');

		    }
            return $options;
        }
add_filter( 'CRM_subscriptions', 'get_subscriptions', 99);
        $prefix = 'SOFT_';
        $meta_boxes[] = array(
           'title' => __( 'SETTINGS', 'meta-box' ),

           'pages' => array('services'),
           'context' => 'normal',
           'fields' => array(
            array(
                   'name' => __( 'Service code', 'meta-box' ),
                   'id' => "{$prefix}service_code",
                   'type' => 'text',
               ),
                array(
                   'name' => __( 'Short Description', 'meta-box' ),
                   'id' => "{$prefix}service_short_desc",
                   'type' => 'textarea',
               ),
                 array(
                   'name' => __( 'Important notes', 'meta-box' ),
                   'id' => "{$prefix}service_advice",
                   'type' => 'textarea',
               ),
                  array(
                       'type' => 'divider',
                       'id'   => 'ssfake_divider_id', // Not used, but needed

               ),
                  array(
                        'name'     => __( 'Subscription', 'meta-box' ),
                        'id'       => "{$prefix}service_subscription",
                        'desc'  => 'Associate service to a subscription',
                        'type'     => 'select_advanced',
                        // Array of 'value' => 'Label' pairs for select box
                        'options'  => apply_filters('CRM_subscriptions',array()),
                        'std'=>''
                    ),
                array(
                       'type' => 'divider',
                       'id'   => 'sssfake_divider_id', // Not used, but needed

                   ),
                array(
                   'name' => __( 'Price per...?', 'meta-box' ),
                   'id' => "{$prefix}service_price_per_month",
                   'type' => 'radio',
                   'options'=>array(
                        'month'=>__('Month','meta-box'),
                        'object'=>__('Full Object','meta-box')
                   ),
                   'desc'=>'Wether the price showed in front-end and applied is intended for a month or for the entire object. In case the price is per month, the total cost applied at purchase will be multiplicated for the subscription length'
               ),
                 array(
                   'name' => __( 'Showcase Price', 'meta-box' ),
                   'id' => "{$prefix}service_price",
                   'type' => 'number',
               ),
                  array(
                    'name' => __( 'Price list 1', 'meta-box' ),
                    'id' => "{$prefix}service_price_1",
                    'type' => 'number',
                ),
                  array(
                    'name' => __( 'Price list 2', 'meta-box' ),
                    'id' => "{$prefix}service_price_2",
                    'type' => 'number',
                ),
                  array(
                    'name' => __( 'Price list 3', 'meta-box' ),
                    'id' => "{$prefix}service_price_3",
                    'type' => 'number',
                ),
                  array(
                    'name' => __( 'Price list 4', 'meta-box' ),
                    'id' => "{$prefix}service_price_4",
                    'type' => 'number',
                ),
                  array(
                    'name' => __( 'VAT', 'meta-box' ),
                    'id' => "{$prefix}vat",
                    'type' => 'number',
                ),
                array(
                   'name' => __( 'Photo Gallery', 'meta-box' ),
                   'id' => "{$prefix}service_gallery",
                   'type' => 'image_advanced',
                   'max_file_uploads' => 6,
                   'desc'=>__( '6 Max', 'meta-box' )
                ),

               )
           );

        if ( class_exists( 'RW_Meta_Box' ) )
            foreach ( $meta_boxes as $meta_box )
                new RW_Meta_Box( $meta_box );
    }
}
$TOURmetabox = new WPsCRM_service_Meta_Boxes;