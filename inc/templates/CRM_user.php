<?php 
if(!is_user_logged_in())
    @die("You don't have permission to view this page");


if ($ID=$_GET["id_invoice"]) //se stiamo visualizzando una singola fattura
{
    global $current_user;
    $user_id=$current_user->ID;
    $sql="select * from ".WPsCRM_TABLE."documenti, ".WPsCRM_TABLE."clienti where id=$ID and fk_clienti=ID_clienti and user_id=$user_id";
    //echo $sql;
	  if ($wpdb->get_row($sql))
	  {
      $plugin_dir  = dirname(dirname(dirname(__FILE__)));
      include($plugin_dir."/inc/crm/mpdf/mpdf.php");
      $stylesheet = file_get_contents($plugin_dir.'/css/documents/pdf_documents.css');
      include ( __DIR__)."/print_invoice.php";
	  }
	  else
      {
          get_header();
          echo '<h2 style="text-align:center">Non sei autorizzato a visualizzare questa fattura</h2>';
          get_footer();
      }
}
else
{
get_header();
 while ( have_posts() ) : the_post();
    global $current_user;
    wp_get_current_user();
    
     //print_r($current_user);
     $email=$current_user->user_email;
     $usermeta=get_user_meta($current_user->ID);
     //print_r($usermeta);
     $client_ID=$usermeta['CRM_ID'][0];
     global $wpdb;
     //echo $client_ID;
     ?>
<div class="_smartcrm_service">
    <h3 style="margin:10px"><?php _e('Welcome','cpsmartcrm')?>  <?php echo $current_user->display_name ?> <small style="margin-right:20px;float:right"><a href="<?php  echo wp_logout_url( home_url()  ); ?>"><?php _e('Logout','cpsmartcrm')?></a></small></h3>
<ul class="tabs">
  <li class="active" rel="tab1"><?php _e('Profile Data','cpsmartcrm')?></li>
  <li rel="tab2"><?php _e('Invoices','cpsmartcrm')?></li>
    <?php do_action('add_extra_tabs')?>
  <!--<li rel="tab3">Subscriptions</li>
  <li rel="tab4">Tickets</li>-->
</ul>
<div class="tab_container">
  <h3 class="d_active tab_drawer_heading" rel="tab1">Tab 1</h3>
  <div id="tab1" class="tab_content">
  <h2><?php _e('Profile data','cpsmartcrm')?></h2>
    <?php include(dirname(__FILE__).'/user-tabs/tab1-form.php')?>
  </div>
  <!-- #tab1 -->
  <h3 class="tab_drawer_heading" rel="tab2">Tab 2</h3>
  <div id="tab2" class="tab_content">
  <h2><?php _e('Your Invoices','cpsmartcrm')?></h2>
    <?php include(dirname(__FILE__).'/user-tabs/tab2-invoices.php')?>
  </div>

  <!-- #tab2 -->
<?php do_action('add_extra_tabs_divs')?>
  <!-- #tab4 --> 
</div>
<!-- .tab_container -->

</div>
<style>
    ul.tabs {
  margin: 0;
  padding: 0;
  float: left;
  list-style: none;
  height: 32px;
  border-bottom: 1px solid #333;
  width: 99%;
}

ul.tabs li {
  float: left;
  margin: 0;
  cursor: pointer;
  padding: 0px 21px;
  height: 31px;
  line-height: 31px;
  border-top: 1px solid #333;
  border-left: 1px solid #333;
  border-bottom: 1px solid #333;
  background-color: #666;
  color: #ccc;
  overflow: hidden;
  position: relative;
}

.tab_last {
  border-right: 1px solid #333;
}

ul.tabs li:hover {
  background-color: #ccc;
  color: #333;
}

ul.tabs li.active {
  background-color: #fff;
  color: #333;
  border-bottom: 1px solid #fff;
  display: block;
}

.tab_container {
  border: 1px solid #333;
  border-top: none;
  clear: both;
  float: left;
  width: 99%;
  background: #fff;
  overflow: auto;
  padding-bottom:30px
}

.tab_content {
  padding: 20px;
  display: none;
}

.tab_drawer_heading {
  display: none;
}

@media screen and (max-width: 480px) {
  .tabs {
    display: none;
  }
  .tab_drawer_heading {
    background-color: #ccc;
    color: #fff;
    border-top: 1px solid #333;
    margin: 0;
    padding: 5px 20px;
    display: block;
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .d_active {
    background-color: #666;
    color: #fff;
  }
}
</style>
<script>
    // tabbed content
    // http://www.entheosweb.com/tutorials/css/tabs.asp
   jQuery(".tab_content").hide();
   jQuery(".tab_content:first").show();

    /* if in tab mode */
   jQuery("ul.tabs li").click(function () {

       jQuery(".tab_content").hide();
        var activeTab =jQuery(this).attr("rel");
       jQuery("#" + activeTab).fadeIn();

       jQuery("ul.tabs li").removeClass("active");
       jQuery(this).addClass("active");

       jQuery(".tab_drawer_heading").removeClass("d_active");
       jQuery(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

    });
    /* if in drawer mode */
   jQuery(".tab_drawer_heading").click(function () {

       jQuery(".tab_content").hide();
        var d_activeTab =jQuery(this).attr("rel");
       jQuery("#" + d_activeTab).fadeIn();

       jQuery(".tab_drawer_heading").removeClass("d_active");
       jQuery(this).addClass("d_active");

       jQuery("ul.tabs li").removeClass("active");
       jQuery("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
    });


    /* Extra class "tab_last" 
	   to add border to right side
	   of last tab */
   jQuery('ul.tabs li').last().addClass("tab_last");

</script>
    <?php
 endwhile;
 get_footer();
}