<?php 
$plugin_dir  = dirname(dirname(dirname(__FILE__)));
require_once($plugin_dir.'/inc/gateways/config.php'); 

global $wpdb;
$options=get_option('CRM_services_settings');
$logo=get_option('CRM_general_settings');
$logo=$logo['company_logo'];
get_header();
 while ( have_posts() ) : the_post();
    global $current_user;
    wp_get_current_user();
    // print_r($current_user);
    $email=$current_user->user_email;
    $usermeta=get_user_meta($current_user->ID);
    //echo "<pre>";
    //var_dump($usermeta);
    //echo "</pre>";
    if($usermeta!=false)
    {
        $SQL="SELECT * FROM ".WPsCRM_TABLE."clienti WHERE ID_clienti= ".$usermeta['CRM_ID'][0];    
        
        $_address=$wpdb->get_row($SQL)->indirizzo;
        $_town=$wpdb->get_row($SQL)->localita;
        $_zip=$wpdb->get_row($SQL)->cap;
        $_phone=$wpdb->get_row($SQL)->telefono1;
        $_businessName=$wpdb->get_row($SQL)->ragione_sociale;
        //gestire radio button
        if($_businessName !="")
            $checked2="checked";
        else
            $checked1="checked";
        $_cf=$wpdb->get_row($SQL)->cod_fis;
        $_piva=$wpdb->get_row($SQL)->p_iva;
        
    }
 $code=get_post_meta($post->ID,'SOFT_service_code',true);
 $price_type=get_post_meta($post->ID,'SOFT_service_price_per_month',true);
 $full_price=get_post_meta($post->ID,'SOFT_service_price',true);
 $list_price_1=get_post_meta($post->ID,'SOFT_service_price_1',true);
 $iva_p=get_post_meta($post->ID,'SOFT_vat',true);
 if (!$iva_p)
 {
   $opts=get_option('CRM_documents_settings');
   $iva_p=$opts['default_vat'];
 }
 $short_desc=get_post_meta($post->ID,'SOFT_service_short_desc',true);
 $service_advice=get_post_meta($post->ID,'SOFT_service_advice',true);
 $subscription=get_post_meta($post->ID,'SOFT_service_subscription',true);
 $gallery_thumbs=rwmb_meta( 'SOFT_service_gallery', 'type=image&size=thumbnail' );
 $length="";
 if($subscription !="" && $subscription !=0){
     $SQL="SELECT * FROM ".WPsCRM_TABLE."subscriptionrules WHERE ID=".$subscription;

     $length="<h5>".__('Subscription length','cpsmartcrm').": <strong>".$wpdb->get_row($SQL)->length." ". __('Months','cpsmartcrm')."</strong></h5>";
     $steps=$wpdb->get_row($SQL)->steps;
     $months=$wpdb->get_row($SQL)->length;
 }

 $terms = get_the_terms( $post->ID, 'services_cat' );
 if($full_price!=""){
     $discount='<span class="badge badge-discount"></span> ';
     $showed_price='<span class="CRM_full_price">'.$full_price.' '.$options['currency'].'</span><span class="CRM_sale_price">'.$list_price_1.' '.$options['currency'].'</span>';
    }
 else{
     $showed_price='<span class="CRM_sale_price">'.$list_price_1.' '.$options['currency'].'</span>';
 }
 $price_type=="month" ? $month= __("per month",'cpsmartcrm')." " : $month="" ;
 if($month !="")
 $button_amount=$list_price_1 * $wpdb->get_row($SQL)->length;
 else $button_amount=$list_price_1;
 $stripe_amount=$button_amount*1.22;
 $stripe_amount=round($stripe_amount,2);
 $stripe_amount=$stripe_amount * 100;
 if(isset($_GET['customer']) && isset($_GET['invoice']) ){
     $user_page=get_option('CRM_user_page');
     //$found = $wpdb->get_var( "SELECT COUNT(*) FROM ".WPsCRM_TABLE."clienti WHERE stripe_ID='".$_GET['customer'] )'";
     //if($found==1)
        $SQL="SELECT nome,cognome,ragione_sociale FROM ".WPsCRM_TABLE."clienti WHERE stripe_ID='".$_GET['customer']."' ";
        
     $_html= '<div class="_smartcrm_service"><pre>';
     $_html.="<h3>". __('Thanks for your purchase','cpsmartcrm').", ". $wpdb->get_row($SQL)->nome." ". $wpdb->get_row($SQL)->cognome."</h3>";
     $_html.="<p>".__("We've sent you an email with your invoice.",'cpsmartcrm')."</p>";
     if(!is_user_logged_in()){
         $_html.="<p>".__("You can access your profile page after logging-in from the form here below. From there you can download your invoices and check status of your subscriptions.",'cpsmartcrm')."</p>";
         $_html.="<h5><a href=\"".get_post_permalink(get_the_ID())."\">".__("Return to the previous page.",'cpsmartcrm')."</a></h5>";
         $_html.="<h5><a href=\"".home_url()."\">".__("Return to homepage.",'cpsmartcrm')."</a></h5>";
         $_html.= "</pre>";
         echo $_html;
         //print_r($options);

         //echo "poo".$user_page;
         echo '<div  class="CRM_login">';
         wp_login_form(array('echo'=>true,'redirect'=>get_page_link($user_page))); 
         echo '</div></div>';
     }
     else{
        
     $_html.="<p>".__("You can access your profile page from the link here below. From there you can download your invoices and check status of your subscriptions.",'cpsmartcrm')."</p>";
         $_html.="<h5><a href=\"".get_page_link($user_page)."\">".__("Profile page.",'cpsmartcrm')."</a></h5>";
         $_html.="<h5><a href=\"".get_post_permalink(get_the_ID())."\">".__("Return to the previous page.",'cpsmartcrm')."</a></h5>";
         $_html.="<h5><a href=\"".home_url()."\">".__("Return to homepage.",'cpsmartcrm')."</a></h5>";
         $_html.= "</pre></div>";
         echo $_html;
     }
    
 }
 
