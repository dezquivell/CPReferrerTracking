<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_AppointmentBookingCalendar' ) )
{

    class CPREFTRACK_AppointmentBookingCalendar extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-ABC-20200119";
		protected $name = "Appointment Booking Calendar Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'Appointment Booking Calendar - Form submission';
        protected $conversion_details_prefix = 'Submission ID #';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for conversions in the <a style=\"font-weight:bold\" href=\"plugin-install.php?tab=plugin-information&amp;plugin=appointment-booking-calendar&amp;\">Appointment Booking Calendar</a> plugin", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active

            add_action( 'init', array( &$this, 'check_active' ), 10, 1 );

			if( !$this->addon_is_active() ) return;

			add_action( 'cpabc_process_data_before_insert', array( &$this, 'register_referrer' ), 10, 1 );

            add_action( 'cpabc_process_data', array( &$this, 'track_conversion_submission' ), 10, 1 );

            add_filter( $this->conversion_submission_indicator, array( &$this, 'filter_conversion_list' ), 10, 1 );

            // filter en post


        } // End __construct



        /************************ PRIVATE METHODS *****************************/





		/************************ PUBLIC METHODS  *****************************/

        public function check_active()
        {
            if (defined('CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME') && !$this->addon_is_active())
            {
                global $cpreftrack_addons_active_list;
                $cpreftrack_addons_active_list[] = $this->get_addon_id();
                update_option( 'cpreftrack_addons_active_list', $cpreftrack_addons_active_list );
            }
        }

        public function get_addon_settings()
        {
            if (!defined('CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME'))
            {
                if(!file_exists(dirname(__FILE__).'/../../appointment-booking-calendar/cpabc_appointments.php'))
                {
?>
            <div class="card h-100" style="background-color: #ffdddd">
                <div class="mt-2"><strong><span style="color:red">Configuration issue:</span></strong> The <strong><?php echo $this->name; ?></strong> add-on has been enabled but the related plugin has not been detected. Please remember to install the related plugin to get listed its conversions.</div>

                <input class="button mt-2" type="button" name="calreport_<?php echo $item->id; ?>" value="<?php _e('Install Appointment Booking Calendar plugin','cp-referrer-and-conversions-tracking'); ?>" onclick="document.location='plugin-install.php?tab=plugin-information&amp;plugin=appointment-booking-calendar&amp;';" />
            </div>
<?php
                }
                else
                {
?>
            <div class="card h-100" style="background-color: #ffdddd">
                <div class="mt-2"><strong><span style="color:red">Configuration issue:</span></strong> The <strong><?php echo $this->name; ?></strong> add-on has been enabled but the related plugin is not active. Please remember to <a href="plugins.php">activate the related plugin</a> to get listed its conversions.</div>
            </div>
<?php
                }
            }
        }


		public function register_referrer(&$params)
		{
            $referrer = apply_filters( 'cpreftrack_referrer', '' );
            $params["referrer_url"] = $referrer;
            $params["referrer_url_latest"] = sanitize_text_field($_COOKIE['cprreftracklatest']);

        }


		public function track_conversion_submission($params)
		{
            do_action( 'cpreftrack_register_conversion', $this->conversion_submission_indicator, $this->conversion_details_prefix.$params["itemnumber"]);
        }


		public function filter_conversion_list($param_registered)
		{
            global $wpdb;
            if (!defined('CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME'))
                return $param_registered;
            $inum = intval(str_replace($this->conversion_details_prefix,'',$param_registered));
            $myrows = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME." WHERE reference=%d",$inum) );
            if (count($myrows))
            {
                $param_registered = "<nobr>".$param_registered."</nobr><br /><b><span style=\"color:#FF9415\">Email: <a href=\"?page=cpabc_appointments.php&cal=".$myrows[0]->appointment_calendar_id."&list=1&search=".urlencode($myrows[0]->title)."&dfrom&dto&ds=Filter\">".sanitize_email($myrows[0]->title)."</a></span></b><br />";
            }

            return $param_registered;
        }



    } // End Class

    // Main add-on code
    $CPREFTRACK_AppointmentBookingCalendar_obj = new CPREFTRACK_AppointmentBookingCalendar();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_AppointmentBookingCalendar_obj->get_addon_id() ] = $CPREFTRACK_AppointmentBookingCalendar_obj;
}
