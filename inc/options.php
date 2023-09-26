<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
$inc_dir  = dirname(__FILE__);

//OPTIONS API

class CRM_Options_Settings{
	
	/*
	 * For easier overriding we declared the keys
	 * here as well as our tabs array which is populated
	 * when registering settings
	 */
	private $business_settings_key = 'CRM_business_settings';
	private $general_settings_key = 'CRM_general_settings';
	private $clients_settings_key = 'CRM_clients_settings';
	private $documents_settings_key = 'CRM_documents_settings';
	private $services_settings_key = 'CRM_services_settings';
	private $woo_settings_key = 'CRM_woo_settings';
	private $acc_settings_key = 'CRM_acc_settings';
	private $adv_settings_key = 'CRM_adv_settings';
    private $ag_settings_key = 'CRM_ag_settings';
	private $plugin_options_key = 'smartcrm_settings';
	private $plugin_settings_tabs = array();
	
	/*
	 * Fired during plugins_loaded (very very early),
	 * so don't miss-use this, only actions and filters,
	 */
	function __construct() {

		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_business_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_clients_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_documents_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_services_settings' ) ); 
		add_action('admin_init', array( &$this, 'check_woo_addon' ),10 ); 
		add_action('admin_init', array( &$this, 'check_accountability_addon' ),10 );
		add_action('admin_init', array( &$this, 'check_advanced_addon' ),10 );
        add_action('admin_init', array( &$this, 'check_agents_addon' ),10 );
		add_action( 'admin_menu', array( &$this, 'WPsCRM_add_admin_menus' ) );
	}
	function check_woo_addon(){
		$wooPlugin='wp-smart-crm-woocommerce/wp-smart-crm-woocommerce.php' ;
		if (is_plugin_active( $wooPlugin ) ) 
			add_action( 'admin_init', array( &$this, 'register_woo_settings'),11 );
	}
	function check_accountability_addon(){
		$accPlugin='wp-smart-crm-accountability/wp-smart-crm-accountability.php' ;
		if (is_plugin_active( $accPlugin ) ) 
			add_action( 'admin_init', array( &$this, 'register_acc_settings'),11 );
	}
	function check_advanced_addon(){
		$advPlugin='wp-smart-crm-advanced/wp-smart-crm-advanced.php' ;
		if (is_plugin_active( $advPlugin ) ) 
			add_action( 'admin_init', array( &$this, 'register_adv_settings'),11 );
	}
    function check_agents_addon(){
		$advPlugin='wp-smart-crm-agents/wp-smart-crm-agents.php' ;
		if (is_plugin_active( $advPlugin ) ) 
			add_action( 'admin_init', array( &$this, 'register_ag_settings'),11 );
	}
	/*
	 * Loads both the general and advanced settings from
	 * the database into their respective arrays. Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 */
	function load_settings() {
		$this->business_settings = (array) get_option( $this->business_settings_key );            
		$this->general_settings = (array) get_option( $this->general_settings_key );
		$this->clients_settings = (array) get_option( $this->clients_settings_key );
		$this->documents_settings = (array) get_option( $this->documents_settings_key );        
		$this->services_settings = (array) get_option( $this->services_settings_key );
		$this->woo_settings = (array) get_option( $this->woo_settings_key );
		$this->acc_settings = (array) get_option( $this->acc_settings_key );	
		$this->adv_settings = (array) get_option( $this->adv_settings_key );  
        $this->ag_settings = (array) get_option( $this->ag_settings_key );
		// Merge with defaults
		$this->business_settings = array_merge( array(
		'CRM_business_option' => 'Business value'
		), $this->general_settings );
		$this->general_settings = array_merge( array(
			'CRM_general_option' => 'General value'
		), $this->general_settings );
		
		$this->clients_settings = array_merge( array(
			'CRM_clients_option' => 'Clients values'
		), $this->clients_settings );
		
		$this->documents_settings = array_merge( array(
			'CRM_documents_option' => 'Documents values'
		), $this->documents_settings );
		
		$this->services_settings = array_merge( array(
			'CRM_services_option' => 'Services values'
		), $this->services_settings ); 
		
		$this->woo_settings = array_merge( array(
			'CRM_woo_option' => 'Woocommerce values'
		), $this->woo_settings );

		$this->acc_settings = array_merge( array(
			'CRM_acc_option' => 'Accountability values'
		), $this->acc_settings );

		$this->adv_settings = array_merge( array(
			'CRM_adv_option' => 'Advanced values'
		), $this->adv_settings );

        $this->ag_settings = array_merge( array(
            'CRM_adv_option' => 'Agents values'
        ), $this->ag_settings );
	}
	function header(){
        is_multisite() ? $filter=get_blog_option(get_current_blog_id(), 'active_plugins' ) : $filter=get_option('active_plugins' );
        if ( in_array( 'wp-smart-crm-agents/wp-smart-crm-agents.php', apply_filters( 'active_plugins', $filter) ) ) {
            $agent_obj=new AGsCRM_agent();
            $privileges=$agent_obj->getAllPrivileges();
        }
        else 
            $privileges=null;
?>
        <div class="wrap">
            <h1 class="WPsCRM_plugin_title" style="text-align:center">CP Smart CRM<?php if(! isset($_GET['p'])){ ?><!--<span class="crmHelp" data-help="main"></span>--><?php } ?></h1>
		    <?php include(WPsCRM_DIR."/inc/crm/c_menu.php")?> 
        <?php
		echo '<h1>'.__('CP smart CRM Optionen und Einstellungen','cpsmartcrm').'</h1>';
	}
	function footer(){
		echo '<small style="text-align:center;top:30px;position:relative">Developed by Webmaster-Community N3rds@Work <a href="https://smart-cms.n3rds.work">https://smart-cms.n3rds.work</a></small></div>';
	}
	
	/*
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 */
	function register_business_settings() {
		$this->plugin_settings_tabs[$this->business_settings_key] =  __('Business' , 'cpsmartcrm');
		register_setting( $this->business_settings_key, $this->business_settings_key );
		add_settings_section( 'section_business', __( 'Business Einstellungen', 'cpsmartcrm'), array( &$this, 'section_business_desc' ), $this->business_settings_key );
		add_settings_field( 'business', __( '', 'cpsmartcrm'), array( &$this, 'smartcrm_business_info' ), $this->business_settings_key, 'section_business' );
	}
	
