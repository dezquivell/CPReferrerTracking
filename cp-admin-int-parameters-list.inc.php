<?php


$this->item = 1; //intval($_GET["cal"]);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');

$current_page = (isset($_GET["p"]) ? intval($_GET["p"]) : 1);
if (!$current_page) $current_page = 1;
$records_per_page = 50;

if ( !is_admin() || !$current_user_access)
{
    echo 'Direct access not allowed.';
    exit;
}

$rawfrom = (isset($_GET["dfrom"]) ? sanitize_text_field($_GET["dfrom"]) : '');
$rawto = (isset($_GET["dto"]) ? sanitize_text_field(@$_GET["dto"]) : '');

$message = "";

if (isset($_POST['additem']) && $_POST['additem'] != '')
{
    $this->verify_nonce ($_POST["anonce"], 'cpreftrack_actions_booking');
    $wpdb->insert( $wpdb->prefix.$this->table_items, array( 
                                      'paramname' => stripcslashes(sanitize_text_field($_POST["paramname"])),
                                      'paramvalue' => stripcslashes(sanitize_text_field($_POST["paramvalue"])),
                                      'refname' => stripcslashes(sanitize_text_field($_POST["refname"])),
                                      )
                                      );
    $message = "Item added";
}
else if (isset($_GET['delmark']) && $_GET['delmark'] != '')
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    for ($i=0; $i<=$records_per_page; $i++)
    if (isset($_GET['c'.$i]) && $_GET['c'.$i] != '')
        $wpdb->query( $wpdb->prepare("DELETE FROM `".$wpdb->prefix.$this->table_items."` WHERE id=%d", intval($_GET['c'.$i])) );       
    $message = "Marked items deleted";
}
else if (isset($_GET['del']) && $_GET['del'] == 'all')
{    
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_items.'`');                    
    $message = "All items deleted";
} 
else if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    $wpdb->query( $wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_items.'` WHERE id=%d', intval($_GET['ld'])) );
    $message = "Item deleted";
}


$events_query = "SELECT * FROM ".$wpdb->prefix.$this->table_items ;

$cond = '';
if (isset($_GET["search"]) && $_GET["search"] != '') 
{
    $search_value = sanitize_text_field($_GET["search"]);
    $events_query  = $wpdb->prepare( $events_query. " WHERE (refname like '%%s%' OR paramname like '%%s%' OR paramvalue LIKE '%%s%')", $search_value, $search_value, $search_value );
}
else
    $search_value = '';

$events_query .= " ORDER BY `refname` DESC";;


/**
 * Allows modify the query of messages, passing the query as parameter
 * returns the new query
 */
$events = $wpdb->get_results( $events_query );
$total_pages = ceil(count($events) / $records_per_page);


if ($message) echo "<div id='setting-error-settings_updated' class='updated'><h2>".$message."</h2></div>";

$nonce = wp_create_nonce( 'cpreftrack_actions_booking' );

?>
<script type="text/javascript">
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_parameters&anonce=<?php echo $nonce; ?>&ld='+id+'&r='+Math.random();
    }
 }
 function cp_deletemarked()
 {
    if (confirm('Are you sure that you want to delete the marked items?')) 
        document.dex_table_form.submit();
 }  
 function cp_deleteall()
 {
    if (confirm('Are you sure that you want to delete ALL items for this form?'))
    {        
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_parameters&del=all&r='+Math.random()+'&anonce=<?php echo $nonce; ?>';
    }    
 }
 function cp_markall()
 {
     var ischecked = document.getElementById("cpcontrolck").checked;
     <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
     document.forms.dex_table_form.c<?php echo $i-($current_page-1)*$records_per_page; ?>.checked = ischecked;
     <?php } ?>
 } 
</script>

<h1><?php _e('Tracking Parameters to identify Referrals','cp-referrer-and-conversions-tracking'); ?></h1>


 <div class="ahb-section-container">
	<div class="ahb-section">
    <form name="dex_table_form" id="dex_table_formadd" action="" method="post">
     <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>_parameters" />
     <input type="hidden" name="additem" value="1" />
     <input type="hidden" name="anonce" value="<?php echo $nonce; ?>" />
        <span style="font-size:130%">* The purpose is to use a link like the following for different marketing platforms, so making easier to identify the source referral:<br />
        <div style="margin:5px; padding:10px; background-color: #ffffcc; border: 1px dotted black; color:blue; font-weight:bold"><?php echo $this->get_site_url();?>?<span style="color:red">source</span>=<span style="color:red">fromfacebook</span></div></span><br /><br />
        
		<label><?php _e('Add New Referral Parameter','cp-referrer-and-conversions-tracking'); ?></label><br />
		<input style="margin-bottom:3px;" type="text" name="refname" id="refname" placeholder=" - <?php _e('Referral Name, example: &quot;Facebook&quot;','cp-referrer-and-conversions-tracking'); ?> - " class="ahb-new-calendar" />
        <input style="margin-bottom:3px;" type="text" name="paramname" id="paramname" placeholder=" - <?php _e('Referral Parameter, example: &quot;source&quot;','cp-referrer-and-conversions-tracking'); ?> - " class="ahb-new-calendar" />
        <input style="margin-bottom:3px;" type="text" name="paramvalue" id="paramvalue" placeholder=" - <?php _e('Referral Parameter Value, example: &quot;fromfacebook&quot;','cp-referrer-and-conversions-tracking'); ?> - " class="ahb-new-calendar" /><br />
		<input style="margin-bottom:3px;" type="submit" class="button-primary" value="<?php _e('Add New','cp-referrer-and-conversions-tracking'); ?>"  />
        <br />
     </form>
	</div>
