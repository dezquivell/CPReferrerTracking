<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$itemid = 1;

$current_user_access = current_user_can('manage_options');

global $wpdb, $cpreftrack_addons_active_list, $cpreftrack_addons_objs_list;

$message = "";

$cpreftrack_addons_active_list = get_option( 'cpreftrack_addons_active_list', array() );


if (isset($_GET["confirm"]))
    $message = 'Settings updated';

if ($message) echo "<div id='setting-error-settings_updated' class='updated'><h2>".$message."</h2></div>";

$nonce = wp_create_nonce( 'cpreftrack_actions_list' );
 

?>
<h1><?php echo esc_html($this->plugin_name); ?></h1>

<script type="text/javascript">
        
 
 function cp_viewMessages(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_list&r='+Math.random();
 } 
 
 function cp_viewReport(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_report&r='+Math.random();
 } 
 
 function cp_viewSources(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_parameters&r='+Math.random();
 } 
 
 function cp_conversions(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_conversions&r='+Math.random();
 }

 function cp_addons(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_addons&r='+Math.random();
 }
 
 function cp_support(id)
 {
    document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_support&r='+Math.random();
 } 
 
</script>


<div class="ahb-section-container">
	<div class="ahb-section row">
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calmessages_<?php echo $itemid; ?>" value="<?php _e('Tracking Logs','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_viewMessages(<?php echo $itemid; ?>);" />
                <div class="mt-2">Logs of every website visitor with identified referrer.</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calreport_<?php echo $itemid; ?>" value="<?php _e('Tracking Stats','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_viewReport(<?php echo $itemid; ?>);" />                 
                <div class="mt-2">Stats of visitors with idenfitied referrers</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calreport_<?php echo $itemid; ?>" value="<?php _e('Referral Sources','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_viewSources(<?php echo $itemid; ?>);" />                 
                <div class="mt-2">The purpose of this section is to create links for different marketing platforms, making easier to identify the referral.</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calreport_<?php echo $itemid; ?>" value="<?php _e('Conversions','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_conversions(<?php echo $itemid; ?>);" />                 
                <div class="mt-2">Conversions with their referrals. Example: form submissions, orders, purchases, bookings... Enable the add-ons of the related plugins to get the conversion tracking enabled for each plugin.</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calreport_<?php echo $itemid; ?>" value="<?php _e('Add Ons','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_addons(<?php echo $itemid; ?>);" />                 
                <div class="mt-2">Add-ons for registering conversions of specific plugins (contact forms, bookings forms, etc...). Enable the add-ons of the related plugins as needed.</div>
            </div>
        </div>        
        <div class="col-xl-2 col-lg-3 col-md-4 p-2">
            <div class="card h-100">
                <input class="button mt-2" type="button" name="calreport_<?php echo $itemid; ?>" value="<?php _e('Support','cp-referrer-and-conversions-tracking'); ?>" onclick="cp_support(<?php echo $itemid; ?>);" />    
                <div class="mt-2">Need help or want to request specific features? Contact us and we will be happy to help!</div>
            </div>
        </div>
        <div class="clearer"></div>
     
	</div>
</div>
  


 
<div id="normal-sortables" class="meta-box-sortables"> 

<?php if ($current_user_access) { ?> 


<?php
	if( count( $cpreftrack_addons_active_list ) )
	{	
		foreach( $cpreftrack_addons_active_list as $addon_id ) if( isset( $cpreftrack_addons_objs_list[ $addon_id ] ) ) print $cpreftrack_addons_objs_list[ $addon_id ]->get_addon_settings();
	}
?>  


 
<?php } ?>
  
</div> 


<div id="normal-sortables" class="meta-box-sortables" <?php if (!$current_user_access) echo ' style="display:none; " ' ?> > 


[<a href="https://wordpress.dwbooster.com/contact-us" target="_blank"><?php _e('Request Custom Modifications','cp-referrer-and-conversions-tracking'); ?></a>] | [<a href="<?php echo $this->plugin_URL; ?>" target="_blank"><?php _e('Help','cp-referrer-and-conversions-tracking'); ?></a>]
</form>
</div>