elseif($_SERVER['REQUEST_METHOD'] === 'POST')
    {
    //print_r($_POST);
    $token=$_POST['stripeToken'];
    $email=$_POST['stripeEmail'];
    $name=$_POST["CRM_firstname"];
    $lastname=$_POST["CRM_lastname"];
    $address=$_POST["CRM_address"];
    $zip=$_POST["CRM_zip"];
    $businessname=$_POST["CRM_businessname"];
    $phone=$_POST["CRM_phone"];
    $cf=$_POST["CRM_client_CF"];
    $iva=$_POST["CRM_client_IVA"];
    $city=$_POST["CRM_town"];

    $SQL="SELECT ID_clienti,email, stripe_ID FROM ".WPsCRM_TABLE. "clienti WHERE email LIKE '".$email."'";
    //cliente esistente
    if( $wpdb->get_row($SQL)->email !="") 
        {

        $customer = \Stripe\Customer::retrieve($wpdb->get_row($SQL)->stripe_ID);
        $customer->card = $token;
        $customer->save();
        $crm_cliente_id=$wpdb->get_row($SQL)->ID_clienti;
        }
    else{

    $customer = \Stripe\Customer::create(
            array(
            'email' => $email,
            'card'  => $token
            )
        );
        //campo user_id su clienti
    }
    $stripe_ID=$customer->id;

    $charge = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount'   => $stripe_amount,
        'currency' => 'eur',
        'description' =>  $short_desc
    ));
    if(! isset($charge['failure_code']) || $charge['failure_code']=="")
        {
            //pagamento ok
        ?>
        il pagamento di <?php echo $stripe_amount / 100 ?> è andato a buon fine
        <?php
            do_action('crm_after_successful_stripe_payment');
          if (!$crm_cliente_id)
          {
              $wpdb->insert(
			  WPsCRM_TABLE."clienti",
			  array('nome' => $name,'cognome' => $lastname,'ragione_sociale' => $businessname,'indirizzo' => $address,'cap' => $zip, 'localita' => $city, 'telefono1' => $phone, 'email' => $email, 'p_iva' => $iva, 'cod_fis' => $cf, 'stripe_ID' => $stripe_ID),
			  array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d')
		      );

            $crm_cliente_id = $wpdb->insert_id;
          }
          else
          {
             $wpdb->update(
			  WPsCRM_TABLE."clienti",
			  array('nome' => $name,'cognome' => $lastname,'ragione_sociale' => $businessname,'indirizzo' => $address,'cap' => $zip, 'localita' => $city, 'telefono1' => $phone, 'email' => $email, 'p_iva' => $iva, 'cod_fis' => $cf, 'stripe_ID' => $stripe_ID),
			  	array('ID_clienti'=>$crm_cliente_id),
			  array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d')
		      );
          }
        	$sql="select max(progressivo) as last_reg from ".WPsCRM_TABLE."documenti where tipo=2";
        	$last_reg=$wpdb->get_row($sql)->last_reg;
        	$new_reg=$last_reg+1;
        	$totale_fattura=$button_amount*1.22;
        	$totale_imposta=$button_amount*0.22;
        $result=$wpdb->query( 
    	 $wpdb->prepare(
    				"insert into ".WPsCRM_TABLE."documenti set tipo=2, data=CURDATE(), data_inserimento=CURDATE(), fk_clienti=%d, progressivo=%d, totale_imponibile=%f, totale=%f, totale_imposta=%f, pagato=1, oggetto=''",
    				$crm_cliente_id, $new_reg, $button_amount, $totale_fattura, $totale_imposta
    			)
	);

          $doc_id = $wpdb->insert_id;
        $wpdb->insert(
			  WPsCRM_TABLE."documenti_dettaglio",
			  array('fk_documenti' => $doc_id,'fk_articoli' => $post->ID, 'prezzo' => $button_amount, 'qta' => 1, 'totale' => $totale_fattura, 'n_riga' => 1, 'descrizione' => $short_desc, 'iva' => $iva_p, 'tipo' => 1, 'fk_subscriptionrules' => $subscription),
			  array('%d','%d','%f','%d','%f','%d','%s','%d','%d','%d')
		);
          $document_row_id=$wpdb->insert_id;

          $attachment=1;
          include($plugin_dir."/inc/crm/mpdf/mpdf.php");
          $stylesheet = file_get_contents($plugin_dir.'/css/documents/pdf_documents.css');
          $ID=$doc_id;
          include($plugin_dir.'/inc/templates/print_invoice.php');
          $userdata=array();
          foreach($_POST as $post_key=>$post_value)
              $userdata[$post_key]=$post_value;
          $userdata['CRM_ID']=$crm_cliente_id;
		  //$userdata['CRM_email']=$email;
          $user_id=apply_filters( 'CRM_add_User',$userdata);
          //update clienti
          $wpdb->update(
                WPsCRM_TABLE."clienti",
                array(
                    'user_id'=>$user_id
                ),
                array('ID_clienti'=>$crm_cliente_id),
                array('%d')
            );
          // SCHEDULER
          $admin_email = get_option( 'admin_email' );
          $header = 'MIME-Version: 1.0' . "\r\n";
          $header.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          $header.= 'From: info@smart-cms.n3rds.work/' . "\r\n";

          $data_scadenza=date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("Y")));

          $data_inserimento=date("Y-m-d H:i:s");
          $scheduler_subject=__("Expiring service",'cpsmartcrm');
          $scheduler_notes= __("Expiring service",'cpsmartcrm');
          $scheduler_notes.= " ".$short_desc." ";
          $scheduler_notes.=__("Invoice #",'cpsmartcrm');
          $scheduler_notes.=" ".$new_reg." ";
          $scheduler_notes.=__("to",'cpsmartcrm');
          $scheduler_notes.=" "."$name $lastname";
          $wpdb->insert(
					WPsCRM_TABLE."agenda",
					array(
						'oggetto'=>$scheduler_subject,
						'fk_clienti'=>$crm_cliente_id,
						'annotazioni'=>$scheduler_notes,
						'start_date'=>$data_scadenza,
						'end_date'=>$data_scadenza,
						'data_inserimento'=>$data_inserimento,
						'fk_subscriptionrules'=>$subscription,
						'fk_documenti'=>$doc_id,
						'fk_documenti_dettaglio'=>$document_row_id,
						'tipo_agenda'=>4
					),
					array('%s','%d','%s','%s','%s','%s','%d','%d','%d', '%d')
				);
          $fk_agenda=$wpdb->insert_id;
          $mail= new CRM_mail(array("ID_doc"=> $doc_id) );
          do_action('crm_after_notification');
          ?>
            <script>
               window.location.replace('<?php echo get_the_permalink($post->ID) ?>?customer=<?php echo $stripe_ID ?>&invoice=<?php echo $doc_id ?>');
            </script>
           <?php
        }
        else
        {
            //problema nel pagamento
            ?>
            il pagamento di <?php echo $stripe_amount?> non è andato a buon fine: <?php echo $charge['failure_message']?>
            <?php
        }
    exit;
    }