</div>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>

<div class="ahb-section-container">
	<div class="ahb-section">
      <form action="admin.php" method="get">
        <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>_parameters" />
		<nobr><label><?php _e('Search for','cp-referrer-and-conversions-tracking'); ?>:</label> <input type="text" name="search" value="<?php echo esc_attr($search_value); ?>">&nbsp;&nbsp;</nobr>		
       <div style="float:right">
		<nobr>         
            <input type="submit" name="ds" value="<?php _e('Filter','cp-referrer-and-conversions-tracking'); ?>" class="button-primary button" style="">	
		</nobr>
       </div> 
      </form>
	</div>
</div>


<?php


echo paginate_links(  array(
    'base'         => 'admin.php?page='.$this->menu_parameter.'_parameters&%_%&dfrom='.urlencode($rawfrom).'&dto='.urlencode($rawto).'&search='.urlencode($search_value),
    'format'       => '&p=%#%',
    'total'        => $total_pages,
    'current'      => $current_page,
    'show_all'     => False,
    'end_size'     => 1,
    'mid_size'     => 2,
    'prev_next'    => True,
    'prev_text'    => __('&laquo; Previous'),
    'next_text'    => __('Next &raquo;'),
    'type'         => 'plain',
    'add_args'     => False
    ) );

?>

<div id="dex_printable_contents" style="overflow:visible !important;"> 
<form name="dex_table_form" id="dex_table_form" action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>_parameters" />
 <input type="hidden" name="delmark" value="1" />
 <input type="hidden" name="anonce" value="<?php echo $nonce; ?>" />
<div class="ahb-orderssection-container" style="background:#f6f6f6;padding-bottom:20px;">
<table border="0" style="width:100%;" class="ahb-orders-list" cellpadding="10" cellspacing="10">
	<thead>
	<tr>
      <th width="10"><input type="checkbox" name="cpcontrolck" id="cpcontrolck" value="" onclick="cp_markall();"></th>
      <th style="text-align:left"><?php _e('Referral Name','cp-referrer-and-conversions-tracking'); ?></th>
	  <th style="text-align:left"><?php _e('Parameter Name','cp-referrer-and-conversions-tracking'); ?></th>
	  <th style="text-align:left"><?php _e('Parameter Value','cp-referrer-and-conversions-tracking'); ?></th>
      <th style="text-align:left"><?php _e('Sample Link','cp-referrer-and-conversions-tracking'); ?></th>
	  <th  class="cpnopr"><?php _e('Options','cp-referrer-and-conversions-tracking'); ?></th>
	</tr>
	</thead>
	<tbody id="the-list">
	 <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>    
	  <tr class='<?php if (($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
        <th><input type="checkbox" name="c<?php echo $i-($current_page-1)*$records_per_page; ?>" value="<?php echo $events[$i]->id; ?>" /></th>
        <td><?php echo esc_html($events[$i]->refname); ?></td>
        <td><?php echo esc_html($events[$i]->paramname); ?></td>
		<td><?php echo esc_html($events[$i]->paramvalue); ?></td>
        <td><a href="<?php echo $this->get_site_url();?>?<?php echo urlencode($events[$i]->paramname); ?>=<?php echo urlencode($events[$i]->paramvalue); ?>"><?php echo $this->get_site_url();?>?<span style="color:red"><?php echo urlencode($events[$i]->paramname); ?></span>=<span style="color:red"><?php echo urlencode($events[$i]->paramvalue); ?></span></a></td>
		<td class="cpnopr" style="text-align:center;">        
		  <input class="button" type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />
		</td>
      </tr>
     <?php } ?>
	</tbody>
</table>
</div>
</form>
</div>

<div class="ahb-buttons-container">
    <input type="button" value="Print" class="button button-primary" onclick="do_dexapp_print();" />
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>

<div style="clear:both"></div>
<p class="submit" style="float:left;"><input class="button" type="button" name="pbutton" value="Delete marked items" onclick="cp_deletemarked();" /> &nbsp; &nbsp; &nbsp; </p>
<p class="submit" style="float:left;"><input class="button" type="button" name="pbutton" value="Delete All items" onclick="cp_deleteall();" /></p>
<div style="clear:both"></div>


<script type="text/javascript">
 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();
 }

</script>














