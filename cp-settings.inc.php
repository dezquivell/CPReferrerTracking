<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$nonce = wp_create_nonce( 'cpreftrack_actions_admin' );

$cpid = 'CP_REFTRACK';

$gotab = '';
if (isset($_POST["gotab"]))
{
    $gotab = sanitize_text_field($_POST["gotab"]);
    if ($gotab == '')
        $message = 'Settings updated.';
}
else
    if (isset($_GET["gotab"]))
        $gotab = sanitize_text_field($_GET["gotab"]);


?>
<style>
	.ahb-tab{display:none;}
	.ahb-tab label{font-weight:600;}
	.tab-active{display:block;}
	.ahb-code-editor-container{border:1px solid #DDDDDD;margin-bottom:20px;}

.ahb-csssample { margin-top: 15px; margin-left:20px;  margin-right:20px;}
.ahb-csssampleheader {
  font-weight: bold;
  background: #dddddd;
	padding:10px 20px;-webkit-box-shadow: 0px 2px 2px 0px rgba(100, 100, 100, 0.1);-moz-box-shadow:    0px 2px 2px 0px rgba(100, 100, 100, 0.1);box-shadow:         0px 2px 2px 0px rgba(100, 100, 100, 0.1);
}
.ahb-csssamplecode {     background: #f4f4f4;
    border: 1px solid #ddd;
    border-left: 3px solid #f36d33;
    color: #666;
    page-break-inside: avoid;
    font-family: monospace;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 1.6em;
    max-width: 100%;
    overflow: auto;
    padding: 1em 1.5em;
    display: block;
    word-wrap: break-word;
}
</style>
<h1><?php _e('CP Referrer Tracking - General Settings','cp-referrer-and-conversions-tracking'); ?></h1>

<?php
    if ($message) echo "<div id='setting-error-settings_updated' class='updated'><h2>".esc_html(message)."</h2></div>";
?>
<nav class="nav-tab-wrapper ahb-tab-wrapper">
	<a href="javascript:void(0);" class="nav-tab<?php if ($gotab == '') echo ' nav-tab-active'; ?>" data-tab="1"><?php _e('General Settings','cp-referrer-and-conversions-tracking'); ?></a>	
</nav>

<!-- TAB 1 -->
<div class="ahb-tab<?php if ($gotab == '') echo ' tab-active'; ?>" data-tab="1">
	
	<form name="updatereportsettings" action="" method="post">
     <input name="nonce" type="hidden" value="<?php echo $nonce; ?>" />
     <input name="<?php echo $cpid; ?>_post_edition" type="hidden" value="1" />
     <input name="gotab" type="hidden" value="" />
     <table class="form-table">
        <tr valign="top">
        <td scope="row" colspan="2"><strong><?php _e('Enable Referrer Tracking?','cp-referrer-and-conversions-tracking'); ?></strong>
          <?php $option = get_option('cp_cpreftrack_rep_enable', ''); ?>
          <select name="cp_cpreftrack_rep_enable">
           <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>><?php _e('No','cp-referrer-and-conversions-tracking'); ?></option>
           <option value=""<?php if ($option == '') echo ' selected'; ?>><?php _e('Yes','cp-referrer-and-conversions-tracking'); ?></option>
          </select>
          <br /><br />
          <strong><?php _e('Delete logs older than','cp-referrer-and-conversions-tracking'); ?>:</strong> <input type="text" name="cp_cpreftrack_rep_days" size="1" value="<?php echo esc_attr(get_option('cp_cpreftrack_rep_days', '90')); ?>" /> <?php _e('days','cp-referrer-and-conversions-tracking'); ?>
          &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;          
        </td>       
     </table>
     <input type="submit" value="Update Settings" class="button button-primary" />
     </form>
     <div class="clear"></div>

</div>


