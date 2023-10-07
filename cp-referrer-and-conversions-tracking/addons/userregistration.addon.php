<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_UserRegistration' ) )
{

    class CPREFTRACK_UserRegistration extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-UserRegistration-20220820";
		protected $name = "User Registration Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'New User Registration';
        protected $conversion_details_prefix = 'USER ID: ';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for new user registrations", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active
            
            add_action( 'init', array( &$this, 'check_active' ), 10, 1 );

			if( !$this->addon_is_active() ) return;

            add_action( 'user_register', array( &$this, 'track_conversion_submission' ), 10, 1 );

            add_filter( $this->conversion_submission_indicator, array( &$this, 'filter_conversion_list' ), 10, 1 );

            // filter en post


        } // End __construct


        /************************ PRIVATE METHODS *****************************/





		/************************ PUBLIC METHODS  *****************************/


        public function check_active()
        {
            global $cp_appb_plugin;
            if (is_object($cp_appb_plugin) && !$this->addon_is_active())
            {
                global $cpreftrack_addons_active_list;
                $cpreftrack_addons_active_list[] = $this->get_addon_id();
                update_option( 'cpreftrack_addons_active_list', $cpreftrack_addons_active_list );
            }
        }
        

        public function get_addon_settings()
        {
            global $cp_appb_plugin;
            // none here            
        }


		public function track_conversion_submission($user_id)
		{
            do_action( 'cpreftrack_register_conversion', $this->conversion_submission_indicator, $this->conversion_details_prefix.$user_id);
        }


		public function filter_conversion_list($param_registered)
		{
            global $wpdb, $cp_appb_plugin;
            $inum = intval(str_replace($this->conversion_details_prefix,'',$param_registered));
            if ($user = get_userdata( $inum ))
            {
                $param_registered = "<nobr>".$param_registered."</nobr><br /><b><span style=\"color:#FF9415\">User name: <a href=\"user-edit.php?user_id=".$inum."\">".esc_html($user->user_login)."</a></span></b>";
            }

            return $param_registered;
        }


    } // End Class

    // Main add-on code
    $CPREFTRACK_UserRegistration_obj = new CPREFTRACK_UserRegistration();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_UserRegistration_obj->get_addon_id() ] = $CPREFTRACK_UserRegistration_obj;
}
