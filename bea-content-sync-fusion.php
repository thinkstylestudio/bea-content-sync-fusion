<?php
/*
  Plugin Name: BEA - Content Synchronization - Fusion
  Plugin URI: https://beapi.fr
  Description: Manage content synchronization across a WordPress multisite.
  Version: 3.0.8
  Author: Be API
  Author URI: http://beapi.fr
  Network: true
  Required WP : 4.6

  Copyright 2013-2017 - Be API Team (technique@beapi.fr)
  
  TODO :
	AJAX Taxo for Sync edition
 */

// Plugin constants
define( 'BEA_CSF_VERSION', '3.0.8' );
define( 'BEA_CSF_OPTION', 'bea-content-sync-fusion' );
define( 'BEA_CSF_CRON_QTY', 500 );

// Define the table relation variables
if ( empty( $GLOBALS['wpdb']->bea_csf_relations ) ) {
	$GLOBALS['wpdb']->bea_csf_relations  = $GLOBALS['wpdb']->base_prefix . 'bea_csf_relations';
	$GLOBALS['wpdb']->ms_global_tables[] = 'bea_csf_relations';
}

// Tables for ASYNC
// Define the table queue variables
if ( empty( $GLOBALS['wpdb']->bea_csf_queue ) ) {
	$GLOBALS['wpdb']->bea_csf_queue      = $GLOBALS['wpdb']->base_prefix . 'bea_csf_queue';
	$GLOBALS['wpdb']->ms_global_tables[] = 'bea_csf_queue';
}

if ( empty( $GLOBALS['wpdb']->bea_csf_queue_maintenance ) ) {
	$GLOBALS['wpdb']->bea_csf_queue_maintenance      = $GLOBALS['wpdb']->base_prefix . 'bea_csf_queue_maintenance';
	$GLOBALS['wpdb']->ms_global_tables[] = 'bea_csf_queue_maintenance';
}

// Plugin URL and PATH
define( 'BEA_CSF_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_CSF_DIR', plugin_dir_path( __FILE__ ) );

// Plugin various
require( BEA_CSF_DIR . 'classes/plugin.php' );
require( BEA_CSF_DIR . 'classes/client.php' );
require( BEA_CSF_DIR . 'classes/multisite.php' );

// Plugins addons
require( BEA_CSF_DIR . 'classes/addons/post-types-order.php' );
require( BEA_CSF_DIR . 'classes/addons/advanced-custom-fields-exclusion.php' );


// Functions various
require( BEA_CSF_DIR . 'functions/api.php' );

// Models
require( BEA_CSF_DIR . 'classes/models/async.php' );
require( BEA_CSF_DIR . 'classes/models/relations.php' );
require( BEA_CSF_DIR . 'classes/models/synchronization.php' );
require( BEA_CSF_DIR . 'classes/models/synchronizations.php' );

// Library server
require( BEA_CSF_DIR . 'classes/server/attachment.php' );
require( BEA_CSF_DIR . 'classes/server/post_type.php' );
require( BEA_CSF_DIR . 'classes/server/taxonomy.php' );
require( BEA_CSF_DIR . 'classes/server/p2p.php' );

// Library client
require( BEA_CSF_DIR . 'classes/client/attachment.php' );
require( BEA_CSF_DIR . 'classes/client/post_type.php' );
require( BEA_CSF_DIR . 'classes/client/taxonomy.php' );
require( BEA_CSF_DIR . 'classes/client/p2p.php' );

// Call admin classes
if ( is_admin() ) {
	require( BEA_CSF_DIR . 'classes/admin/admin-blog.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-synchronizations-network.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-metaboxes.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-client-metaboxes.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-restrictions.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-terms.php' );
	require( BEA_CSF_DIR . 'classes/admin/admin-terms-metaboxes.php' );
}

// Plugin activate/desactive hooks
register_activation_hook( __FILE__, array( 'BEA_CSF_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BEA_CSF_Plugin', 'deactivate' ) );

// Init !
add_action( 'plugins_loaded', 'init_bea_content_sync_fusion' );
function init_bea_content_sync_fusion() {
	// Load translations
	load_plugin_textdomain( 'bea-content-sync-fusion', false, basename( BEA_CSF_DIR ) . '/languages' );

	// Synchronizations
	BEA_CSF_Synchronizations::init_from_db();

	// Server
	new BEA_CSF_Client();
	new BEA_CSF_Multisite();

	// Addons
	new BEA_CSF_Addon_Post_Types_Order();
	new BEA_CSF_Addon_ACF_Exclusion();

	// Admin
	if ( is_admin() ) {
		new BEA_CSF_Admin_Synchronizations_Network();
		new BEA_CSF_Admin_Metaboxes();
		new BEA_CSF_Admin_Client_Metaboxes();
		new BEA_CSF_Admin_Restrictions();
		new BEA_CSF_Admin_Terms();
		new BEA_CSF_Admin_Terms_Metaboxes();
		new BEA_CSF_Admin_Blog();
	}
}
