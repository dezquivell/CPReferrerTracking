<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

global $wpdb, $cpreftrack_addons_active_list, $cpreftrack_addons_objs_list;

$message = "";

if( isset( $_GET[ 'b' ] ) && $_GET[ 'b' ] == 1 )
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_list');
	// Save the option for active addons
	delete_option( 'cpreftrack_addons_active_list' );
	if( !empty( $_GET[ 'cpreftrack_addons_active_list' ] ) && is_array( $_GET[ 'cpreftrack_addons_active_list' ] ) ) 
	{
        foreach ($_GET[ 'cpreftrack_addons_active_list' ] as $item)
             $sanitized_list[] = sanitize_text_field($item);
		update_option( 'cpreftrack_addons_active_list', $sanitized_list );
	}	
	
	// Get the list of active addons
	$cpreftrack_addons_active_list = get_option( 'cpreftrack_addons_active_list', array() );
    $message = "Add Ons settings updated";
}

$nonce = wp_create_nonce( 'cpreftrack_actions_list' );

?>
<style>
	.clear{clear:both;}
	.ahb-addons-container {
		border: 1px solid #e6e6e6;
		padding: 20px;
		border-radius: 3px;
		-webkit-box-flex: 1;
		flex: 1;
		margin: 1em 1em 1em 0;
		min-width: 200px;
		background: white;
		position:relative;
	}
	.ahb-addons-container h2{margin:0 0 20px 0;padding:0;}
	.ahb-addon{border-bottom: 1px solid #efefef;padding: 10px 0;}
	.ahb-addon:first-child{border-top: 1px solid #efefef;}
	.ahb-addon:last-child{border-bottom: 0;}
	.ahb-addon label{font-weight:600;}
	.ahb-addon p{font-style:italic;margin:5px 0 0 0;}
	.ahb-first-button{margin-right:10px !important;}
    
    .ahb-buttons-container{margin:1em 1em 1em 0;}
    .ahb-return-link{float:right;}

	.ahb-disabled-addons {
		background: #f9f9f9;
	}
	.ahb-addons-container h2{margin-left:30px;}
	.ahb-disabled-addons *{
		color:#888888;
	}
	.ahb-disabled-addons input{
		pointer-events: none !important;
	}

	/** For Ribbon **/
	.ribbon {
		position: absolute;
		left: -5px; top: -5px;
		z-index: 1;
		overflow: hidden;
		width: 75px; height: 75px;
		text-align: right;
	}
	.ribbon span {
		font-size: 10px;
		font-weight: bold;
		color: #FFF;
		text-transform: uppercase;
		text-align: center;
		line-height: 20px;
		transform: rotate(-45deg);
		-webkit-transform: rotate(-45deg);
		width: 100px;
		display: block;
		background: #79A70A;
		background: linear-gradient(#2989d8 0%, #1e5799 100%);
		box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
		position: absolute;
		top: 19px; left: -21px;
	}
	.ribbon span::before {
		content: "";
		position: absolute; left: 0px; top: 100%;
		z-index: -1;
		border-left: 3px solid #1e5799;
		border-right: 3px solid transparent;
		border-bottom: 3px solid transparent;
		border-top: 3px solid #1e5799;
	}
	.ribbon span::after {
		content: "";
		position: absolute; right: 0px; top: 100%;
		z-index: -1;
		border-left: 3px solid transparent;
		border-right: 3px solid #1e5799;
		border-bottom: 3px solid transparent;
		border-top: 3px solid #1e5799;
	}
</style>

<script type="text/javascript">
    
 function cp_activateAddons()
 {
    var cpreftrack_addons = document.getElementsByName("cpreftrack_addons"),
		cpreftrack_addons_active_list = [];
	for( var i = 0, h = cpreftrack_addons.length; i < h; i++ )
	{
		if( cpreftrack_addons[ i ].checked ) cpreftrack_addons_active_list.push( 'cpreftrack_addons_active_list[]='+encodeURIComponent( cpreftrack_addons[ i ].value ) );
	}	
	document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_addons&anonce=<?php echo $nonce; ?>&b=1&r='+Math.random()+( ( cpreftrack_addons_active_list.length ) ? '&'+cpreftrack_addons_active_list.join( '&' ) : '' )+'#addons-section';
 }    
 
</script>

<a id="top"></a>

<h1><?php _e('CP Referrer Tracking - Add Ons','cp-referrer-and-conversions-tracking'); ?></h1>

<?php if ($message) echo "<div id='setting-error-settings_updated' class='updated' style='margin:0px;'><h2>".esc_html($message)."</h2></div> <br />";
 ?>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>


<input type="button" value="Activate/Deactivate Marked Add Ons" onclick="cp_activateAddons();" class="button button-primary ahb-first-button" />
<div class="clear"></div>

<!-- Add Ons -->
<h2><?php _e('Active Add Ons','cp-referrer-and-conversions-tracking'); ?></h2>
<div class="ahb-addons-container">
	<div class="ahb-addons-group">

	<?php
    $i=0;
	foreach( $cpreftrack_addons_objs_list as $key => $obj )
	{
		print '<div class="ahb-addon" style="border:0;width:50%;float:left;"><label><input type="checkbox" id="'.$key.'" name="cpreftrack_addons" value="'.$key.'" '.( ( $obj->addon_is_active() ) ? 'CHECKED' : '' ).'>'.$obj->get_addon_name().'</label><p>'.$obj->get_addon_description().'</p></div>';
        $i++;
        if (!($i%2)) echo '<div class="clear"></div>';
	}
    if ($i == 0) echo __('No add-ons registered so far.','cp-referrer-and-conversions-tracking');
	?>    
    <div class="clear"></div>
	</div>
</div>

<div class="ahb-to-top" style="margin-bottom:10px;"><a href="#top">&uarr; <?php _e('Top','cp-referrer-and-conversions-tracking'); ?></a></div>

<input type="button" value="Activate/Deactivate Marked Add Ons" onclick="cp_activateAddons();" class="button button-primary ahb-first-button" />
<div class="clear"></div>