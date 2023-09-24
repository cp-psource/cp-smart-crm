<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//global $WPsCRM_db_version;
$WPsCRM_db_version = '1.5.16';
function WPsCRM_crm_install() {
	global $wpdb;
	global $table_prefix;
	define ('WPsCRM_SETUP_TABLE',$table_prefix.'smartcrm_');
	global $WPsCRM_db_version;

	$charset_collate = $wpdb->get_charset_collate();

	$sql[] = "CREATE TABLE `".WPsCRM_SETUP_TABLE."clienti` (
  `ID_clienti` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL DEFAULT '0',
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `ragione_sociale` varchar(250) NOT NULL DEFAULT '',
  `indirizzo` varchar(200) DEFAULT NULL,
  `cap` varchar(10) DEFAULT NULL,
  `localita` varchar(55) NOT NULL DEFAULT '',
  `provincia` varchar(5) DEFAULT NULL,
  `nazione` varchar(100) DEFAULT NULL,
  `telefono1` varchar(50) DEFAULT NULL,
  `telefono2` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `sitoweb` varchar(100) NOT NULL,
  `skype` varchar(100) NOT NULL,
  `p_iva` varchar(30) DEFAULT NULL,
  `cod_fis` varchar(30) DEFAULT NULL,
  `annotazioni` text,
  `FK_aziende` int(10) unsigned NOT NULL DEFAULT '0',
  `data_inserimento` date DEFAULT NULL,
  `data_modifica` date DEFAULT NULL,
  `eliminato` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `aggiornato` enum('No','Si') NOT NULL DEFAULT 'No',
  `provenienza` varchar(100) NOT NULL DEFAULT '',
  `luogo_nascita` varchar(200) NOT NULL,
  `data_nascita` date DEFAULT NULL,
  `stripe_ID` varchar(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `tipo_cliente` tinyint(3) unsigned NOT NULL,
  `agente` int(10) unsigned NULL,
  `interessi` varchar(100) NOT NULL DEFAULT '',
  `fatturabile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `custom_fields` text,
  `custom_tax` text,
  `uploads` text,
  PRIMARY KEY (`ID_clienti`),
  KEY `FK_categorie_clienti` (`categoria`),
  KEY `FK_aziende` (`FK_aziende`)
) ENGINE=MyISAM  ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."agenda` (
 `id_agenda` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_aziende` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_utenti_ins` int(10) unsigned NOT NULL DEFAULT '0',
  `oggetto` varchar(255) DEFAULT NULL,
  `fk_utenti_des` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_clienti` int(10) unsigned DEFAULT NULL,
  `fk_contatti` int(10) unsigned NOT NULL DEFAULT '0',
  `data_agenda` date DEFAULT NULL,
  `ora_agenda` time DEFAULT NULL,
  `annotazioni` text NOT NULL,
  `data_inserimento` datetime NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `esito` text,
  `priorita` tinyint(3) unsigned NOT NULL,
  `importante` enum('No','Si') NOT NULL DEFAULT 'No',
  `urgente` enum('No','Si') NOT NULL DEFAULT 'No',
  `fatto` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1=da fare, 2= fatto, 3=cancellato',
  `tipo_agenda` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1=todo, 2= appuntamento, 3=notifica scadenza pagamento fattura, 4=acquisto,5 notifica scadenza servizio',
  `fk_documenti` int(10) unsigned NOT NULL,
  `fk_documenti_dettaglio` int(10) unsigned NOT NULL,
  `fk_subscriptionrules` int(10) unsigned NOT NULL,
  `eliminato` tinyint(3) unsigned NOT NULL,
  `visto` varchar(50)  DEFAULT NULL,
  PRIMARY KEY (`id_agenda`),
  KEY `FK_aziende` (`fk_aziende`),
  KEY `FK_utenti_ins` (`fk_utenti_ins`),
  KEY `FK_utenti_des` (`fk_utenti_des`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."contatti` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_clienti` int(10) unsigned NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `qualifica` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."documenti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` tinyint(3) unsigned NOT NULL COMMENT '1=preventivo, 2=fattura, 3=proforma',
  `data` date NOT NULL,
  `data_inserimento` date NOT NULL,
  `data_timestamp` int(15) NOT NULL,
  `data_scadenza_timestamp` int(15) NOT NULL,
  `oggetto` varchar(100) NOT NULL,
  `riferimento` varchar(100) NOT NULL,
  `fk_clienti` int(10) unsigned NOT NULL,
  `fk_utenti_ins` int(10) unsigned NOT NULL,
  `fk_utenti_age` int(10) unsigned NOT NULL,
  `progressivo` int(11) NOT NULL,
  `totale_imponibile` float(9,2) unsigned NOT NULL,
  `totale_imposta` float(9,2) unsigned NOT NULL,
  `totale` float(9,2) unsigned NOT NULL,
  `tot_cassa_inps` float(9,2) unsigned NOT NULL,
  `ritenuta_acconto` float(9,2) unsigned NOT NULL,
  `totale_netto` float(9,2) unsigned NOT NULL,
  `valore_preventivo` float(9,2) unsigned NOT NULL,
  `sezionale_iva` char(1) NOT NULL,
  `movimenta_magazzino` char(1) NOT NULL,
  `testo_libero` text NOT NULL,
  `modalita_pagamento` varchar(250) NOT NULL,
  `annotazioni` text NOT NULL,
  `commento` text NOT NULL,
  `giorni_pagamento` tinyint(3) unsigned DEFAULT NULL,
  `data_scadenza` date NOT NULL,
  `pagato` tinyint(3) unsigned NOT NULL,
  `registrato` tinyint(3) unsigned NOT NULL,
  `approvato` tinyint(3) unsigned NOT NULL,
  `filename` varchar(100) NOT NULL,
  `perc_realizzo` varchar(10) DEFAULT NULL,
  `notifica_pagamento` tinyint(3) unsigned NOT NULL,
  `fk_woo_order` int(10) unsigned NOT NULL,
  `origine_proforma` tinyint(3) UNSIGNED NOT NULL DEFAULT  '0',
  `tipo_sconto` tinyint(3) UNSIGNED NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."documenti_dettaglio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_documenti` int(10) unsigned NOT NULL,
  `fk_articoli` int(10) unsigned NOT NULL,
  `qta` int(10) unsigned NOT NULL,
  `n_riga` int(10) unsigned NOT NULL,
  `sconto` float(9,2) unsigned NOT NULL,
  `iva` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `prezzo` float(9,2) unsigned NOT NULL,
  `totale` float(9,2) unsigned NOT NULL,
  `tipo` tinyint(3) unsigned NOT NULL COMMENT '1=prodotto, 2=articolo manuale, 3=descrizione, 4=rimborso',
  `codice` varchar(30) NOT NULL,
  `descrizione` text NOT NULL,
  `eliminato` tinyint(3) unsigned NOT NULL ,
  `fk_subscriptionrules` int(10) unsigned NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `e_from` varchar(100) NOT NULL,
  `e_to` varchar(100) NOT NULL,
  `e_subject` varchar(100) NOT NULL,
  `e_body` text NOT NULL,
  `e_sent` tinyint(3) unsigned NOT NULL,
  `e_date` datetime NOT NULL,
  `fk_agenda` int(10) unsigned NOT NULL,
  `fk_documenti` int(10) unsigned NOT NULL,
  `fk_documenti_dettaglio` int(10) unsigned NOT NULL,
  `e_unsent` VARCHAR( 255 ) NOT NULL,
  `fk_clienti` int(10) unsigned NOT NULL,
  `attachments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_label` varchar(50) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `field_alt` varchar(50) NOT NULL,
  `required` tinyint(3) unsigned NOT NULL,
  `multiple` tinyint(1) NOT NULL,
  `sorting` tinyint(3) unsigned NOT NULL,
  `position` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `show_grid` TINYINT UNSIGNED NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."subscriptionrules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `length` tinyint(2) NOT NULL,
  `steps` text NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `s_specific` tinyint(3) unsigned NOT NULL,
  `s_type` tinyint(3) unsigned NOT NULL COMMENT '1=todo, 2=appuntamento',
  `s_email` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_fields` int(10) unsigned NOT NULL,
  `fk_table_name` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1";

	$sql[]="CREATE TABLE `".WPsCRM_SETUP_TABLE."email_templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `lingua` varchar(10) NOT NULL DEFAULT 'it',
  `oggetto` varchar(255) NOT NULL,
  `corpo` text NOT NULL,
  `contesto` varchar(55) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM ".$charset_collate." AUTO_INCREMENT=1 ;";

    //print_r ($sql);//exit;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$nomefile="error_setup_".date("YmdHi").".txt";
	$myFile = WPsCRM_DIR."/logs/".$nomefile;
	$msg="";
	foreach($sql as $q)
    {
		dbDelta( $q );
    };

/*	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` CHANGE  `categoria_merceologica`  `luogo_nascita` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$fo = fopen($myFile, 'a');
		$msg="setup.php Line 232--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);

    };
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` CHANGE  `indirizzo`  `indirizzo` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$fo = fopen($myFile, 'a');
		$msg="setup.php Line 243--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);

    };
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `data_nascita` DATE NULL DEFAULT NULL AFTER `luogo_nascita`;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 254--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);

    };
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `nazione` VARCHAR( 100 ) DEFAULT NULL AFTER  `provincia` ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 264--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);

    };
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti` ADD  `tot_cassa_inps` FLOAT( 9, 2 ) UNSIGNED NOT NULL AFTER  `totale` ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 274--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti` ADD  `fk_woo_order` INT( 10 ) UNSIGNED NOT NULL AFTER  `notifica_pagamento` ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 283--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."emails` ADD  `fk_clienti` INT UNSIGNED NOT NULL , ADD  `attachments` TEXT NOT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 292--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."emails` CHANGE  `e_date`  `e_date` DATETIME NOT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 301--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti`  ADD  `ritenuta_acconto` FLOAT( 9, 2 ) UNSIGNED NOT NULL AFTER  `tot_cassa_inps` ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 310--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti` ADD  `totale_netto` FLOAT( 9, 2 ) UNSIGNED NOT NULL AFTER  `ritenuta_acconto` ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 319--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `interessi` VARCHAR( 100 ) NOT NULL DEFAULT  '';";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 327--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti`  ADD  `origine_proforma` TINYINT UNSIGNED NOT NULL DEFAULT  '0';";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 337--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `fatturabile` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 346--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` CHANGE  `agente`  `agente` INT( 10 ) UNSIGNED NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 355--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `custom_fields` TEXT ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 362--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."clienti` ADD  `custom_tax` TEXT ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 371--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."fields`  ADD `multiple` TINYINT NOT NULL;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 379--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti`  ADD  `tipo_sconto` tinyint(3) UNSIGNED NOT NULL DEFAULT  '0';";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 390--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."fields`  ADD `show_grid` TINYINT UNSIGNED NOT NULL DEFAULT  '0';";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 401--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE  `".WPsCRM_SETUP_TABLE."documenti_dettaglio` CHANGE  `sconto`  `sconto` FLOAT( 9, 2 ) UNSIGNED NOT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 410--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE `".WPsCRM_SETUP_TABLE."clienti` ADD `uploads` TEXT NOT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 415--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};
	try {
		$alter_sql = "ALTER TABLE `".WPsCRM_SETUP_TABLE."documenti` ADD `perc_realizzo` VARCHAR( 10 ) DEFAULT NULL ;";
		$wpdb->query( $alter_sql);
	}
	catch (Exception $exc) {
		$msg="setup.php Line 415--";
		fwrite($fo, $msg.$exc->getMessage().PHP_EOL);
		fclose($fo);
	};*/
	if ( $msg =="")
		update_option( 'WPsCRM_db_version', $WPsCRM_db_version );

}

function WPsCRM_upgrade_taxonomies()
{
	global $wpdb;
    $table=WPsCRM_TABLE."clienti";
	$sql="select ID_clienti, categoria, provenienza from $table";
    foreach( $wpdb->get_results( $sql ) as $record)
	{
		$cat=$record->categoria;
		$pro=$record->provenienza;
		$id_cli=$record->ID_clienti;
		if ($cat!="0" && $cat!="")
		{
			$categorybyname = get_term_by('name', $cat, 'WPsCRM_customersCat');
			if ($categorybyname!=false)
			{
				$cat_id=$categorybyname->term_id;
			}
			else
			{
				$categorybyid = get_term_by('id', (int)$cat, 'WPsCRM_customersCat');
				if ($categorybyid!=false)
				{
					$cat_id=$categorybyid->term_id;
				}
				else
				{
					$ret=wp_insert_term($cat, 'WPsCRM_customersCat');
					$cat_id=$ret["term_id"];
				}
			}
			$wpdb->update(
				$table,
				array(
					'categoria'=>$cat_id
				),
				array(
					'ID_clienti'=>$id_cli
				),
				array(
				'%s'
				)
			);
		}
		if ($pro!="0" && $pro!="")
		{
			$originbyname = get_term_by('name', $pro, 'WPsCRM_customersProv');
			if ($originbyname!=false)
			{
				$pro_id=$originbyname->term_id;
			}
			else
			{
				$originbyid = get_term_by('id', (int)$pro, 'WPsCRM_customersProv');
				if ($originbyid!=false)
				{
					$pro_id=$originbyid->term_id;
				}
				else
				{
					$ret=wp_insert_term($pro, 'WPsCRM_customersProv');
					$pro_id=$ret["term_id"];
				}
			}
			$wpdb->update(
				$table,
				array(
					'provenienza'=>$pro_id
				),
				array(
					'ID_clienti'=>$id_cli
				),
				array(
				'%s'
				)
			);
			//echo $wpdb->last_query;
		}
	}
	update_option ("WPsCRM_upgrade_taxonomies", 1);
}


function WPsCRM_update_db_check() {
    global $WPsCRM_db_version;
    if ( get_option( 'WPsCRM_db_version' ) != $WPsCRM_db_version ) {
        WPsCRM_crm_install();
    }
    if ( get_option( 'WPsCRM_upgrade_taxonomies' ) == false ) {
        WPsCRM_upgrade_taxonomies();
    }
}
add_action( 'plugins_loaded', 'WPsCRM_update_db_check',13 );

///**
//Custom post type "services/products"
// * */
//function create_services() {

//    register_post_type( 'services',
//        array(
//            'labels' => array(
//                'name' => __( 'Services' ),
//                'singular_name' => __( 'Service' ),
//                'edit_item'         => __( 'Edit Service' ),
//                'add_new_item'      => __( 'Add Service' ),
//                'new_item_name'     => __( 'New Service' ),
//            ),
//        'public' => true,
//        'has_archive' => true,
//        'supports'=>array('thumbnail','author','title','editor','shortcode'),
//        'show_ui' => true,
//        'slug'=>'services',
//        'show_in_menu' => 'admin.php?page=smart-crm'
//        )
//    );
//}
//add_action( 'init', 'create_services' );
//function create_cat_services() {

//    $labels=array(
//    'name'              => _x( 'Category', 'taxonomy general name' ),
//    'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
//    'search_items'      => __( 'Search category' ),
//    'all_items'         => __( 'All Categories' ),
//    'parent_item'       => __( 'Categoria superiore' ),
//    'parent_item_colon' => __( 'Categoria superiore:' ),
//    'edit_item'         => __( 'Edit Category' ),
//    'update_item'       => __( 'Update Category' ),
//    'add_new_item'      => __( 'Add Category' ),
//    'new_item_name'     => __( 'New Category' ),
//    'menu_name'         => __( 'Categories' ),
//    );
//    $args = array(
//    'hierarchical'      => true,
//    'labels'            => $labels,
//    'show_ui'           => true,
//    'show_admin_column' => true,
//    'query_var'         => 'services_cat',
//    'rewrite'           => array( 'slug' =>'cat'),
//    'rewrite'           => false,
//);
//    register_taxonomy( 'services_cat', array(
//                                         'services',

//                                         ), $args );

//}
//add_action( 'init', 'create_cat_services' );



add_action( 'plugins_loaded', 'WPsCRM_create_clienti',11 );
function WPsCRM_create_clienti() {

    register_post_type( 'clienti',
        array(
            'labels' => array(
                'name' => __( 'Clienti','commonFunctions' ),
                'singular_name' => __( 'Cliente' ),
                'edit_item'         => __( 'Modifica cliente' ),
                'add_new_item'      => __( 'Aggiungi cliente' ),
                'new_item_name'     => __( 'Nuovo cliente' ),
            ),
        'public' => false,
        'has_archive' => false,
        'rewrite' => false,
        'supports'=>array('thumbnail','author','editor','title'),
        //'taxonomies' => array('post_tag','localita'),
        'show_ui' => false,
        'publicly_queryable'=>true,
        'capability_type' => 'post'
        )
    );
}
add_action( 'plugins_loaded', 'WPsCRM_customers_tax' ,12);
function WPsCRM_customers_tax() {

    $labels=array(
    'name'              => _x( 'Interests', 'taxonomy general name' ),
    'singular_name'     => _x( 'Interest', 'taxonomy singular name' ),
    'search_items'      => __( 'Search interest','cpsmartcrm' ),
    'all_items'         => __( 'All interests','cpsmartcrm' ),
    'edit_item'         => __( 'Edit interest','cpsmartcrm' ),
    'update_item'       => __( 'Update interest','cpsmartcrm' ),
    'add_new_item'      => __( 'Add interest','cpsmartcrm' ),
    'new_item_name'     => __( 'New interest','cpsmartcrm' ),
    'menu_name'         => __( 'Interests','cpsmartcrm' ),
    );
    $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'query_var'         => 'WPsCRM_customersInt',
     'rewrite'           => false,
	);
	register_taxonomy( 'WPsCRM_customersInt', array('clienti'), $args );

	$labels=array(
   'name'              => _x( 'Categories', 'taxonomy general name' ),
   'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
   'search_items'      => __( 'Search category','cpsmartcrm' ),
   'all_items'         => __( 'All categories','cpsmartcrm' ),
   'edit_item'         => __( 'Edit category','cpsmartcrm' ),
   'update_item'       => __( 'Update category','cpsmartcrm' ),
   'add_new_item'      => __( 'Add category','cpsmartcrm' ),
   'new_item_name'     => __( 'New category','cpsmartcrm' ),
   'menu_name'         => __( 'Categories','cpsmartcrm' ),
   );
    $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'query_var'         => 'WPsCRM_customersCat',
    'rewrite'           => false
	);
	register_taxonomy( 'WPsCRM_customersCat', array('clienti'), $args );

	$labels=array(
   'name'              => _x( 'Origins', 'taxonomy general name' ),
   'singular_name'     => _x( 'Origin', 'taxonomy singular name' ),
   'search_items'      => __( 'Search origin','cpsmartcrm' ),
   'all_items'         => __( 'All Origins','cpsmartcrm' ),
   'edit_item'         => __( 'Edit Origin','cpsmartcrm' ),
   'update_item'       => __( 'Update Origin','cpsmartcrm' ),
   'add_new_item'      => __( 'Add Origin','cpsmartcrm' ),
   'new_item_name'     => __( 'New Origin','cpsmartcrm' ),
   'menu_name'         => __( 'Origins','cpsmartcrm' ),
   );
    $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => false,
    'query_var'         => 'WPsCRM_customersProv',
    'rewrite'           => false
	);
	register_taxonomy( 'WPsCRM_customersProv', array('clienti'), $args );
}


