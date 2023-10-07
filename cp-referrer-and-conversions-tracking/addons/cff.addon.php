<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_CalculatedFieldsForm' ) )
{

    class CPREFTRACK_CalculatedFieldsForm extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-CalculatedFieldsForm-20200119";
		protected $name = "Calculated Fields Form Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'Calculated Field Form - Form submission';
        protected $conversion_details_prefix = 'Submission ID #';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for conversions in the <a style=\"font-weight:bold\" href=\"plugin-install.php?tab=plugin-information&amp;plugin=calculated-fields-form&amp;\">Calculated Fields Form</a> plugin", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active
            
            //$this->check_active();

			if( !$this->addon_is_active() ) return;

			add_action( 'cpcff_process_data_before_insert', array( &$this, 'register_referrer' ), 10, 3 );

            add_action( 'cpcff_process_data', array( &$this, 'track_conversion_submission' ), 10, 1 );

            add_filter( $this->conversion_submission_indicator, array( &$this, 'filter_conversion_list' ), 10, 1 );

            // filter en post


        } // End __construct



        /************************ PRIVATE METHODS *****************************/





		/************************ PUBLIC METHODS  *****************************/



        public function get_addon_settings()
        {
            // check here if the CFF is not installed and provide link to install... check other addons for sample
        }


		public function register_referrer(&$params, &$str, $fields )
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
            $inum = intval(str_replace($this->conversion_details_prefix,'',$param_registered));

            // check here if CFF is active and add a link to the submissions list or something like that

            return $param_registered;
        }



    } // End Class

    // Main add-on code
    $CPREFTRACK_CalculatedFieldsForm_obj = new CPREFTRACK_CalculatedFieldsForm();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_CalculatedFieldsForm_obj->get_addon_id() ] = $CPREFTRACK_CalculatedFieldsForm_obj;
}
