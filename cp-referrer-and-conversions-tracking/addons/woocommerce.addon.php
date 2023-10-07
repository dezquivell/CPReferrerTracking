<?php
/*
    iCal Import Addon
*/
require_once dirname( __FILE__ ).'/base.addon.php';

if( !class_exists( 'CPREFTRACK_WooCommerce' ) )
{

    class CPREFTRACK_WooCommerce extends CPREFTRACK_BaseAddon
    {

        /************* ADDON SYSTEM - ATTRIBUTES AND METHODS *************/
		protected $addonID = "addon-WooCommerce-20200119";
		protected $name = "WooCommerce Conversion Tracking";
		protected $description;
        protected $conversion_submission_indicator = 'WooCommerce - Order Placed';
        protected $conversion_details_prefix = 'Order ID #';


		/************************ ADDON CODE *****************************/

        /************************ ATTRIBUTES *****************************/


        /************************ CONSTRUCT *****************************/

        function __construct()
        {
			$this->description = __("The add-on enables referrer tracking for conversions in the <strong>WooCommerce</strong> plugin", 'cp-referrer-and-conversions-tracking' );
            // Check if the plugin is active

			if( !$this->addon_is_active() ) return;

			add_action( 'cpappb_process_data_before_insert', array( &$this, 'register_referrer' ), 10, 1);

            add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this, 'my_custom_checkout_field_display_admin_order_meta' ), 10, 1 );

            add_action( 'woocommerce_new_order', array( &$this, 'track_conversion_submission' ), 10, 1 );

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
        

		public function my_custom_checkout_field_display_admin_order_meta($order)
        {
            echo '<p><strong><a href="admin.php?page=cp_reftrack_conversions">'.__('Referrer').'</a>:</strong> <br/>' . get_post_meta( $order->get_id(), 'cpreftrack_woocommerce_referrer', true ) . '</p>';
        }

		public function track_conversion_submission($order_id)
		{
            $referrer = apply_filters( 'cpreftrack_referrer', '' );
            update_post_meta($order_id, 'cpreftrack_woocommerce_referrer', esc_attr(htmlspecialchars($referrer)));
            do_action( 'cpreftrack_register_conversion', $this->conversion_submission_indicator, $this->conversion_details_prefix.$order_id);
        }


		public function filter_conversion_list($param_registered)
		{
            global $wpdb;            
            $inum = intval(str_replace($this->conversion_details_prefix,'',$param_registered));
            $param_registered = "<nobr><a href=\"post.php?post=".$inum ."&action=edit\">".$param_registered."</a></nobr>";
            return $param_registered;
        }


    } // End Class

    // Main add-on code
    $CPREFTRACK_WooCommerce_obj = new CPREFTRACK_WooCommerce();

	// Add addon object to the objects list
	global $cpreftrack_addons_objs_list;
	$cpreftrack_addons_objs_list[ $CPREFTRACK_WooCommerce_obj->get_addon_id() ] = $CPREFTRACK_WooCommerce_obj;
}