/**
menu page
 * */
add_action('admin_menu', 'smart_crm_menu');
function smart_crm_menu(){


    add_menu_page( 'WP SMART CRM', 'CP Smart CRM', 'manage_crm', 'smart-crm', 'WPsCRM_smartcrm', 'dashicons-analytics', 71 );
    add_submenu_page('SMART CRM', 'CP Smart CRM', 'manage_crm', 'smart-crm', 'WPsCRM_smartcrm', 'dashicons-analytics', 71);

	add_submenu_page(
			'smart-crm',
			__('WP SMART CRM NOTIFICATION RULES', 'cpsmartcrm'),
			__('Notification rules', 'cpsmartcrm'),
			'manage_options',
			'smartcrm_subscription-rules',
			'smartcrm_subscription_rules'
			);
	add_submenu_page(
			'smart-crm',
			__('WP SMART CRM Customers', 'cpsmartcrm'),
			__('Customers', 'cpsmartcrm'),
			'manage_crm',
			'admin.php?page=smart-crm&p=clienti/list.php',
			''
			);
	add_submenu_page(
			'smart-crm',
			__('WP SMART CRM Scheduler', 'cpsmartcrm'),
			__('Scheduler', 'cpsmartcrm'),
			'manage_crm',
			'admin.php?page=smart-crm&p=scheduler/list.php',
			''
			);
	add_submenu_page(
			'smart-crm',
			__('WP SMART CRM Documents', 'cpsmartcrm'),
			__('Documents', 'cpsmartcrm'),
			'manage_crm',
			'admin.php?page=smart-crm&p=documenti/list.php',
			''
			);
	//function wpdocs_my_custom_submenu_page_callback() {
	//    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
	//    echo '<h2>My Custom Submenu Page</h2>';
	//    echo '</div>';
	//}
}
//$options=get_option('CRM_general_settings');
//if(isset($options['services']) && $options['services']==1)
//    add_action( 'admin_menu', 'add_CRM_services_menu' );
//function add_CRM_services_menu() {
//    add_submenu_page(
//            'smart-crm',
//            'SERVICES',
//            __('Services'),
//            'manage_options',
//            'edit.php?post_type=services',
//            ''
//            );
//}
add_action('wp_head', 'CRM_ajaxurl');
function CRM_ajaxurl() {

	echo '<script type="text/javascript">
             var ajaxurl = "' . admin_url('admin-ajax.php') . '";
</script>';
}
