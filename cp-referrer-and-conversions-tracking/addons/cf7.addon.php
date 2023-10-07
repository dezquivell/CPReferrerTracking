<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_CF7' ) )
{

    class CPREFTRACK_CF7 extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-CF7-20200119";
		protected $name = "Contact Form 7 Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'Contact Form 7 - Message';
        protected $conversion_details_prefix = 'Email: ';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for conversions in the <strong>Contact Form 7</strong> plugin", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active

			if( !$this->addon_is_active() ) return;
            
            add_action( 'wpcf7_before_send_mail', array( &$this, 'track_conversion_submission' ), 10, 1 );

            add_filter( $this->conversion_submission_indicator, array( &$this, 'filter_conversion_list' ), 10, 1 );

            // filter en post


        } // End __construct



        /************************ PRIVATE METHODS *****************************/





		/************************ PUBLIC METHODS  *****************************/



        public function get_addon_settings()
        {
            global $cp_appb_plugin;
            // no code needed here
        }


		public function register_referrer(&$params)
		{
            // no code needed here
        }

		public function track_conversion_submission($contact_form)
		{
            $referrer = apply_filters( 'cpreftrack_referrer', '' );
            $mail = $contact_form->prop( 'mail' ); // returns array 
            $mail['body'] .= "\n\n".'Referrer: '. $referrer;     
            $contact_form->set_properties( array( 'mail' => $mail ) );
            $email = sanitize_email($mail['recipient']);     
            
            $submission = WPCF7_Submission::get_instance();  
            if ( $submission ) {
                $posted_data = $submission->get_posted_data();
                if (!empty($posted_data['your-email']))
                    $email .= ' Sent by: '.sanitize_email($posted_data['your-email']);
            }  
    
            do_action( 'cpreftrack_register_conversion', $this->conversion_submission_indicator, $this->conversion_details_prefix.$email);
        }


		public function filter_conversion_list($param_registered)
		{
            global $wpdb;            
            // no code needed here
            return $param_registered;
        }


    } // End Class

    // Main add-on code
    $CPREFTRACK_CF7_obj = new CPREFTRACK_CF7();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_CF7_obj->get_addon_id() ] = $CPREFTRACK_CF7_obj;
}