else{
?>
    <div class="_smartcrm_service container">
       
        <h1 class="entry-title"><?php the_title();?></h1>
        <div class="entry-content" style="float:left;width: 100%;">
        <figure>
        <?php if ( has_post_thumbnail() ) 
                {echo get_the_post_thumbnail($post->ID, 'medium', array('class' => 'alignleft'));}
               echo $discount;?>
        </figure>
        
            <form action="<?php echo get_permalink( $post->ID )?>" method="post"  id="stripeCheckout" >
                <div class="CRM_main_show_price">
                    <span><?php _e('PRICE','cpsmartcrm') ?>: <?php echo $showed_price." ".$month?><small>(+ iva)</small></span> <a class="button button-buy" data-amount="<?php echo $button_amount?>" data-code="<?php echo $code?>" data-subscription="<?php echo $subscription?>"><?php _e('Buy now','cpsmartcrm') ?> &raquo;</a>
                </div>

                    <div id="crmForm" style="display:none">
                        <ul>
                            <li><label><?php _e('First Name','cpsmartcrm')?></label><input type="text" id="CRM_firstname" name="CRM_firstname" value="<?php echo $usermeta['first_name'][0]?>" required /></li>
                            <li><label><?php _e('Last Name','cpsmartcrm')?></label><input type="text" id="CRM_lastname" name="CRM_lastname" value="<?php echo $usermeta['last_name'][0]?>" required/></li>
                            <li><label><?php _e('Email','cpsmartcrm')?></label><input type="email" id="CRM_email" name="CRM_email" value="<?php echo $email ?>" required /></li>
                            <li><label><?php _e('Confirm Email','cpsmartcrm')?></label><input type="email" id="CRM_cfmemail" name="CRM_cfmemail" value="<?php echo $email ?>" required /></li>
                            <li><label class="_address"><?php _e('Address','cpsmartcrm')?></label><input type="text" name="CRM_address" value="<?php echo $_address ?>" required /></li>
                            <li><label><?php _e('Town','cpsmartcrm')?></label><input type="text" name="CRM_town" value="<?php echo $_town ?>" required /></li>
                            <li><label><?php _e('ZIP Code','cpsmartcrm')?></label><input type="text" name="CRM_zip" value="<?php echo $_zip ?>"/></li>              
                            <li><label><?php _e('Phone','cpsmartcrm')?></label><input type="text" name="CRM_phone" value="<?php echo $_phone ?>" required /></li>
                            <li><label><?php _e('Client type','cpsmartcrm')?></label><?php _e('Private','cpsmartcrm')?>: <input type="radio" name="CRM_client_type" value="privato" <?php echo $checked1 ?>/> <?php _e('Business','cpsmartcrm')?>:<input type="radio" name="CRM_client_type" value="azienda" <?php echo $checked2 ?>/></li>
                            <li id="business_name" style="display:none"><label><?php _e('Business Name','cpsmartcrm')?></label><input type="text" id="CRM_businessname" name="CRM_businessname" value="<?php echo $_businessName ?>" /></li>
                            <li><label><?php _e('Cod. Fisc.','cpsmartcrm')?></label><input type="text" id="CRM_client_CF" name="CRM_client_CF" class="_fiscal" value="<?php echo $_cf ?>" required/></li>
                            <li><label><?php _e('Vat code','cpsmartcrm')?></label><input type="text" id="CRM_client_IVA" name="CRM_client_IVA" class="_fiscal" value="<?php echo $_piva ?>"/></li>
                            <hr />
                            <?php if($current_user->ID ==""){ ?>
                            <li><h5 ><span class="register_link"><?php _e('Register','cpsmartcrm')?> &raquo;</span> <small style="margin-left:30px;margin-right:20px;"><?php _e('Already registerd','cpsmartcrm')?>? <span class="show_login _highlighted"><?php _e('login here','cpsmartcrm')?>&raquo;</span></small></h5></li>
                            <li class="_register"><label><?php _e('Choose a username','cpsmartcrm')?></label><input name="CRM_username" id="CRM_username" type="text" required/></li>
                            <li class="_register"><label><?php _e('Choose a Password','cpsmartcrm')?></label><input type="password" name="CRM_password" id="CRM_password" /></li>
                            <li class="_register"><label><?php _e('Confirm Password','cpsmartcrm')?></label><input type="password" name="CRM_cfmpassword" id="CRM_cfmpassword" /></li>
                            
                            <?php }
                                  else{ ?>

                            <input name="CRM_username" id="CRM_username" type="hidden"  value="<?php echo $current_user->user_login ?>"/>
                            <?php  } ?>
                            <li id="sendToRegistration" style="display:none"><span id= "_register" onclick="_prepareToBuy()" class="button"><?php _e('Continue','cpsmartcrm')?>&raquo;</span></li>
                            <input type="submit" id="_send" value="<?php _e('Buy Now','cpsmartcrm')?>&raquo;" style="display:none" />
                            <input type="hidden" id="pwd_strenght" name="pwd_strenght" />
                            <input type="hidden" id="full_amount" name="full_amount" value="<?php echo $button_amount?>"/>
                        </ul>
                    </div>

                <div id="stripeForm" style="display:none"> 
                 

                </div> 
           </form>
            <ul class="_login" style="float:left;padding:20px;border:1px solid #ccc">
                <li  ><?php wp_login_form(array('remember'=>false,'echo'=>true,'redirect'=>get_the_permalink(get_the_ID()).'?log=self')) ?></li>
            </ul>

        </div>
        <h3 style="margin-top:20px"><?php _e('Service Info and features','cpsmartcrm') ?>:</h3>
        <div class="CRM_service_features">
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
                if(count($gallery_thumbs) > 0){ ?> 
                <h5 style="background:#fafafa" ><?php _e('Gallery','cpsmartcrm');?></h5>
                <div id="CRM_service_gallery">
                <?php 
                    foreach ( $gallery_thumbs as $image )
                        echo "<a href='{$image['full_url']}' title='{$image['title']}' rel='thickbox'><img src='{$image['url']}' width='{$image['width']}' height='{$image['height']}' alt='{$image['alt']}' /></a>";
                    ?>
                 </div>
                
                <?php } ?>
        </div>
        <div class="entry-content content" style="float:left">
        <?php  the_content(); 
               //do_action('crm_pippo');
               ?>
        </div>
    </div>
<style>

</style>
<script>

    function _prepareToBuy() {
        var email=jQuery('#CRM_email').val();
        var user = jQuery('#CRM_username').val();
        var strenght = jQuery('.strength_meter div').attr('class');
        jQuery('#pwd_strenght').val(strenght)
        console.log(user + email + strenght);
            var _html='<script src="https://checkout.stripe.com/checkout.js" class="stripe-button" id="stripePopup"\n\
                data-key="<?php echo $stripe["publishable_key"]; ?>"\n\
                data-description="<?php echo $short_desc?>"\n\
                data-amount="<?php echo $stripe_amount?>"\n\
                data-locale="it"\n\
                data-image="<?php echo $logo ?>"\n\
                data-email="' + email + '"\n\
                data-currency="<?php echo $options["currency"]?>"<//script>';
        jQuery('#stripeForm').html(_html);
        jQuery.ajax({
            url: ajaxurl,
            data: {
                'action': 'CRM_is_user_registrable',
                'email': email,
                '_user': user,
                
            },
            type: "POST",
            success: function (response) {
                console.log(response);
                if (response != "false") {
                    <?php
                    if ( ! is_user_logged_in ()){
                    ?>
                        jQuery('#CRM_username').val(response)
                    <?php } ?>

                    jQuery('#_register').fadeToggle('400')
                    setTimeout(function () {
                        jQuery('#_send').fadeToggle('fast');
                    },400)
                    
                }
                else (alert('Utente esistente'));
            }
        })
            
        }
    jQuery(document).ready(function ($) {
        <?php
        if(isset($_GET['log']) && $_GET['log']=="self")
        {?>

        setTimeout(function () {
            $('.button-buy').trigger('click');
        }, 30)
        <?php } 
        
        ?> 
        $('.show_login').on('click', function () {
            $('._register').slideToggle();
            $('._login').slideToggle();
            $('.register_link').removeClass('_highlighted');
            $('.show_login').addClass('_highlighted');
        })
        $('.register_link').on('click', function () {
            $('._login').slideUp();
            $('._register').slideDown();
            $('.show_login').removeClass('_highlighted');
            $('.register_link').addClass('_highlighted');
        })
        $('.button-buy').on('click', function () {
            $('#crmForm').slideToggle('slow');
        })
        $('.stripe-button-el span').text('Buy Now')
        $('.stripe-button-el').addClass('button button-buy').removeClass('stripe-button-el');

        if ($('input[name="CRM_client_type"]').filter(':checked').val() == "azienda") {
            $('#business_name').show();
            $('._address').html("<?php _e('Business Address','cpsmartcrm')?>");
            $('#CRM_businessname').attr('required', 'required');
            $('#CRM_client_IVA').attr('required', 'required');
        }

        else {
            $('#business_name').hide();
            $('._address').html('<?php _e('Address','cpsmartcrm')?>');
            $('#CRM_businessname').attr('required', false);
            $('#CRM_client_IVA').attr('required', false);
        }
        $('#crmForm input[required="required"],:input[required]').addClass('_required')
        $('input[name="CRM_client_type"]').on('change', function () {

            if ($(this).filter(':checked').val() == "azienda") {
                $('._address').html("<?php _e('Business Address','cpsmartcrm')?>");
                $('#business_name').slideDown();
                $('#CRM_businessname').attr('required', 'required').addClass('_required');
                $('#CRM_client_IVA').attr('required', 'required').addClass('_required');
            }

            else {
                $('._address').html('<?php _e('Address','cpsmartcrm')?>');
                $('#business_name').slideUp();
                $('#CRM_businessname').attr('required', false).removeClass('_required');
                $('#CRM_client_IVA').attr('required', false).removeClass('_required');
            }

        })
        $("#CRM_password").strength({
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter',
            strengthButtonClass: 'button_strength',
            strengthButtonText: '<div style="float:right"><span class="show_password"></span>show</div>',
            strengthButtonTextToggle: '<div style="float:right"><span class="show_password"></span>hide</div>'
        });
        $('input[type="password"]').on('input',function () {
            if (($('#CRM_password').val() != $('#CRM_cfmpassword').val()) || ($('#CRM_email').val() != $('#CRM_cfmemail').val()) || $('#CRM_password').val() == "" || $('#CRM_cfmpassword').val() == "" || $('#CRM_email').val() == "" || $('#CRM_cfmemail').val() == "" || $('#CRM_username').val() == "") {
                $('#sendToRegistration').hide()
                $('#sendToRegistration input[type="submit"]').attr('disabled', 'disabled');
            }
            else {
                $('#sendToRegistration').show()
                $('#sendToRegistration input[type="submit"]').attr('disabled', false);
            }
        })
        $('input[type="email"]').on('input', function () {
            if (($('#CRM_email').val() != $('#CRM_cfmemail').val()) || ($('#CRM_password').val() != $('#CRM_cfmpassword').val()) || $('#CRM_password').val() == "" || $('#CRM_cfmpassword').val() == "" || $('#CRM_email').val() == "" || $('#CRM_cfmemail').val() == "" || $('#CRM_username').val() == "") {
                $('#sendToRegistration').hide()
                $('#sendToRegistration input[type="submit"]').attr('disabled', 'disabled');
            }
            else {
                $('#sendToRegistration').show()
                $('#sendToRegistration input[type="submit"]').attr('disabled', false);
            }
                        $('#sendToRegistration').show()})
        <?php
    if (is_user_logged_in ()){
        ?>
            $('#sendToRegistration').show()
        <?php } ?>
    })
</script>
<?php 
}
endwhile; // end of the loop. 
 get_footer();     
?>