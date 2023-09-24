<?php 
$SQL="SELECT * FROM ".WPsCRM_TABLE."clienti WHERE ID_clienti= ".$client_ID;    
//echo $SQL;
     
    $_address=$wpdb->get_row($SQL)->indirizzo;
    $_town=$wpdb->get_row($SQL)->localita;
    $_zip=$wpdb->get_row($SQL)->cap;
    $_phone=$wpdb->get_row($SQL)->telefono1;
    $_businessName=$wpdb->get_row($SQL)->ragione_sociale;
    $_firstName=$wpdb->get_row($SQL)->nome;
    $_lastName=$wpdb->get_row($SQL)->cognome;
    $_email=$wpdb->get_row($SQL)->email;
    //gestire radio button
    if($_businessName !="")
        $checked2="checked";
    else
        $checked1="checked";
    $_cf=$wpdb->get_row($SQL)->cod_fis;
    $_piva=$wpdb->get_row($SQL)->p_iva;
    // echo __FILE__;
    ?>
<form id="crmForm">
    <div >
        <ul>
            <li><label><?php _e('First Name','cpsmartcrm')?></label><input type="text" id="nome" name="nome" value="<?php echo $_firstName?>" required /></li>
            <li><label><?php _e('Last Name','cpsmartcrm')?></label><input type="text" id="cognome" name="cognome" value="<?php echo $_lastName?>" required/></li>
            <li><label><?php _e('Email','cpsmartcrm')?></label><input type="email" id="email" name="email" value="<?php echo $_email ?>" required /></li>
            <li><label><?php _e('Address','cpsmartcrm')?></label><input type="text" name="indirizzo" value="<?php echo $_address?>"/></li>
            <li><label><?php _e('Town','cpsmartcrm')?></label><input type="text" name="localita" value="<?php echo $_town?>"/></li>
            <li><label><?php _e('ZIP Code','cpsmartcrm')?></label><input type="text" name="cap" value="<?php echo $_zip ?>"/></li>              
            <li><label><?php _e('Phone','cpsmartcrm')?></label><input type="text" name="telefono1" value="<?php echo $_phone?>"/></li>
            <li style="width:50%;height: 60px;line-height: 70px;"><label><?php _e('Client type','cpsmartcrm')?></label><?php _e('Private','cpsmartcrm')?>: <input type="radio" name="CRM_client_type" value="privato" <?php echo $checked1  ?>/> <?php _e('Business','cpsmartcrm')?>:<input type="radio" name="CRM_client_type" value="azienda" <?php echo  $checked2 ?>/></li>
            <li id="business_name" style="display:none"><label><?php _e('Business Name','cpsmartcrm')?></label><input type="text" id="ragione_sociale" name="ragione_sociale" value="<?php echo $_businessName ?>" /></li>
            <li><label><?php _e('Cod. Fisc.','cpsmartcrm')?></label><input type="text" id="cod_fis" name="cod_fis" class="_fiscal" value="<?php echo $_cf ?>" required/></li>
            <li><label><?php _e('P.IVA','cpsmartcrm')?></label><input type="text" id="p_iva" name="p_iva" class="_fiscal" value="<?php echo $_piva ?>"/></li>

        </ul>
    </div>
</form>
<span class="button btn" id="save_user" style="margin:30px"><?php _e('Save','cpsmartcrm')?> &raquo;</span>
<script>
    jQuery('#save_user').click(function (e) {
        e.preventDefault;
        var form = jQuery('form');
        //alert(form.serialize());
        jQuery.ajax({
            url: ajaxurl,
            data: {
                'action': 'WPsCRM_save_crm_user_fields',
                'fields':form.serialize(),
                'client_ID':<?=$client_ID?>,
                
            },
            type: "POST",
            success: function (response) {
                console.log(response);
                window.location.reload();
            }
        })

    })
</script>
<style>
    #crmForm ul{margin-bottom:40px}
    #crmForm label{width:50%;float:left}
    #crmForm input:not([type=radio]){width:50%;float:left}
    #crmForm li{float:left;width:99%}
</style>
