<?php
/*
Plugin Name: CP Referrer and Conversions Tracking
Plugin URI: http://wordpress.dwbooster.com/
Description: Tracking for referrer websites and conversions.
Version: 1.01.19
Author: CodePeople
Author URI: http://codepeople.net
License: GPL
Text Domain: cp-referrer-and-conversions-tracking
*/


// loading add-ons
// -----------------------------------------
global $cpreftrack_addons_active_list, // List of addon IDs
	   $cpreftrack_addons_objs_list; // List of addon objects
	   
$cpreftrack_addons_active_list = array();
$cpreftrack_addons_objs_list	 = array();
	
function cpreftrack_loading_add_ons()
{
	global $cpreftrack_addons_active_list, // List of addon IDs
		   $cpreftrack_addons_objs_list; // List of addon objects
	
    // Get the list of active addons
	$cpreftrack_addons_active_list = get_option( 'cpreftrack_addons_active_list', array() );
    if( !empty( $cpreftrack_addons_active_list ) 
        || ( isset( $_GET["page"] ) && $_GET["page"] == "cp_reftrack" )  
        || ( isset( $_GET["page"] ) && $_GET["page"] == "cp_reftrack_addons" )
      )
	{	
		$path = dirname( __FILE__ ).'/addons';
		if( file_exists( $path ) )
		{
			$addons = dir( $path );
			while( false !== ( $entry = $addons->read() ) ) 
			{    
				if( strlen( $entry ) > 3 && strtolower( pathinfo( $entry, PATHINFO_EXTENSION) ) == 'php' )
				{
					require_once $addons->path.'/'.$entry;
				}			
			}
		} 
	}	
}
cpreftrack_loading_add_ons();



/* initialization / install */

include_once dirname( __FILE__ ) . '/classes/cp-base-class.inc.php';
include_once dirname( __FILE__ ) . '/cp-main-class.inc.php';

$cp_reftrack_plugin = new CP_REFTRACK_Plugin;

register_activation_hook(__FILE__, array($cp_reftrack_plugin,'install') ); 
add_action( 'init', array($cp_reftrack_plugin, 'data_management'));
add_action( 'cpreftrack_register_conversion', array( $cp_reftrack_plugin, 'register_conversion' ), 10, 3 );
add_filter( 'cpreftrack_referrer', array( $cp_reftrack_plugin, 'referrer_filter' ), 10, 1 );

// cron setup to delete old events
add_filter( 'cron_schedules',  array( $cp_reftrack_plugin, 'isa_add_cron_recurrence_interval' ) ); 
if ( ! wp_next_scheduled( 'cpreftrack_del_old_hook_fmin' ) ) {                
    wp_schedule_event( time(), 'cp_every_once_day', 'cpreftrack_del_old_hook_fmin' );
}
add_action( 'cpreftrack_del_old_hook_fmin', array( $cp_reftrack_plugin, 'delete_old_logs' ) ); 


// admin filters & actions
if ( is_admin() ) {    
    add_action('admin_enqueue_scripts', array($cp_reftrack_plugin,'insert_adminScripts'), 1);    
    add_filter("plugin_action_links_".plugin_basename(__FILE__), array($cp_reftrack_plugin,'plugin_page_links'));   
    add_action('admin_menu', array($cp_reftrack_plugin,'admin_menu') );
}

// banner             
$codepeople_promote_banner_plugins_cp_ref[ 'cp-referrer-and-conversions-tracking' ] = array( 
                      'plugin_name' => 'CP Referrer and Conversion Tracking', 
                      'plugin_url'  => 'https://wordpress.org/support/plugin/cp-referrer-and-conversions-tracking/reviews/?filter=5#new-post'
);
require_once 'banner.php';

?>