	function register_general_settings() {
		$this->plugin_settings_tabs[$this->general_settings_key] =  __('Allgemein' , 'cpsmartcrm');
		register_setting( $this->general_settings_key, $this->general_settings_key );
		add_settings_section( 'section_general', __('Allgemeine CRM-Einstellungen' , 'cpsmartcrm').'<span class="crmHelp crmHelp-dark _options" data-help="general-options"></span>', array( &$this, 'section_general_desc' ), $this->general_settings_key );
		add_settings_field( 'redirect', __( 'Weiterleitung zu CRM', 'cpsmartcrm'), array( &$this, 'smartcrm_redirect' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'minimize', __( 'CMS-Menü minimieren', 'cpsmartcrm'), array( &$this, 'smartcrm_minimize_WP_menu' ), $this->general_settings_key, 'section_general' );
		//add_settings_field('services', __( 'Activate Services Module', 'cpsmartcrm'), array( &$this, 'smartcrm_checkbox_services'),$this->general_settings_key,'section_general' );
		add_settings_field('company_logo', __( 'Firmenlogo', 'cpsmartcrm'),array( &$this, 'smartcrm_company_logo'), $this->general_settings_key, 'section_general' );
		add_settings_field('print_logo', __( 'Logo in Dokumenten (Rechnungen, Angebote) verwenden', 'cpsmartcrm'),array( &$this, 'smartcrm_print_logo'), $this->general_settings_key, 'section_general' );
		add_settings_field('show_all_for_administrators', __( 'Alle Benachrichtigungen für Administratoren anzeigen', 'cpsmartcrm'),array( &$this, 'smartcrm_administrator_noty'), $this->general_settings_key, 'section_general' );
		add_settings_field('future_activity', __( 'Geschlossene vergangene Aktivitäten nicht im Planer und Dashboard anzeigen', 'cpsmartcrm'),array( &$this, 'smartcrm_show_future_activity'), $this->general_settings_key, 'section_general' );
		add_settings_field('activity_deletion', __( 'Löschen von Aktivitäten (Aufgaben, Termine) zulassen', 'cpsmartcrm'),array( &$this, 'smartcrm_activity_deletion_privileges'), $this->general_settings_key, 'section_general' );
		add_settings_field( 'agent_can', __( 'Erweitere die Agentenfunktionen' , 'cpsmartcrm'), array( &$this, 'smartcrm_agent_can' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'emailfrom', __( 'E-Mail-Absender für Benachrichtigung festlegen', 'cpsmartcrm'), array( &$this, 'smartcrm_sender_email' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'emailfromLabel', __( 'Lege den Absendernamen für die Benachrichtigung fest' , 'cpsmartcrm'), array( &$this, 'smartcrm_sender_name' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'customersGridHeight', __( 'Rasterhöhe für Kunden festlegen' , 'cpsmartcrm'), array( &$this, 'smartcrm_customers_grid_height' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'documentsGridHeight', __( 'Rasterhöhe für Dokumente festlegen' , 'cpsmartcrm'), array( &$this, 'smartcrm_documents_grid_height' ), $this->general_settings_key, 'section_general' );

		do_action('WPsCRM_register_additional_general_options');
	}

	function register_clients_settings() {
		$this->plugin_settings_tabs[$this->clients_settings_key] = __('Kunden' , 'cpsmartcrm') ;
		register_setting( $this->clients_settings_key, $this->clients_settings_key );
		add_settings_section( 'section_clients', __( 'Kundeneinstellungen', 'cpsmartcrm') , array( &$this, 'section_clients_desc' ), $this->clients_settings_key );
		add_settings_field('clientsCategories',__( 'Kundenkategorien', 'cpsmartcrm').'<span class="crmHelp crmHelp-dark" data-help="customer-categories" style="margin:0"></span>', array( &$this, 'smartcrm_add_client_category'), $this->clients_settings_key, 'section_clients' );
		//add_settings_field('clientsTax',__( 'Show taxonomies in grid', 'cpsmartcrm'), array( &$this, 'smartcrm_tax_columns'), $this->clients_settings_key, 'section_clients' );
		do_action('WPsCRM_register_additional_clients_options');
	}

	function register_documents_settings() {
		$this->plugin_settings_tabs[$this->documents_settings_key] =  __( 'Dokumente', 'cpsmartcrm');
		register_setting( $this->documents_settings_key, $this->documents_settings_key );
		add_settings_section( 'section_documents', __( 'Dokumenteinstellungen', 'cpsmartcrm'), array( &$this, 'section_documents_desc' ), $this->documents_settings_key );
		//add_settings_field( 'delayedPayments', __( '', 'cpsmartcrm'),  array( &$this,'smartcrm_add_payment_description'), $this->documents_settings_key, 'section_documents' );
		add_settings_field( 'document_header', __( '', 'cpsmartcrm'),  array( &$this,'smart_crm_documents_settings'), $this->documents_settings_key, 'section_documents' );
		
	}
	
	function register_services_settings() { //conditional if services module is activated in general options
		$options = get_option( $this->general_settings_key );
		if( isset($options['services']) && $options['services'] ==1){
			$this->plugin_settings_tabs[$this->services_settings_key] =  __( 'Services', 'cpsmartcrm');
			
			register_setting( $this->services_settings_key, $this->services_settings_key );
			/**
			 * Section services removed until next release
			 **/
			//add_settings_section( 'section_services', __( 'Services Settings', 'cpsmartcrm'), array( &$this, 'section_services_desc' ), $this->services_settings_key );
			add_settings_field('currency', __( 'Währung', 'cpsmartcrm'), array( &$this,'smartcrm_currency_select' ),$this->services_settings_key,'section_services' );
			add_settings_field('gateways',__( 'Zahlungsgateways', 'cpsmartcrm'), array( &$this,'smartcrm_gateway_select' ),$this->services_settings_key,'section_services' );
		}
	}
	function register_woo_settings(){
		$this->plugin_settings_tabs[$this->woo_settings_key] =  __( 'Woocommerce Einstellungen', 'cpsmartcrm');
		register_setting( $this->woo_settings_key, $this->woo_settings_key );
		add_settings_section( 'section_woocommerce', __( 'Woocommerce Einstellungen', 'cpsmartcrm'), array( &$this, 'section_woo_desc' ), $this->woo_settings_key );
		do_action('WPsCRM_add_woo_settings_fields');
	}
	//ToDo PSeCommerce_Settings
	function register_acc_settings(){
		$this->plugin_settings_tabs[$this->acc_settings_key] =  __( 'Verantwortlichkeitseinstellungen', 'cpsmartcrm');
		register_setting( $this->acc_settings_key, $this->acc_settings_key );
		add_settings_section( 'section_accountability', __( 'Verantwortlichkeitseinstellungen', 'cpsmartcrm'), array( &$this, 'section_acc_desc' ), $this->acc_settings_key );
		do_action('WPsCRM_add_acc_settings_fields');
	}
	function register_adv_settings(){
		$this->plugin_settings_tabs[$this->adv_settings_key] =  __( 'Erweiterte Einstellungen', 'cpsmartcrm');
		register_setting( $this->adv_settings_key, $this->adv_settings_key );
		add_settings_section( 'section_advanced', __( 'Erweiterte Einstellungen', 'cpsmartcrm'), array( &$this, 'section_adv_desc' ), $this->adv_settings_key );
		do_action('WPsCRM_add_adv_settings_fields');
	}
    function register_ag_settings(){
		$this->plugin_settings_tabs[$this->ag_settings_key] =  __( 'Agenteneinstellungen', 'cpsmartcrm');
		register_setting( $this->ag_settings_key, $this->ag_settings_key );
		add_settings_section( 'section_agents', __( 'Agenteneinstellungen', 'cpsmartcrm'), array( &$this, 'section_ag_desc' ), $this->ag_settings_key );
		do_action('WPsCRM_add_ag_settings_fields');
	}
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */
	function section_business_desc() { echo ''; }
	function section_general_desc() { echo ''; }
	function section_documents_desc() { echo ''; }
	function section_services_desc() { echo ''; }
	function section_clients_desc() { echo ''; }    
	function section_woo_desc() { echo ''; }
	function section_acc_desc() { echo ''; }
	function section_adv_desc() { echo ''; }
	function section_ag_desc() { echo ''; }
	/**
	 * @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	 * 
	 * inputs initializations.
	 */
	
	
    /**
	 * Summary of smartcrm_business_info
	 * generates a formatted set of fields for document headers
	 */
    
	function smartcrm_business_info(){
		$options=get_option($this->business_settings_key);
        ?>
        <div id="pages" class="col-md-12">
            <div id="pages-title"><h4 class="page-header" style="text-align:center"><span class="crmHelp crmHelp-dark" data-help="business-data" data-role="tooltip"></span><?php _e('Business main data', 'wp-smart-crm-invoices-pro') ?><small style="font-size:small"> - <?php _e('Mit diesen Informationen wird bei der Plugin-Aktivierung Kontakt Nr. 1 (zur Selbsterledigung) erstellt und in Dokumenten (Rechnungen, Kostenvoranschläge usw.) verwendet.', 'wp-smart-crm-invoices-pro') ?></small></h4></div>
            <div id="sortable-handlers">
                <div class="item xml_mandatory">
                    <label><?php _e('Firmenname', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />
                    <input type="text" id="crm_business_name" name="CRM_business_settings[business_name]" value="<?php echo isset( $options['business_name']) ? $options['business_name'] : "" ?>"  class="form-control _m" />

                </div>
                <?php do_action("business_extra_field"); ?>
                <div class="item xml_mandatory">
                    <label><?php _e('Adresse (Straße)', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />

                    <input type="text" id="crm_business_address" name="CRM_business_settings[business_address]"  value="<?php echo isset($options['business_address']) ? $options['business_address'] : "" ?>"  class="form-control _m"/>
                </div>
              <div class="item">
                <label><?php _e('Adresse (Nummer)', 'wp-smart-crm-invoices-pro') ?> </label><br />

                <input type="text" id="crm_business_number" name="CRM_business_settings[business_number]" value="<?php echo isset($options['business_number']) ? $options['business_number'] : "" ?>" class="form-control _m" />
              </div>
                <div class="item xml_mandatory">
                    <label><?php _e('Stadt', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />
                    <input type="text" id="crm_business_town" name="CRM_business_settings[business_town]"  value="<?php echo isset($options['business_town']) ? $options['business_town'] : "" ?>"  class="form-control _m"/>
                </div>
                <div class="item xml_mandatory">
                    <label><?php _e('PLZ', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />
                    <input type="text" id="crm_business_zip" name="CRM_business_settings[business_zip]"  value="<?php echo isset($options['business_zip']) ? $options['business_zip'] : "" ?>"  class="form-control _m"/>
                </div>
              <div class="item xml_mandatory">
                <label><?php _e('Staat/Provinz', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />
                <input type="text" id="crm_business_provincia" name="CRM_business_settings[crm_business_provincia]" value="<?php echo isset($options['crm_business_provincia']) ? $options['crm_business_provincia'] : "" ?>" class="form-control _m" />
              </div>
                <div class="item xml_mandatory">
                    <label><?php _e('Land', 'wp-smart-crm-invoices-pro') ?> ( <span style="color:red"> * </span> )</label><br />
                    <select data-nazione="<?php if (isset($options['business_country'])) echo $options['business_country'] ?>" id="nazione" name="CRM_business_settings[business_country]" size="20" maxlength='50'><?php
                        if (isset($options['business_country']))
                          echo stripslashes(WPsCRM_get_countries($options['business_country']));
                        else
                          echo stripslashes(WPsCRM_get_countries('0'))
                          ?></select>                         
                </div>
                <div class="item xml_mandatory">
                    <label><?php _e('MwSt.-Code', 'wp-smart-crm-invoices-pro') ?>  ( <span style="color:red"> * </span> )</label><br />
                    <input type="text" id="crm_business_iva" name="CRM_business_settings[business_iva]" value="<?php echo isset($options['business_iva'] ) ? $options['business_iva']: "" ?>"  class="form-control _m"/>
                </div>
                <div class="item">
                    <label><?php _e('Cod. Fisc.', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_cf" name="CRM_business_settings[business_cf]" value="<?php echo isset( $options['business_cf'] ) ?  $options['business_cf'] :""?>"  class="form-control _m"/>
                </div>
                <div class="item">
                    <label><?php _e('Telefon', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_phone" name="CRM_business_settings[business_phone]" value="<?php echo isset($options['business_phone']) ? $options['business_phone'] :"" ?>"  class="form-control _m" /><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_phone]" <?php echo (isset($options['show_phone']) && $options['show_phone'] == "1" ? 'checked' : null) ?>/></label><br />
                </div>
                <div class="item">
                    <label><?php _e('Fax', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_fax" name="CRM_business_settings[business_fax]" value="<?php echo isset($options['business_fax']) ? $options['business_fax'] :"" ?>"  class="form-control _m" /><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_fax]" <?php echo (isset($options['show_fax']) && $options['show_fax'] == "1" ? 'checked' : null) ?>/></label><br />
                </div>
                <div class="item">
                    <label><?php _e('Email', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_email" name="CRM_business_settings[business_email]" value="<?php echo isset($options['business_email'] ) ? $options['business_email'] : "" ?>"  class="form-control _m" /><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_email]" <?php echo (isset($options['show_email']) && $options['show_email'] == "1" ? 'checked' : null) ?>/></label><br />
                </div>
                <div class="item">
                    <label><?php _e('Webseite', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_web" name="CRM_business_settings[business_web]" value="<?php echo isset( $options['business_web']) ? $options['business_web'] : "" ?>"  class="form-control _m"><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_web]" <?php echo (isset($options['show_web']) && $options['show_web'] == "1" ? 'checked' : null) ?>/></label><br />
                </div>
                <div class="item">
                    <label><?php _e('Bankkontonummer (IBAN)', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_iban" name="CRM_business_settings[business_iban]"  value="<?php echo isset( $options['business_iban']) ? $options['business_iban'] : "" ?>"  class="form-control _m"/><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_iban]" <?php echo (isset($options['show_iban']) && $options['show_iban'] == "1" ? 'checked' : null) ?>/></label><br />
                </div>
                <div class="item">
                    <label><?php _e('Int. Kontocode (SWIFT)', 'wp-smart-crm-invoices-pro') ?></label><br />
                    <input type="text" id="crm_business_swift" name="CRM_business_settings[business_swift]" value="<?php echo isset( $options['business_swift']) ? $options['business_swift'] : "" ?>" class="form-control _m" /><label class="toRight"><?php _e('Im Dokumentkopf anzeigen', 'wp-smart-crm-invoices-pro') ?>?<input type="checkbox" value="1" name="CRM_business_settings[show_swift]" <?php echo (isset($options['show_swift']) && $options['show_swift'] == "1" ? 'checked' : null) ?> /></label><br />
                </div>
                <input type="hidden" id="CRM_required_settings" name="CRM_business_settings[CRM_required_settings]" value="<?php echo isset( $options['CRM_required_settings']) ? $options['CRM_required_settings'] : "" ?>" />

                <span  class="_flat btn btn-success" value="Save" style="margin: 30px;" onclick="saveBusiness()"><?php _e('Save', 'wp-smart-crm-invoices-pro') ?></span> 
            </div>
			<style>
				#sortable-handlers label:not(.toRight){float:left;line-height:2em}
				#sortable-handlers input[type=text]{width:50%;}
			</style>
			<script>
                jQuery('#nazione').kendoDropDownList({
    	            placeholder: "<?php _e('Land auswählen','cpsmartcrm') ?>...",
                    value: "<?php if(isset($options['business_country'])) echo $options['business_country']; else echo 'IT'?>"
                });
				function saveBusiness(e) {
				var validator = jQuery("form").kendoValidator({
				rules: {
					hasName: function (input) {
						if (input.is("[id=crm_business_name]")) {
							var kb = jQuery("#crm_business_name").val();
							if (kb == "") {
								jQuery("#crm_business_name").focus();
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}

						return true;
					},
					hasAddress: function (input) {
						if (input.is("[id=crm_business_address]")) {
							var kb = jQuery("#crm_business_address").val();
							if (kb == "") {
								jQuery("#crm_business_address").focus();
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}
						return true;
					},
					hasTown: function (input) {
						if (input.is("[id=crm_business_town]")) {
							var ms = jQuery('#crm_business_town').val();
							if (ms == "") {
								jQuery("#crm_business_town").focus();
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}
						return true;

					},
					hasZip: function (input) {
						if (input.is("[id=crm_business_zip]")) {
							var ms = jQuery('#crm_business_zip').val();
							if (ms == "") {
								jQuery("#crm_business_zip").focus();
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}
						return true;

					},
					hasCF: function (input) {
						if (input.is("[id=crm_business_iva]")) {
							var ms = jQuery('#crm_business_iva').val();
							if (ms == "") {
								jQuery("#crm_business_iva").focus();
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}
						return true;

					},
                                        hasMail: function (input) {
						if (input.is("[id=crm_business_email]")) {
							var kb = jQuery("#crm_business_email").val();
							if (kb == "") {
								jQuery("#crm_business_email").focus();
								jQuery.playSound("<?php echo WPsCRM_URL?>inc/audio/double-alert-2")
								jQuery('#CRM_required_settings').val(0)
								return false;
							}
						}

						return true;
					}

				},

				messages: {
					hasName: "<?php _e('Firmenname ist erforderlich','cpsmartcrm')?>",
					hasAddress: "<?php _e('Adresse ist erforderlich','cpsmartcrm')?>",
					hasTown: "<?php _e('Stadt ist erforderlich','cpsmartcrm')?>",
					hasZip: "<?php _e('Postleitzahl ist erforderlich','cpsmartcrm')?>",
					hasCF: "<?php _e('Umsatzsteuer-Code ist erforderlich','cpsmartcrm')?>",
                                        hasMail: "<?php _e('E-Mail ist erforderlich','cpsmartcrm')?>",

				}
				}).data("kendoValidator");
			if (validator.validate()) {
				jQuery('#CRM_required_settings').val(1)
				jQuery('form').find(':submit').click();
			}

		}
</script>
		<?php
		$options=get_option($this->business_settings_key);
		if(isset($_GET['noty'] ) && $_GET['noty']=="settings_required" && $options['CRM_required_settings'] !=1){
        ?>
			<div class="col-md-12">
				
			</div>

			<script>
            	jQuery(document).ready(function ($) {
            		noty({
            			text: "<?php _e('ES SIND EINIGE GRUNDLEGENDE INFORMATIONEN ERFORDERLICH, UM FORTFAHREN ZU KÖNNEN','cpsmartcrm')?>",
            			layout: 'center',
            			type: 'error',
            			template: '<div class="noty_message"><span class="noty_text"></span><span class="noty_close glyphicons gypicons-close"></span></div>',
            			closeWith: ['button'],
            			//timeout: 1500
            		});
            	});
			</script>
			<?php
		}
	}
	/**
	 * Summary of smartcrm_checkbox_services
	 * activates services module of smart CRM
	 */
	function smartcrm_checkbox_services() {

		$options = get_option( $this->general_settings_key );
		
		$html = '<input type="checkbox" id="services" name="'.$this->general_settings_key.'[services]" value="1"' . checked( 1, $options['services'], false ) . ' class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Activate services module','cpsmartcrm').'</label>';
		
		echo $html;

	} 
	
	/**
	 * 
	 * CHANGES STYLE OF UI to be implemented in next versions
	 * 
	 **/   
	
	function smartcrm_select_style() {

		$options = get_option(  $this->general_settings_key );
		
		$html = '<div class="col-md-4"><select id="grid_style" name="'.$this->general_settings_key.'[grid_style]" class="form-control">';
		$html .= '<option value="default">' . __( 'Wähle einen Stil...', 'cpsmartcrm') . '</option>';
		$html .= '<option value="dark" ' . selected( $options['grid_style'], 'dark', false) . '>' . __( 'Dunkel', 'cpsmartcrm') . '</option>';
		$html .= '<option value="light" ' . selected( $options['grid_style'], 'light', false) . '>' . __( 'Hell', 'cpsmartcrm') . '</option>';
		$html .= '</select></div>';
		echo $html;

	} 
    /**
	 * 
	 * Optionally redirect to CRM dashboard on login
	 * 
	 **/          
	function smartcrm_redirect(){
		$options = get_option( $this->general_settings_key );
		global $current_user;
		$userID = $current_user->ID;
		if (isset($options['smartcrm_redirect-'.$userID]))
			$html = '<input type="checkbox" id="redirect_to_crm" name="'.$this->general_settings_key.'[smartcrm_redirect-'.$userID.']" value="1"' . checked( 1, $options['smartcrm_redirect-'.$userID.''], false ) . ' class="form-control"/>';
		else
			$html = '<input type="checkbox" id="redirect_to_crm" name="'.$this->general_settings_key.'[smartcrm_redirect-'.$userID.']" value="1" class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Bei der Anmeldung zum CRM-Dashboard weiterleiten','cpsmartcrm').'</label>';
		
		echo $html;
		
	}
	
	/**
	 * 
	 * Optionally minimize WP main menu to use crm fullpage
	 * 
	 **/          
	function smartcrm_minimize_WP_menu(){
		$options = get_option( $this->general_settings_key );
		global $current_user;
		$userID = $current_user->ID;
		if (isset($options['minimize_WP_menu-'.$userID]))
			$html = '<input type="checkbox" id="minimize_Wp_menu" name="'.$this->general_settings_key.'[minimize_WP_menu-'.$userID.']" value="1"' . checked( 1, $options['minimize_WP_menu-'.$userID.''], false ) . ' class="form-control"/>';
		else
			$html = '<input type="checkbox" id="minimize_Wp_menu" name="'.$this->general_settings_key.'[minimize_WP_menu-'.$userID.']" value="1" class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Minimiere das CMS-Hauptmenü und verwende CRM ganzseitig (empfohlen)','cpsmartcrm').'</label>';
		
		echo $html;
		
	}

	/**
	 * 
	 * Optionally show notification for all users to site administrators
	 * 
	 **/    
	function smartcrm_administrator_noty(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['administrator_all']))
			$html = '<input type="checkbox" id="administrator_all" name="'.$this->general_settings_key.'[administrator_all]" value="1"' . checked( 1, $options['administrator_all'], false ) . ' class="form-control"/>';
		else
			$html = '<input type="checkbox" id="administrator_all" name="'.$this->general_settings_key.'[administrator_all]" value="1" class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Zeige Administratoren Benachrichtigungen für alle Agenten/Benutzer an','cpsmartcrm').'</label>';
		
		echo $html;

	}

	/**
	 * 
	 * Optionally show only today and future notification in scheduler and dashboard
	 * 
	 **/   
	function smartcrm_show_future_activity(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['future_activities']))
			$html = '<input type="checkbox" id="future_activities" name="'.$this->general_settings_key.'[future_activities]" value="1"' . checked( 1, $options['future_activities'], false ) . ' class="form-control"/>';
		else
			$html = '<input type="checkbox" id="future_activities" name="'.$this->general_settings_key.'[future_activities]" value="1" class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Zeige im Dashboard und im Planer keine Aktivitäten an, die als FERTIG oder ABGESAGT markiert sind und älter als einen Tag sind','cpsmartcrm').'</label>';
		
		echo $html;

	}

	/**
	 * 
	 * Optionally allow deletion of activities to admin and uts creator
	 * 
	 **/  
	function smartcrm_activity_deletion_privileges(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['deletion_privileges']))
		{
			$html = '<label>'.__('Erlaube das Löschen von Aktivitäten nur Administratoren','cpsmartcrm');
			$html .= '<input type="radio" id="deletion_privileges" name="'.$this->general_settings_key.'[deletion_privileges]" value="1"' . checked( 1, $options['deletion_privileges'], false ) . ' /></label>';
			$html .= '<br>';
			$html .= '<label>'.__('Erlaube Administratoren und Erstellern das Löschen von Aktivitäten','cpsmartcrm');
			$html .= '<input type="radio" id="deletion_privileges" name="'.$this->general_settings_key.'[deletion_privileges]" value="0"' . checked( 0, $options['deletion_privileges'], false ) . ' /></label>';
		}
		else
		{
			$html = '<label>'.__('Erlaube das Löschen von Aktivitäten nur Administratoren','cpsmartcrm');
			$html .= '<input type="radio" id="deletion_privileges" name="'.$this->general_settings_key.'[deletion_privileges]" value="1" /></label>';
			$html .= '<br>';
			$html .= '<label>'.__('Erlaube Administratoren und Erstellern das Löschen von Aktivitäten','cpsmartcrm');
			$html .= '<input type="radio" id="deletion_privileges" name="'.$this->general_settings_key.'[deletion_privileges]" value="0" /></label>';
		}
		echo $html;
	}


	/**
	 * 
	 * Optionally allow agents to see documents and customers belonging to all  ( default no )
	 * 
	 **/  
	function smartcrm_agent_can(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['crm_agent_can']))
		{
			$html = '<label>'.__('Ermögliche Agenten, Dokumente und Kunden anzuzeigen, die anderen CRM-Agenten zugeordnet sind','cpsmartcrm');
			$html .= '<input type="radio" id="crm_agent_can" name="'.$this->general_settings_key.'[crm_agent_can]" value="1"' . checked( 1, $options['crm_agent_can'], false ) . ' /></label>';
			$html .= '<br>';
			$html .= '<label>'.__('Erlaube einem Agenten, nur Dokumente und Kunden zu sehen, die ihm zugeordnet sind','cpsmartcrm');
			$html .= '<input type="radio" id="crm_agent_can" name="'.$this->general_settings_key.'[crm_agent_can]" value="0"' . checked( 0, $options['crm_agent_can'], false ) . ' /></label>';
		}
		else
		{
			$html = '<label>'.__('Ermögliche Agenten, Dokumente und Kunden anzuzeigen, die anderen CRM-Agenten zugeordnet sind','cpsmartcrm');
			$html .= '<input type="radio" id="crm_agent_can" name="'.$this->general_settings_key.'[crm_agent_can]" value="1" /></label>';
			$html .= '<br>';
			$html .= '<label>'.__('Erlaube einem Agenten, nur Dokumente und Kunden zu sehen, die ihm zugeordnet sind','cpsmartcrm');
			$html .= '<input type="radio" id="crm_agent_can" name="'.$this->general_settings_key.'[crm_agent_can]" value="0" /></label>';
		}
		echo $html;
		return;
	}
	/**
	 * Summary of smartcrm_company_logo
	 * Select your Company Logo to be used in documents ( invoices, offers etc..)
	 */
	function smartcrm_company_logo(){
		$options = get_option( $this->general_settings_key );
            ?>
		<div class="row">

			<div class="uploader col-md-4">
				<input id="companyLogo" name="<?php echo $this->general_settings_key ?>[company_logo]" type="text" value="<?php echo isset($options['company_logo'])?$options['company_logo']:'' ?>"  class="form-control _m"/>
				<input style="margin-top:10px;text-align:center" class="button button-primary" value="<?php _e('Upload', 'cpsmartcrm')?>" onClick="open_media_uploader_images()"/>
			</div>
			<span style="width:100%;float:left;margin-top:10px;color:#999"><?php _e('Wähle Dein Firmenlogo aus, das in Dokumenten (Rechnungen, Kostenvoranschlägen usw.) verwendet werden soll. Beste Ergebnisse erhätst Du mit einem quadratischen Bild von 100 x 100 Pixel','cpsmartcrm')?></span>
			</div>
    <span class="thumbContainer row"><?php if(isset($options['company_logo'])) {?> <img src="<?php echo $options['company_logo'] ?>" /><?php } ?></span>
    <script>

        var media_uploader = null;

        function open_media_uploader_images() {
            media_uploader = wp.media({
                frame: "post",
                state: "insert",
                multiple: false
            });

            media_uploader.on("insert", function () {

                var length = media_uploader.state().get("selection").length;
                var images = media_uploader.state().get("selection").models
                console.log(images);

                for (var iii = 0; iii < length; iii++) {
                    var image_url = images[iii].changed.url;
                    console.log(image_url);
                    jQuery('.thumbContainer').html('<img src="' + image_url.replace(".jpg", ".jpg") + '">');
                    jQuery('#companyLogo').val(image_url)
                    var image_caption = images[iii].changed.caption;
                    var image_title = images[iii].changed.title;
                }
            });

            media_uploader.open();
        }

    </script>
<?php }  
	
    function smartcrm_print_logo(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['print_logo']))
			$html = '<input type="checkbox" id="print_logo" name="'.$this->general_settings_key.'[print_logo]" value="1"' . checked( 1, $options['print_logo'], false ) . ' class="form-control"/>';
		else
			$html = '<input type="checkbox" id="print_logo" name="'.$this->general_settings_key.'[print_logo]" value="1" class="form-control"/>';
		$html .= '&nbsp;';
		$html .= '<label>'.__('Logo in Dokumenten verwenden','cpsmartcrm').'</label>';
		
		echo $html;

	}

	/**
	 * Summary of smartcrm_sender_email
	 * set an email address as sender of crm notifications
	 */
	function smartcrm_sender_email(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['emailFrom']))
			$html = '<div class="col-md-4"><input type="email" id="emailFrom" name="'.$this->general_settings_key.'[emailFrom]" value="'. $options['emailFrom'] . '" class=" form-control _m"/>';
		else
			$html = '<div class="col-md-4"><input type="email" id="emailFrom" name="'.$this->general_settings_key.'[emailFrom]" value="" class=" form-control _m"/>';
		$html .= '&nbsp;';
		$html .= '<label style="line-height:1em">'.__('Lege eine Absender-E-Mail-Adresse für CRM-Benachrichtigungs-E-Mails fest','cpsmartcrm').' <small><br />'.__('Wenn leer, wird die Administrator-E-Mail verwendet','cpsmartcrm').'</small></label></div>';
		
		echo $html;
	}

	/**
	 * Summary of smartcrm_sender_name
	 * set a Name as sender of crm notifications
	 */
	function smartcrm_sender_name(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['nameFrom']))
			$html = '<div class="col-md-4"><input type="text" id="nameFrom" name="'.$this->general_settings_key.'[nameFrom]" value="'. $options['nameFrom'] . '" class=" form-control _m"/>';
		else
			$html = '<div class="col-md-4"><input type="text" id="nameFrom" name="'.$this->general_settings_key.'[nameFrom]" value="" class=" form-control _m"/>';
		$html .= '&nbsp;';
		$html .= '<label style="line-height:1em">'.__('Lege einen Absendernamen für CRM-Benachrichtigungs-E-Mails fest','cpsmartcrm').' <small><br />'.__('Wenn leer, wird der Webseiten-Name verwendet','cpsmartcrm').'</small></label></div>';
		
		echo $html;
	}

	/**
	 * set height of grids for customers Default 600px
	 */
	function smartcrm_customers_grid_height(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['customersGridHeight']))
			$html = '<div class="col-md-4"><input type="number" id="customersGridHeight" name="'.$this->general_settings_key.'[customersGridHeight]" value="'. $options['customersGridHeight'] . '" class=" form-control _m" style="width:200px"/>';
		else
			$html = '<div class="col-md-4"><input type="number" id="customersGridHeight" name="'.$this->general_settings_key.'[customersGridHeight]" value="" class=" form-control _m" style="width:200px"/>';
		$html .= '&nbsp;';
		$html .= '<label style="line-height:1em">'.__('Lege die Rasterhöhe (in px) für Kunden fest','cpsmartcrm').'</label></div>';
		
		echo $html;

	}
		/**
	 * set height of grids for customers  Default 600px
	 */
	function smartcrm_documents_grid_height(){
		$options = get_option( $this->general_settings_key );
		if (isset($options['documentsGridHeight']))
			$html = '<div class="col-md-4"><input type="number" id="documentsGridHeight" name="'.$this->general_settings_key.'[documentsGridHeight]" value="'. $options['documentsGridHeight'] . '" class=" form-control _m" style="width:200px"/>';
		else
			$html = '<div class="col-md-4"><input type="number" id="documentsGridHeight" name="'.$this->general_settings_key.'[documentsGridHeight]" value="" class=" form-control _m" style="width:200px"/>';
		$html .= '&nbsp;';
		$html .= '<label style="line-height:1em">'.__('Lege die Rasterhöhe (in px) für Dokumente fest','cpsmartcrm').'</label></div>';
		
		echo $html;

	}
	/**
	 * select currency in services selling not in use
	 */
	function smartcrm_currency_select() {

		$options= get_option( $this->services_settings_key );
		$html= '<div class="row"><div class="col-md-3">';
		$html .= '<select id="currency" name="'.$this->services_settings_key .'[currency]" class="form-control"/>';
		$html .= '<option value="EUR"' . selected( $options['currency'], 'EUR', false) . '>' . __( 'EUR', 'cpsmartcrm') . '</option>';
		$html .= '<option value="USD"' . selected( $options['currency'], 'USD', false) . '>' . __( 'USD', 'cpsmartcrm') . '</option>';
		$html .= '<option value="CHF"' . selected( $options['currency'], 'CHF', false) . '>' . __( 'CHF', 'cpsmartcrm') . '</option>';
		$html .= '<option value="BRL"' . selected( $options['currency'], 'BRL', false) . '>' . __( 'BRL', 'cpsmartcrm') . '</option>';
		$html .= '</select></div></div>';
		
		echo $html;
	} 

