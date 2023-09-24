<?php
$plugin_dir  = dirname(dirname(dirname(__FILE__)));

global $wpdb;
$options=get_option('CRM_services_settings');
get_header();
echo '<h1 class="services_title">'.__('Services','cpsmartcrm').'</h1>';
do_action('wpCRM_before_loop');
 while ( have_posts() ) : the_post(); 
  $discount="";
 $code=get_post_meta($post->ID,'SOFT_service_code',true);
 $price_type=get_post_meta($post->ID,'SOFT_service_price_per_month',true);
 $full_price=get_post_meta($post->ID,'SOFT_service_price',true);
 $list_price_1=get_post_meta($post->ID,'SOFT_service_price_1',true);
 $short_desc=get_post_meta($post->ID,'SOFT_service_short_desc',true);
 $service_advice=get_post_meta($post->ID,'SOFT_service_advice',true);
 $subscription=get_post_meta($post->ID,'SOFT_service_subscription',true);
 if($full_price!=""){
     $discount='<span class="badge badge-discount"></span> ';
     $showed_price='<span class="CRM_full_price">'.$full_price.' '.$options['currency'].'</span><span class="CRM_sale_price">'.$list_price_1.' '.$options['currency'].'</span>';
 }
 else{
     $showed_price='<span class="CRM_sale_price">'.$list_price_1.' '.$options['currency'].'</span>';
 }
 $price_type=="month" ? $month= __("per month",'cpsmartcrm')." " : $month="" ;
 ?>
<div class="_smartcrm_service col-md-12 col-sd-12" style="min-height:180px;width:98%;float:left;border:2px solid #ccc;margin-bottom:30px">
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    
        <?php if ( has_post_thumbnail() ) {?> 
        <div class="col-md-4 col-sd-12" style="float:left">
            <figure style="padding:4px;margin-left:20px">
                <?php
                   echo get_the_post_thumbnail($post->ID, 'medium', array('class' => 'alignleft'));
                   echo $discount;
                   ?>
            </figure>
        </div>
        <?php } ?>
    <div class="col-md-8 col-sd-12 alignright CRM_service_features" style="width:96%;border:none">
        <div class="CRM_main_show_price" style="background:#ccc;color:#000!important">
            <span><?php _e('PRICE','cpsmartcrm') ?>: <?php echo $showed_price." ".$month?></span> 
        </div>
            <h5>
            <?php _e('Code','cpsmartcrm') ;?>: <strong><?php echo $code ?></strong>
            </h5>
            <?php if (count($terms) > 0) {?>
            <h5 class="services_category">
            
            <?php _e('CATEGORIES','cpsmartcrm') ?>: 
                <small>
                <?php 
                      foreach ( $terms as $term ) {
                          $term_link = get_term_link( $term );
                          if ( is_wp_error( $term_link ) ) {
                              continue;
                          }
                          
                          echo '<span><a href="' . esc_url( $term_link ) . '"><b>' . $term->name . '</b></a></span>';
                      }
                ?>
                </small>
            </h5>
            <?php } 
                  
                  if ($length !="")
                      echo $length;
                      
                if( $service_advice !=""){ ?>
                <h5>
                <?php _e('Additional info','cpsmartcrm') ;?>:<br /> <small><?php echo $service_advice ?></small>
                </h5>

                <?php }
                ?>
        <small><?php the_excerpt()?></small>
        <a href="<?php the_permalink(); ?>" class="more-link" style="float:right"><?php _e('Read More','cpsmartcrm') ?></a>
    </div>
    
</div> 

<?php
echo "<br />";
 endwhile;
 get_footer();