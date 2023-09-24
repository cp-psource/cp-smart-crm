<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//add_action( 'tgmpa_register', 'WPsCRM_register_CRM_plugins' );
function WPsCRM_register_CRM_plugins() {
    /**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
    $plugins = array(

        array(
            'name' => 'Meta Box',
            'slug' => 'meta-box',
            'source' => dirname( __FILE__ ) . '/dependencies/meta-box.zip',
            'required' => true,
            'version' => '4.3.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url' => '', // If set, overrides default API URL and points to an external URL.
            ),
        );
    /**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
    $config = array(
        'default_path' => '', // Default absolute path to pre-packaged plugins.
        'menu' => 'tgmpa-install-plugins', // Menu slug.
        'has_notices' => true, // Show admin notices or not.
        'dismissable' => true, // If false, a user cannot dismiss the nag message.
        'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false, // Automatically activate plugins after installation or not.
        'message' => '', // Message to output right before the plugins table.
        'strings' => array(
        'page_title' => __( 'Install Required Plugins', 'cpsmartcrm' ),
        'menu_title' => __( 'Install Plugins', 'cpsmartcrm' ),
        'installing' => __( 'Installing Plugin: %s', 'cpsmartcrm' ), // %s = plugin name.
        'oops' => __( 'Something went wrong with the plugin API.', 'cpsmartcrm' ),
        'notice_can_install_required' => _n_noop( 'This plugin requires the following plugin: %1$s.', 'This plugin requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
        'notice_can_install_recommended' => _n_noop( 'This plugin recommends the following plugin: %1$s.', 'This plugin recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
        'notice_cannot_install' => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
        'notice_can_activate_required' => _n_noop( 'The following required plugin for WP Smart CRM is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
        'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
        'notice_cannot_activate' => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
        'notice_ask_to_update' => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
        'notice_cannot_update' => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
        'install_link' => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
        'activate_link' => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
        'return' => __( 'Return to Required Plugins Installer', 'cpsmartcrm' ),
        'plugin_activated' => __( 'Plugin activated successfully.', 'cpsmartcrm' ),
        'complete' => __( 'All plugins installed and activated successfully. %s', 'cpsmartcrm' ), // %s = dashboard link.
        'nag_type' => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
            )
    );
    tgmpa( $plugins, $config );
}
add_action('admin_init','WPsCRM_check_for_metabox_plugin');
if(!function_exists('WPsCRM_check_for_metabox_plugin')){
    function WPsCRM_check_for_metabox_plugin(){
        if ( ! class_exists( 'RW_Meta_Box' ) && is_plugin_active( 'meta-box/meta-box.php' ) )
            require_once (WP_CONTENT_DIR.'/plugins/meta-box/inc/meta-box.php');
		require_once(__DIR__ . '/metabox.php');
    }
}

?>