	/**
	 * Summary of smartcrm_gateway_select
	 * SELECT THE payment gateways
	 */
	function smartcrm_gateway_select() {

		$options= get_option( $this->services_settings_key );
		$html= '<div class="row"><div class="col-md-3">';
		$html .= '<select id="gateways" name="'.$this->services_settings_key .'[gateways]" class="form-control"/>';
		$html .= '<option value="">'. __('Wählen','cpsmartcrm').'</option>';
		$html .= '<option value="STRIPE"' . selected( $options['gateways'], 'STRIPE', false) . '>STRIPE</option>';
		$html .= '<option value="PAYPAL"' . selected( $options['gateways'], 'PAYPAL', false) . '>PAYPAL</option>';
		$html .= '</select></div></div>';
		
		echo $html;
?>
<div class="row">
    <div class="panel panel-default col-md-9" style="margin:20px;padding-bottom:20px">
        <div id="stripe_config" style="display:none">
                <h3><?php _e('Stripe-Konfiguration','cpsmartcrm')?></h3>
                <p><?php _e('Verwende hier Deine Stripe-Kontoeinstellungen','cpsmartcrm')?></p>
            
                <h4>Test mode &raquo;<input type="radio" name="<?php echo $this->services_settings_key; ?>[stripe_mode]" id="test_mode" value="test_mode" <?php echo checked( $options['stripe_mode'], 'test_mode', false) ?>/></h4>
                <label>Secret key for <span style="color:red">test mode</span></label><input class="test_mode" type="text" style="width:300px" name="<?php echo $this->services_settings_key; ?>[stripe_test_secret_key]" value="<?php echo esc_attr( $this->services_settings['stripe_test_secret_key'] ); ?>" />
                <label>Publishable key for <span style="color:red">test mode</span></label><input class="test_mode" type="text" style="width:300px" name="<?php echo $this->services_settings_key; ?>[stripe_test_publishable_key]" value="<?php echo esc_attr( $this->services_settings['stripe_test_publishable_key'] ); ?>" />
            
                <h4>Live mode &raquo;<input type="radio" name="<?php echo $this->services_settings_key; ?>[stripe_mode]" id="live_mode" value="live_mode" <?php echo checked( $options['stripe_mode'], 'live_mode', false) ?>/></h4>
                <label>Secret key for <span style="color:green">live mode</span></label><input class="live_mode" type="text" style="width:300px" name="<?php echo $this->services_settings_key; ?>[stripe_live_secret_key]" value="<?php echo esc_attr( $this->services_settings['stripe_live_secret_key'] ); ?>" />
                <label>Publishable key for <span style="color:green">live mode</span></label><input class="live_mode" type="text" style="width:300px" name="<?php echo $this->services_settings_key; ?>[stripe_live_publishable_key]" value="<?php echo esc_attr( $this->services_settings['stripe_live_publishable_key'] ); ?>" />
            </div>
            <div id="paypal_config" style="display:none">
                <h3>Paypal Configuration</h3>
            </div>
        </div> 
</div>
<script>
    jQuery(document).ready(function ($) {
        if ($('#gateways').val() == 'STRIPE') {
            $('#stripe_config').show();
            $('#paypal_config').hide();
        }
        else if ($('#gateways').val() == 'PAYPAL') {
            $('#stripe_config').hide();
            $('#paypal_config').show();
        }

        $('#gateways').on('change', function () {
            if ($(this).val() == 'STRIPE') {
                $('#stripe_config').show();
                $('#paypal_config').hide();
            }
            else {
                $('#stripe_config').hide();
                $('#paypal_config').show();
            }

        })

        if ($('#test_mode').attr('checked') == "checked") {

            $('.live_mode').attr('readonly', 'readonly');
            $('.test_mode').attr('readonly', false);
        }
        if ($('#live_mode').attr('checked') == "checked") {
            $('.test_mode').attr('readonly', 'readonly');
            $('.live_mode').attr('readonly', false);
        }

       

        $('input[name="<?php echo $this->services_settings_key; ?>[stripe_mode]"]').on('click', function () {
            $('.' + $('input[name="<?php echo $this->services_settings_key; ?>[stripe_mode]"]:checked').val()).attr('readonly', false);
            $('input[type="text"]:not(.' + $('input[name="<?php echo $this->services_settings_key; ?>[stripe_mode]"]:checked').val() + ')').attr('readonly', 'readonly')
        })
        $('#submit').click(function (e) {
            if ($('input[name="<?php echo $this->services_settings_key; ?>[stripe_mode]"]:checked').val() == "live_mode" && $('.live_mode').val() == "") {
                e.preventDefault();
                alert('Warning: live values missing');
            }
            if ($('input[name="<?php echo $this->services_settings_key; ?>[stripe_mode]"]:checked').val() == "test_mode" && $('.test_mode').val() == "") {
                e.preventDefault();
                alert('Warning: test values missing');
            }

        })
    })
</script>
    <?php
	} 

	/**
	 * Summary of smartcrm_add_client_category
	 * set labels for clients categories
	 */
    function smartcrm_add_client_category() {
		$options= get_option( $this->clients_settings_key );
		$catOptions=isset($options['clientsCategories']) ? $options['clientsCategories'] : null;
        $showCategories= isset($options['gridShowCat']) ? $options['gridShowCat'] : null;
        $showInterests= isset($options['gridShowInt']) ? $options['gridShowInt'] : null;
        $showOrigins= isset($options['gridShowOr']) ? $options['gridShowOr'] : null;
    ?>
	<div class="row" style="border-bottom:1px solid #000;padding-bottom:20px;margin-bottom:10px">
		<!--<div class="col-md-10"><h4><span class="crmHelp crmHelp-dark" data-help="customer-categories"></span></h4></div>-->
		<div class="row" style="margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid #ccc">
            <div class="col-md-6"><iframe width="450" height="450" frameborder="0" title="Categorie:" src="<?php echo admin_url( '/edit-tags.php?taxonomy=WPsCRM_customersCat&amp;layout=modal')?>"></iframe></div>
            <div class="col-md-1"></div>
			<div class="col-md-4">
                <label>
                    <?php _e('Kundenkategorie im Raster anzeigen','cpsmartcrm')?>?
                </label>
                <input type="checkbox" value="1" name="<?php echo $this->clients_settings_key ?>[gridShowCat]" <?php echo checked( 1, $showCategories, false ) ?> />
            </div>
		</div>
        <div class="row" style="margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid #ccc">
            <div class="col-md-6">
                <iframe width="450" height="450" frameborder="0" title="Interessi:" src="<?php echo admin_url( '/edit-tags.php?taxonomy=WPsCRM_customersInt&amp;layout=modal')?>"></iframe>
			</div>
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <label><?php _e('Zeige Kundeninteressen am Raster','cpsmartcrm')?>?
                </label>
                <input type="checkbox" value="1" name="<?php echo $this->clients_settings_key ?>[gridShowInt]" <?php echo checked( 1, $showInterests, false ) ?> />
            </div>
        </div>
        <div class="row" style="margin-bottom:10px;padding-bottom:10px;border-bottom:2px solid #ccc">
            <div class="col-md-6">
                <iframe width="450" height="450" frameborder="0" title="Interessi:" src="<?php echo admin_url( '/edit-tags.php?taxonomy=WPsCRM_customersProv&amp;layout=modal')?>"></iframe>
			</div>
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <label><?php _e('Kundenherkunft im Raster anzeigen','cpsmartcrm')?>?
                </label>
                <input type="checkbox" value="1" name="<?php echo $this->clients_settings_key ?>[gridShowOr]" <?php echo checked( 1, $showOrigins, false ) ?> />
            </div>
        </div>
		
	</div>
    <script>
    	jQuery(document).ready(function ($) {
    		//$('#submit').unbind('click').remove();
    	})
	</script>
            
    <?php
	} 


	function smart_crm_documents_settings(){
		global $document;
		$general_options=get_option('CRM_general_settings');
		$document_options=get_option($this->documents_settings_key );
    ?>
	<div id="innerTabstrip">
        <ul>
			<li class="k-state-active">
				<?php _e('Dokumenteinstellungen','cpsmartcrm')?>
			</li>
            <li >
                <?php _e('Dokumentkopf','cpsmartcrm')?>
            </li>
            
            <li>
                <?php _e('Zahlungsarten','cpsmartcrm')?>
            </li>
            <li>
                <?php _e('Messages','cpsmartcrm')?>
            </li>
            <li>
                <?php _e('Signatur','cpsmartcrm')?>
            </li>
            <li>
                <?php _e('Benutzerdefinierter Stil','cpsmartcrm')?>
            </li>
			<?php
		do_action('WPsCRM_add_tabs_to_document_settings')
            ?>
        </ul>
        <!-- Impostazioni varie (iva notifiche, numerazione etc) --><div>
                <div class="row">
                    <div id="global_vat">
                        <div class="widget col-md-5 pull-left">
                            <h3><span class="crmHelp crmHelp-dark" data-help="default-vat"></span>
                                <?php _e('Standardmäßige Mehrwertsteuer und Währung','cpsmartcrm')?>
                            </h3>
                            <div>
                                <div class="col-md-4 pull-left">
                                    <label style="font-size:1.4em; position:relative;top:-5px"><?php _e('VAT','cpsmartcrm')?> (%) </label>
                                    <input class="col-md-4" type="number" id="default_vat" name="<?php echo $this->documents_settings_key ?>[default_vat]" value="<?php echo $document_options['default_vat']?>" />
                                </div>
                                <div class="col-md-6 pull-right">
                                    <label style="font-size:1.4em; position:relative;top:-15px"><?php _e('Währung','cpsmartcrm')?> </label>
									<?php
									if(!isset($document_options['crm_currency']))
										$document_options['crm_currency']="";
									$html = '<select id="crm_currency"  name="'.$this->documents_settings_key.'[crm_currency]" class="col-md-6">';
									$html .= '<option value="default">'.__('Select','cpsmartcrm').'</option>';
									$html .= '<option value="EUR"' . selected(  $document_options['crm_currency'], 'EUR', false) . '>EUR</option>';
									$html .= '<option value="USD"' . selected(  $document_options['crm_currency'], 'USD', false) . '>USD</option>';
									$html .= '<option value="GBP"' . selected(  $document_options['crm_currency'], 'GBP', false) . '>GBP</option>';
									$html .= '<option value="CHF"' . selected(  $document_options['crm_currency'], 'CHF', false) . '>CHF</option>';
									$html .= '<option value="BRL"' . selected(  $document_options['crm_currency'], 'BRL', false) . '>BRL</option>';
									$html .= '<option value="INR"' . selected(  $document_options['crm_currency'], 'INR', false) . '>INR</option>';
									$html .= '<option value="CNY"' . selected(  $document_options['crm_currency'], 'CNY', false) . '>CNY</option>';
									$html .= '<option value="JPY"' . selected(  $document_options['crm_currency'], 'JPY', false) . '>JPY</option>';
									$html .= '</select>';
		
									echo $html;
                                    ?>
									
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="payment_notification">
                        <div class="widget col-md-5 pull-right">
                            <h3><span class="crmHelp crmHelp-dark" data-help="payment-notification"></span>
                                <?php _e('Tage nach Zahlungsbenachrichtigung','cpsmartcrm')?>
                            </h3>

                            <div>
                                <label style="font-size:1.4em; position:relative;top:-5px"> </label>
                                <input class="col-md-3" type="number" id="invoice_noty_days" name="<?php echo $this->documents_settings_key ?>[invoice_noty_days]" value="<?php echo isset($document_options['invoice_noty_days'])?$document_options['invoice_noty_days']:''?>" />
                            </div>
                        </div>
                    </div>
				</div>
                <div class="row">
                    <div id="_header_invoices_numbering">
                        <div class="widget col-md-5 pull-left">
                            <h3><span class="crmHelp crmHelp-dark" data-help="document-numbering"></span>
                                <?php _e('Nummerierungseinstellungen für Rechnungen','cpsmartcrm')?>
                            </h3>

                            <div>
                                <div class="col-md-10">
                                    <label class="col-md-10">
                                        <?php _e('Rechnungspräfix','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="text" id="invoices_prefix" name="<?php echo $this->documents_settings_key ?>[invoices_prefix]" value="<?php echo isset( $document_options['invoices_prefix']) ? $document_options['invoices_prefix'] : null ?>" />
                                    <label class="col-md-10">
                                        <?php _e('Rechnungssuffix','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="text" id="invoices_suffix" name="<?php echo $this->documents_settings_key ?>[invoices_suffix]" value="<?php echo isset( $document_options['invoices_suffix']) ? $document_options['invoices_suffix'] : null?>" />
                                    <label class="col-md-10">
                                        <?php _e('Rechnungen letzte Beilage','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="number" min="0" id="invoices_start" name="<?php echo $this->documents_settings_key ?>[invoices_start]" value="<?php echo isset( $document_options['invoices_start']) ? $document_options['invoices_start'] : null?>" />

                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="_header_offers_numbering">
                        <div class="widget col-md-5 pull-right">
                            <h3><span class="crmHelp crmHelp-dark" data-help="document-numbering"></span>
                                <?php _e('Einstellungen für die Nummerierung von Angeboten','cpsmartcrm')?>
                            </h3>

                            <div>
                                <div class="col-md-10">
                                    <label class="col-md-10">
                                        <?php _e('Anmerkunge Präfix','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="text" id="offers_prefix" name="<?php echo $this->documents_settings_key ?>[offers_prefix]" value="<?php echo isset( $document_options['offers_prefix']) ? $document_options['offers_prefix'] : null?>" />
                                    <label class="col-md-10">
                                        <?php _e('Anmerkunge Suffix','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="text" id="offers_suffix" name="<?php echo $this->documents_settings_key ?>[offers_suffix]" value="<?php echo isset( $document_options['offers_suffix']) ? $document_options['offers_suffix'] : null?>" />
                                    <label class="col-md-10">
                                        <?php _e('Anmerkunge letzter Einsatz','cpsmartcrm')?>
                                    </label>
                                    <input class="col-md-10" type="number" min="0" id="offers_start" name="<?php echo $this->documents_settings_key ?>[offers_start]" value="<?php echo isset( $document_options['offers_start']) ? $document_options['offers_start'] : null?>" />

                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                    	jQuery(document).ready(function($){
                    		$('#invoices_start').on('input', function () {
                    			if( $(this).val() < <?php echo (isset($document_options['invoices_start']) && $document_options['invoices_start']!="" ) ? $document_options['invoices_start'] : '0' ?> )
									alert("<?php _e('Warnung: Du gibst eine Nummer ein, die niedriger ist als die der zuletzt ausgestellten Rechnung. Sei vorsichtig, dies kann zu Problemen mit doppelten IDs führen','cpsmartcrm')?>");
                    		})
                    		$('#offers_start').on('input', function () {
                    			if( $(this).val() < <?php echo (isset($document_options['invoices_start']) && $document_options['invoices_start'] !="")  ? $document_options['invoices_start'] : '0' ?> )
									alert("<?php _e('Warnung: Du gibst eine Zahl ein, die niedriger ist als die zuletzt ausgegebene Zahl. Sei vorsichtig, dies kann zu Problemen mit doppelten i führend','cpsmartcrm')?>");
                    		})
                    	})
                    </script>
                </div>
				</div><!-- FINE Impostazioni varie -->
        <!--IMPOSTAZIONI ALLINEAMENTO ELEMENTI --><div>

            <div id="_header_align">
                <div class="dash-head hidden-on-narrow">
                    <h4 style="text-align: center;" class="page-header"><span class="crmHelp crmHelp-dark" data-help="header-align"></span><?php _e('Ziehe Elemente (von links nach rechts), um sie in Dokumenten auszurichten ','cpsmartcrm')?> </h4>
                </div>
                <div class="panel-wrap hidden-on-narrow row">
                    <div id="sortable-horizontal">
						<?php if( isset($document_options['header_alignment']) &&  ($document_options['header_alignment']=="" || $document_options['header_alignment'] == 'logo,text' )) { ?>
                        <div id="_logo" class="col-md-5">
                            <div class="widget">
                                <h3>
                                    <?php _e('Logo','cpsmartcrm')?>
                                    <span class="collapse k-icon k-i-arrowhead-n"></span>
                                </h3>
                                <div style="text-align:center">

                                    <img src="<?php echo isset($general_options['company_logo'])?$general_options['company_logo']:''?>" />

                                </div>
                                <a href="<?php echo admin_url( '/admin.php?page=smartcrm_settings&tab=CRM_general_settings' )?>">
                                    <?php _e('Bearbeiten','cpsmartcrm')?>&raquo;
                                </a>
                            </div>

                        </div>
                        <div id="_intestazione" class="col-md-6">
                            <div id="news" class="widget">
                                <h3>
                                    <?php _e('Header','cpsmartcrm')?>
                                    <span class="collapse k-icon k-i-arrowhead-n"></span>
                                </h3>
                                <div>
                            <?php foreach($document->master_data() as $data =>$val){

									  $val1 = array_values($val);
									  if(isset($val['show']) && $val['show']==1)
									  {
										  if(isset($val['show_label']) && $val['show_label']==1 && html_entity_decode($val1[0]) !="")
										  { ?>
										<p style="line-height:1em">
											<?php echo"<small>". key($val) ."</small>:". html_entity_decode($val1[0])?>
										</p>
										<?php }
										  else if( $val1[0] !="" ){?>
										<p style="line-height:1em">
											<?php  echo $val1[0]?>
										</p>
										<?php }
									  }

								  }
                                        ?>
                                </div>
                                <a href="<?php echo admin_url( '/admin.php?page=smartcrm_settings&tab=CRM_business_settings' )?>">
                                    <?php _e('Bearbeiten','cpsmartcrm')?>&raquo;
                                </a>
                            </div>

                        </div>
						<?php } else { ?> 
						<div id="_intestazione" class="col-md-6">
                            <div id="news" class="widget">
                                <h3>
                                    <?php _e('Header','cpsmartcrm')?>
                                    <span class="collapse k-icon k-i-arrowhead-n"></span>
                                </h3>
                                <div>
                            <?php foreach($document->master_data() as $data =>$val){
									  $val1 = array_values($val);
									  if($val['show']==1)
									  {
										  if(isset($val['show_label']) && $val['show_label']==1 && html_entity_decode($val1[0]) !="")
										  { ?>
								<p style="line-height:1em;">
									<?php echo"<small>". key($val) ."</small>:". html_entity_decode($val1[0])?>
								</p>
								<?php }
										  else if( $val1[0] !="" ){?>
								<p style="line-height:1em">
									<?php  echo $val1[0]?>
								</p>
								<?php }
									  }

								  }
                                ?>
                                </div>
                                <a href="<?php echo admin_url( '/admin.php?page=smartcrm_settings&tab=CRM_business_settings' )?>">
                                    <?php _e('Bearbeiten','cpsmartcrm')?>&raquo;
                                </a>
                            </div>

                        </div>
						<div id="_logo" class="col-md-5">
                            <div class="widget">
                                <h3>
                                    <?php _e('Logo','cpsmartcrm')?>
                                    <span class="collapse k-icon k-i-arrowhead-n"></span>
                                </h3>
                                <div style="text-align:center">

                                    <img src="<?php echo isset($general_options['company_logo'])?$general_options['company_logo']:''?>"" />

                                </div>
                                <a href="<?php echo admin_url( '/admin.php?page=smartcrm_settings&tab=CRM_general_settings' )?>">
                                    <?php _e('Bearbeiten','cpsmartcrm')?>&raquo;
                                </a>
                            </div>

                        </div>
                        
						<?php } ?>
                    </div>
                </div>

                <div class="responsive-message"></div>
				<input type="hidden" id="header_alignment" name="<?php echo $this->documents_settings_key ?>[header_alignment]" value="<?php if (isset($document_options['header_alignment'])) echo $document_options['header_alignment']?>"/>
                <script>
                	jQuery(document).ready(function ($) {
                	var sortable = $("#sortable-horizontal").kendoSortable({
                		filter: ">div",
                		axis: "x",
                		cursor: "move",
                		container: "#sortable-horizontal",
                		placeholder: placeholder,
                		hint: hint,
                		change: function (e) {
                			var first = $(this.items()[0]);
                			if ($(first).attr('id') == "_logo")
                				$('#header_alignment').val('logo,text')
                			else
                				$('#header_alignment').val('text,logo')
                			console.log(this.items() )
                		}
                	}).data("kendoSortable");
                });

                function placeholder(element) {
                    return element.clone().addClass("placeholder");
                }

                function hint(element) {
                    return element.clone().addClass("hint")
                                .height(element.height())
                                .width(element.width());
                }
                </script>


            </div>
            
        </div><!--FINE IMPOSTAZIONI ALLINEAMENTO ELEMENTI -->
        <!--METODI DI PAGAMENTO--><div>
			
            <?php 
		$options= get_option( $this->documents_settings_key );
		$payOptions=isset($options['delayedPayments'] ) ? $options['delayedPayments'] : null;
            ?>
		<div id="_payments">
			<div class="dash-head hidden-on-narrow">
                <h4 style="text-align: center;margin:30px auto" class="page-header" >
                    <?php _e('Definitionen der Zahlungsmethoden','cpsmartcrm')?><span class="crmHelp crmHelp-dark" data-help="options-payments-definitions"></span>
                </h4>
            </div>
            <div class="panel-wrap hidden-on-narrow row">
				<div class="col-md-4">
					<div class="input-group">
                        <label>
                            <?php _e('Label','cpsmartcrm')?> <span style="color:red">*</span>
                            <input type="text" id="addPayment" class="form-control _m" />
                        </label>
                        <label>
                            <?php _e('Tage','cpsmartcrm')?>
                            <input type="number" id="daysPayment" class="form-control _m" />
                        </label>
						<span class="input-group-btn">
						<button class="btn btn-default" id="_savePayment" type="button" style="margin-top:40px"><?php _e('Hinzufügen','cpsmartcrm')?> &raquo;</button>
						</span>
					</div>
					</div>
				<div class="col-md-6"><ul id="activePayments"></ul></div>
			</div>

		<?php 
		$arr_payments=maybe_unserialize($payOptions);
		$html = '<select multiple id="delayedPayments" name="'. $this->documents_settings_key.'[delayedPayments][]" style="display:none">';
		$option_index=0;
		if($arr_payments)
			foreach($arr_payments as $pay){
				$pay_label=$pay;
				$html .= '<option value="'.$pay.'" selected data-index="'.$option_index.'">' . $pay_label . '</option>';
				$option_index ++;
			}
		$html .= '</select>';
		echo $html;
        ?>
		</div>
		<script>
			var pay = [];
			<?php
		if (!empty($arr_payments ) )
			foreach($arr_payments as $pay){?>
			pay.push('<?php echo $pay ?>');
			<?php } ?>
			jQuery(document).ready(function ($) {
				$('#_savePayment').on('click', function () {
					if ($('#addPayment').val() == "")
						return;
					var index = parseInt($('#activePayments li').length) ;
					var days = $('#daysPayment').val().toString();
					var e;
					days != "" ? (days = ("~" + days), e="("+ days +" <?php _e('dd','cpsmartcrm')?>)"): (days = "" ,e="");
					$('#delayedPayments').append('<option value="' + $('#addPayment').val() + days +'" selected="selected" data-index="' + index + '">' + $('#addPayment').val() +'</option>\n')
					$('#activePayments').append('<li class="' + index + '-' + $('#addPayment').val() + '" data-index="' + index + '"><span>' + $('#addPayment').val() + '</span> <span class="_days"> '+ e.replace('~','') +'</span><i class="glyphicon glyphicon-remove" style="float:right;margin-right:20px"></i></li>\n');
					$('#addPayment').val(''), $('#daysPayment').val('');
				})
				for (var k = 0; k < pay.length; k++) {
					var m = pay[k].split('~');
					if (m[1] != undefined)
						m = m[0] + " (" + m[1] + " <?php _e('dd','cpsmartcrm')?>)";
					else m = pay[k];

					$('#activePayments').append('<li class="' + k + '-' + pay[k] + ' " data-index="' + k + '"><span>' + m + '</span><i class="glyphicon glyphicon-remove" style="float:right;margin-right:20px"></i></li>\n');
				}

				$('#activePayments').on('click', 'i', function () {
					var $this = $(this).parent().data('index');
					console.log($this);
					$('#delayedPayments').find('[data-index="' + $this + '"]').remove()
					$(this).parent().remove();
				})
			})
		</script>
        
        </div><!--FINE METODI DI PAGAMENTO-->
        <!-- MESSAGGI FATTURE / PREVENTIVI--><div>
           
            <div id="_header_invoices_messages">
                <div class="dash-head hidden-on-narrow">
				<h4 style="text-align: center;margin:30px auto" class="page-header" ><span class="crmHelp crmHelp-dark" data-help="document-messages"></span><?php _e('INVOICES MESSAGES SETTINGS','cpsmartcrm')?> </h4></div>
                <div class="panel-wrap hidden-on-narrow row">
                    <div class="col-md-12 _messages">
                        <div class="item">
                            <label><?php _e('Hallo','cpsmartcrm')?></label>
                            <input type="text" id="crm_invoices_dear" name="<?php echo $this->documents_settings_key ?>[invoices_dear]" value="<?php echo  isset($document_options['invoices_dear'] ) ? $document_options['invoices_dear'] : null?>" class="form-control _m"/>
                        </div>
                        <div class="item">
                            <label><?php _e('Rechnungen vor Text','cpsmartcrm')?></label>
                            
                            <textarea id="crm_invoices_before" name="<?php echo $this->documents_settings_key ?>[invoices_before]" class="_m" style="width:96%"><?php echo isset($document_options['invoices_before'] ) ? $document_options['invoices_before'] : null ?></textarea>
                        </div>
                        <div class="item">
                            <label><?php _e('Rechnungen nach Text','cpsmartcrm')?></label>
                            <textarea  id="crm_invoices_after" name="<?php echo $this->documents_settings_key ?>[invoices_after]" class="_m" style="width:96%"><?php echo isset($document_options['invoices_after'] ) ? $document_options['invoices_after'] : null ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div id="_header_offers_messages">
                <div class="dash-head hidden-on-narrow">
					<h4 style="text-align: center;margin:30px auto" class="page-header"><span class="crmHelp crmHelp-dark" data-help="document-messages"></span><?php _e('ZITATE NACHRICHTEN EINSTELLUNGEN','cpsmartcrm')?> </h4>
				</div>
                <div class="panel-wrap hidden-on-narrow row _messages">
                    <div class="col-md-12">

                        <div class="item">
                            <label><?php _e('Hallo','cpsmartcrm')?></label>
                            <input type="text" id="crm_offers_dear" name="<?php echo $this->documents_settings_key ?>[offers_dear]" value="<?php echo  isset( $document_options['offers_dear'] ) ? $document_options['offers_dear'] : null?>" class="form-control _m"/>
                        </div>
                        <div class="item">
                            <label><?php _e('Anmerkunge vor dem Text','cpsmartcrm')?></label>
                            <textarea id="crm_offers_before" name="<?php echo $this->documents_settings_key ?>[offers_before]" class="_m" style="width:96%"><?php echo isset($document_options['offers_before'] ) ? $document_options['offers_before']: null ?></textarea>
                        </div>
                        <div class="item">
                            <label><?php _e('Anmerkunge nach dem Text','cpsmartcrm')?></label>
                            <textarea id="crm_offers_after" name="<?php echo $this->documents_settings_key ?>[offers_after]" class="_m" style="width:96%"><?php echo isset($document_options['offers_after'] ) ? $document_options['offers_after'] : null ?></textarea>
                        </div>
                    </div>
                </div>
				<style>._messages input{width:50%;height:20px}</style>
            </div>
        </div><!-- FINE MESSAGGI OFFERTE/PREVENTIVI -->
        <!-- FIRMA --><div>
            
            <div id="_signature" >

                <div class="dash-head hidden-on-narrow">
                    <h1 style="text-align:center">
                        <?php _e('Signatureinstellungen','cpsmartcrm')?>
                    </h1>
                    <h4 style="text-align: center; padding: 12px;background-color:gainsboro;">
                        <?php _e('Zeichne Deine Signatur (Maus oder Touch), um sie in Anführungszeichen zu verwenden','cpsmartcrm')?>
                    </h4>
                </div>
                <div class="panel-wrap hidden-on-narrow row" style="margin-top:20px">

                    <div id="signature-pad" class="m-signature-pad">

                        <div class="m-signature-pad--body" style="text-align:center;">
                            <canvas id="cSignature" style="width:800px;border:1px solid #666"></canvas>
                        </div>
                        <div class="m-signature-pad--footer">
                            <br />
                            <button type="button" class="btn btn-warning btn-sm _flat" data-action="clear">
                                <?php _e('Zurücksetzen','cpsmartcrm')?>
                            </button>
                            <button type="button" class="btn btn-success btn-sm _flat" data-action="save">
                                <?php _e('Speichern','cpsmartcrm')?>
                            </button>
                            <div style="float:right;margin-right:100px">
                                <label>
                                    <?php _e('Verwende diese Signatur','cpsmartcrm')?>?
                                </label>
                                <input type="checkbox" value="1" name="<?php echo $this->documents_settings_key ?>[use_crm_signature]" <?php echo checked( 1, isset($document_options['use_crm_signature']) ? $document_options['use_crm_signature'] : 0, false ) ?> />
                                <input type="hidden" id="crm_signature" name="<?php echo $this->documents_settings_key ?>[crm_signature]" value="<?php echo  isset($document_options['crm_signature'] ) ? $document_options['crm_signature'] : null?>" />
                            </div>
                        </div>
                    </div>

                    <h4 style="text-align: center; padding: 12px;background-color:gainsboro;">
                        <?php _e('Formatierte Signatur (z. B. Firmenname)','cpsmartcrm')?>
                    </h4>

                    <div style="text-align:center" id="signature_formatted">
                        <span class="editable_signature" data-field="formatted_signature" id="editor_signature_formatted" style="border:1px solid;height:90px;width:800px;text-align:left!important" >
                        <?php echo isset($document_options['crm_formatted_signature'] ) ? html_entity_decode($document_options['crm_formatted_signature'] ) : null?>
                        </span>
                    <input type="hidden" id="crm_formatted_signature" name="<?php echo $this->documents_settings_key ?>[crm_formatted_signature]" value="<?php echo  isset($document_options['crm_formatted_signature'] ) ?$document_options['crm_formatted_signature'] : null?>" />
                    </div>
                    <div class="m-signature-pad--footer">
                        <br />
                        <button type="button" class="btn btn-warning btn-sm _flat" onclick="jQuery('#editor_signature_formatted').html(''); jQuery('#crm_formatted_signature').val('');">
                            <?php _e('Zurücksetzen','cpsmartcrm')?>
                        </button>
                        <button type="button" class="btn btn-success btn-sm _flat" onclick="jQuery('#submit').trigger('click');">
                            <?php _e('Speichern','cpsmartcrm')?>
                        </button>
                        <div style="float:right;margin-right:100px">

                            <label>
                                <?php _e('Verwende diese Signatur','cpsmartcrm')?>?
                            </label>
                            <input type="checkbox" value="1" name="<?php echo $this->documents_settings_key ?>[use_crm_formatted_signature]" <?php echo checked( 1, isset($document_options['use_crm_formatted_signature'] ) ? $document_options['use_crm_formatted_signature'] : 0 , false ) ?> />
                        </div>
                    </div>

                    </div>

                <script type="text/javascript">
                var _canvas = document.getElementById("cSignature");
                var ctx = _canvas.getContext("2d");
                data = "<?php echo isset($document_options['crm_signature']) ? $document_options['crm_signature'] : "" ?>";
                var image = new Image();
                image.onload = function () {
                    ctx.drawImage(image, 0, 0);
                };
                image.src = data;
                </script>
            </div>
            
		</div><!-- END FIRMA -->
		<!--CUSTOM CSS--><div>
                    <div id="_custom_css">
                        <div class="dash-head hidden-on-narrow">
                            <h1 style="text-align:center">
                                <?php _e('Benutzerdefinierte CSS','cpsmartcrm')?>
                            </h1>
                            <h4 style="text-align: center; padding: 12px;background-color:gainsboro;">
                                <?php _e('Füge einen vorhandenen Stil in PDF-Dokumenten hinzu oder überschreibe ihn','cpsmartcrm')?>
                            </h4>
                        </div>
                        <div class="panel-wrap hidden-on-narrow row" style="margin-top:20px">
                            <div class="col-md-12">
                                <textarea name="<?php echo $this->documents_settings_key ?>[document_custom_css]" style="width:100%;height:200px"><?php echo isset($document_options['document_custom_css'] ) ? $document_options['document_custom_css'] : null?></textarea>
                            </div>
							<p><?php _e('Verwende diese CSS-Regeln, um das Layout der druckbaren Version Deiner Dokumente anzupassen (verfügbar, nachdem ein Dokument erstellt wurde).','cpsmartcrm')?>. </p>
						</div>
                    </div>
                </div><!--END CUSTOM CSS-->
    </div>
<!--SIGNATURE SCRIPT--><script>

    var wrapper = document.getElementById("signature-pad"),
    clearButton = wrapper.querySelector("[data-action=clear]"),
    saveButton = wrapper.querySelector("[data-action=save]"),
    canvas = wrapper.querySelector("canvas"),
    signaturePad;
    function resizeCanvas() {
        // When zoomed out to less than 100%, for some very strange reason,
        // some browsers report devicePixelRatio as less than 1
        // and only part of the canvas is cleared then.
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    window.onresize = resizeCanvas;
    resizeCanvas();

    signaturePad = new SignaturePad(canvas);

    clearButton.addEventListener("click", function (event) {
        signaturePad.clear();
    });

    saveButton.addEventListener("click", function (event) {
        if (signaturePad.isEmpty()) {
            alert("Bitte gib zuerst Deine Unterschrift ein.");
        } else {
            //window.open(signaturePad.toDataURL());
            var Pic = signaturePad.toDataURL();
            //Pic = Pic.replace(/^data:image\/(png|jpg);base64,/, "");
            jQuery('#crm_signature').val(Pic);
            jQuery('#submit').trigger('click');
        }
    });

</script><!--END SIGNATURE SCRIPT-->

<?php
		do_action('WPsCRM_add_documents_inner_divs');
	}       
    function WPsCRM_add_admin_menus() {
		
		add_submenu_page(
			'smart-crm',
			__('CP SMART CRM EINSTELLUNGEN','cpsmartcrm'),
			__('Einstellungen','cpsmartcrm'),
			'manage_options', 
			'smartcrm_settings',
			array( $this, 'plugin_options_page' )
			);
		
	}
	
	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function plugin_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
?>
		<div class="wrap">
            
			<?php 
		$this->header();
		$this->plugin_options_tabs(); ?>
			
            <div class="row">

                <div class="col-md-12">
			    <form method="post" action="options.php">
				    <?php wp_nonce_field( 'update-options' ); ?>
				    <?php settings_fields( $tab ); ?>
				    <?php do_settings_sections( $tab ); ?>
                    <span class="" style="padding:12px"><input type="submit" name="submit" id="submit" class="_flat btn btn-success" value="<?php _e('Speichern','cpsmartcrm')?>" style="margin: 30px;<?php if(isset($_GET['tab']) && $_GET['tab']=="CRM_business_settings") { ?>display:none;<?php } ?>" ></span>
			    </form>
                </div>

            </div>
    
            <?php $this->footer();?>
		</div>
<style>
                
    .left-menu{margin:100px 20px 0}
    .href{display:none;margin-top:24px}
    .dash-head {
        width: 970px;
        height: 80px;
        background-color: #f3f5f7;
        color:#393939;
        /*background: url('../content/web/sortable/dashboard-head.png') no-repeat 50% 50% #222222;*/
            border-top-left-radius: 4px;
            border-top-right-radius:4px
    }

    .panel-wrap {
        display: table;
        margin: 0 0 20px;
        width: 968px;
        background-color: #f5f5f5;
        border: 1px solid #e5e5e5;
    }

    #sidebar {
        display: table-cell;
        margin: 0;
        padding: 20px 0 20px 20px;
        /*width: 220px;*/
        vertical-align: top;
    }

    #main-content {
        display: table-cell;
        margin: 0;
        padding: 20px;
        /*width: 680px;*/
        vertical-align: top;
    }

    .widget.placeholder {
        opacity: 0.4;
        border: 1px dashed #a6a6a6;
    }

    /* WIDGETS */
    .widget {
        margin: 0 0 20px;
        padding: 0;
        background-color: #ffffff;
        border: 1px solid #e7e7e7;
        border-radius: 3px;
        cursor: move;
    }

    .widget:hover {
        background-color: #fcfcfc;
        border-color: #cccccc;
    }

    .widget div {
        padding: 10px;
        min-height: 50px;
    }

    .widget h3 {
        font-size: 12px;
        padding: 8px 10px;
        text-transform: uppercase;
        border-bottom: 1px solid #e7e7e7;
    }

    .widget h3 span {
        float: right;
    }

    .widget h3 span:hover {
        cursor: pointer;
        background-color: #e7e7e7;
        border-radius: 20px;
    }
   
    .tooltip {
        opacity: .6;
        width:50%!important
    }
    #activeCategories li, #activeOrigins li{list-style:none;padding-bottom:10px;border-bottom:1px solid #ccc;width:100%}
    #activeCategories i, #activeOrigins i{cursor:pointer;color:red}
    #activePayments{margin-top:30px}
    #activePayments li{list-style:none;padding-bottom:10px;border-bottom:1px solid #ccc;width:100%}
    label{font-size:.95em;font-weight:300;}

    label.toRight{float:right;font-size:xx-small}
    .k-tool-text{display:none!important}
    /*.k-editor-inline {
        margin: 0;
        padding: 21px 21px 11px;
        border-width: 0;
        box-shadow: none;
        background: none;
    }

    .k-editor-inline.k-state-active {
        border-width: 1px;
        padding: 20px 20px 10px;
        background: none;
    }*/
    .k-editor-inline {
        border:none;
        /*border:1px solid #ccc*/
    }
    .k-editor-inline.k-state-active {
        border:1px solid #ccc;
        /*padding: 20px 20px 10px;*/
        background: none;
    }
    .editable,.editable_signature{width:56%;display:inline-block;font-weight:300;}
    #pages {
        /*margin: 30px auto;
        width: 300px;*/
        background-color: #f3f5f7;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,.1);
    }

    #pages-title {
        height: 60px;
    }

    .item {
        margin: 10px;
        padding:3px 12px;
        min-width: 200px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.1);
        border-radius: 3px;
        font-size: 1.3em;
        line-height: 2.5em;
    }

    .placeholder {
        width: 298px;
        border: 1px solid #2db245;
    }

    .hint {
        border: 2px solid #2db245;
        border-radius: 6px;
    }

    .hint .handler {
        background-color: #2db245;
    }
    #activePayments i{cursor:pointer;color:red}
	#innerTabstrip .row{margin:0}
	#innerTabstrip .widget h3{font-weight:bold}
	#innerTabstrip-1 .crmHelp{margin-top: -18px;font-size:large}
	#innerTabstrip-1 .widget{cursor:inherit}
	#innerTabstrip-2 h2{font-size:20px}
</style>
<script>
	jQuery(document).ready(function ($) {
		<?php if(isset($_GET['tab']) && $_GET['tab']=="CRM_business_settings") { ?>

		$('.form-table th').hide().remove();

		<?php } ?>
		<?php if(isset($_GET['tab']) && $_GET['tab']=="CRM_documents_settings") { ?>
			
		$('.form-table th').hide().remove();
		<?php } ?>
        var getEffects = function () {
            return ("expand:vertical fadeIn") || false;
        };
        var innerTabstrip = $("#innerTabstrip").kendoTabStrip({ animation: { open: { effects: getEffects() } } }).data('kendoTabStrip');
		if(window.location.hash) {
			var innerHash = window.location.hash.substring(9); //Puts hash in variable, and removes the # character
			innerTabstrip.select(innerHash);
			}
        $(".editable").kendoEditor({
            tools: [
                "bold",
                "italic",
                "underline",
                "createLink",
                "unlink"
            ],
            change: editorChange
        });
        $(".editable_signature").kendoEditor({
        	tools: [
                "bold",
                "italic",
                "underline"
        	],
        	change: editorSignatureChange
        });
        function editorChange(e) {
            var editor = $(".editable").data("kendoEditor");
            var content = $(e.sender.element[0]).html();
            content = content.replace(/[\u00A0-\u9999<>\&]/gim, function (i) {
                return '&#' + i.charCodeAt(0) + ';';
            }).replace('&#60;br class="k-br"&#62;', '').replace('&#65279;', '').replace('<br class="k-br">', '');
            var el = $(e.sender.element[0]).data('field');
            $('#crm_' + el).val(content.replace('<em></em>&#65279;', '').replace('<br class="k-br">', '').replace('&#65279;', '').replace('&#60;br class="k-br"&#62;', ''));
        }

        function editorSignatureChange(e) {
        	var editor = $(".editable_signature").data("kendoEditor");
        	var content = $(e.sender.element[0]).html();
        	content = content.replace(/[\u00A0-\u9999<>\&]/gim, function (i) {
        		return '&#' + i.charCodeAt(0) + ';';
        	}).replace('&#60;br class="k-br"&#62;', '').replace('&#65279;', '').replace('<br class="k-br">', '');
        	var el = $(e.sender.element[0]).data('field');
        	$('#crm_' + el).val(content.replace('<em></em>&#65279;', '').replace('<br class="k-br">', '').replace('&#65279;', '').replace('&#60;br class="k-br"&#62;', ''));
        }
    });
</script>

		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;

		
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		
		echo '</h2>';
	}
}
add_action( 'plugins_loaded', function() {
    $wp_crm = new CRM_Options_Settings;
});
        ?>
<?php

/**
//subscription rules FIELDS
 * manage subscription rules save in wp_options
 **/ 

function smartcrm_subscription_rules(){
    require_once(__DIR__ . '/subscription_rules.php');

}
function smartcrm_fields(){
    //require_once(__DIR__ . '/custom_fields.php');

}
