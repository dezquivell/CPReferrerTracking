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

$message = "";

if (isset($_GET['delmark']) && $_GET['delmark'] != '')
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    for ($i=0; $i<=$records_per_page; $i++)
        if (isset($_GET['c'.$i]) && $_GET['c'.$i] != '')
            $wpdb->query( $wpdb->prepare("DELETE FROM `".$wpdb->prefix.$this->table_conversions."` WHERE id=%d,", intval($_GET['c'.$i])) );
    $message = "Marked items deleted";
}
else if (isset($_GET['del']) && $_GET['del'] == 'all')
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_conversions.'`');
    $message = "All items deleted";
}
else if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $this->verify_nonce ($_GET["anonce"], 'cpreftrack_actions_booking');
    $wpdb->query( $wpdb->prepare('DELETE FROM `'.$wpdb->prefix.$this->table_conversions.'` WHERE id=%d', intval($_GET['ld'])) );
    $message = "Item deleted";
}

$rawfrom = (isset($_GET["dfrom"]) ? sanitize_text_field($_GET["dfrom"]) : '');
$rawto = (isset($_GET["dto"]) ? sanitize_text_field(@$_GET["dto"]) : '');

$cond = '';
if (isset($_GET["search"]) && $_GET["search"] != '') 
{
    $search_value = sanitize_text_field($_GET["search"]);
    $cond .= " AND (convdesc like '%".esc_sql($search_value)."%' OR convname like '%".esc_sql($search_value)."%' OR referrer LIKE '%".esc_sql($search_value)."%' OR referrerlast LIKE '%".esc_sql($search_value)."%')";
}
else
    $search_value = '';

if ($rawfrom != '') $cond .= " AND (`time` >= '".esc_sql( date("Y-m-d",strtotime($rawfrom)))."')";
if ($rawto != '') $cond .= " AND (`time` <= '".esc_sql(date("Y-m-d",strtotime($rawto)))." 23:59:59')";

$events_query = "SELECT * FROM ".$wpdb->prefix.$this->table_conversions." WHERE 1=1 ".$cond." ORDER BY `time` DESC";
$events = $wpdb->get_results( $events_query );
$total_pages = ceil(count($events) / $records_per_page);

if ($message) echo "<div id='setting-error-settings_updated' class='updated'><h2>".esc_html($message)."</h2></div>";

$nonce = wp_create_nonce( 'cpreftrack_actions_booking' );

?>
<script type="text/javascript">
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_conversions&anonce=<?php echo $nonce; ?>&ld='+id+'&r='+Math.random();
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
        document.location = 'admin.php?page=<?php echo $this->menu_parameter; ?>_conversions&del=all&r='+Math.random()+'&anonce=<?php echo $nonce; ?>';
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

<h1><?php _e('Conversions','cp-referrer-and-conversions-tracking'); ?></h1>

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the main settings page','cp-referrer-and-conversions-tracking'); ?></a>
	<div class="clear"></div>
</div>

<div class="ahb-section-container">
	<div class="ahb-section">
      <form action="admin.php" method="get">
        <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>_conversions" />
		<nobr><label><?php _e('Search for','cp-referrer-and-conversions-tracking'); ?>:</label> <input type="text" name="search" value="<?php echo esc_attr($search_value); ?>">&nbsp;&nbsp;</nobr>
		<nobr><label><?php _e('From','cp-referrer-and-conversions-tracking'); ?>:</label> <input autocomplete="off" type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($rawfrom); ?>" >&nbsp;&nbsp;</nobr>
		<nobr><label><?php _e('To','cp-referrer-and-conversions-tracking'); ?>:</label> <input autocomplete="off" type="text" id="dto" name="dto" value="<?php echo esc_attr($rawto); ?>" >&nbsp;&nbsp;</nobr>
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
    'base'         => 'admin.php?page='.$this->menu_parameter.'_conversions&%_%&dfrom='.urlencode($rawfrom).'&dto='.urlencode($rawto).'&search='.urlencode($search_value),
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
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>_conversions" />
 <input type="hidden" name="delmark" value="1" />
 <input type="hidden" name="anonce" value="<?php echo $nonce; ?>" />
<div class="ahb-orderssection-container" style="background:#f6f6f6;padding-bottom:20px;">
<table border="0" style="width:100%;" class="ahb-orders-list" cellpadding="10" cellspacing="10">
	<thead>
	<tr>
      <th width="10"><input type="checkbox" name="cpcontrolck" id="cpcontrolck" value="" onclick="cp_markall();"></th>
      <th width="30"><?php _e('IP','cp-referrer-and-conversions-tracking'); ?></th>
	  <th style="text-align:left" width="130"><?php _e('Time','cp-referrer-and-conversions-tracking'); ?></th>
      <th style="text-align:left"><?php _e('Conversion','cp-referrer-and-conversions-tracking'); ?></th>
      <th style="text-align:left"><?php _e('Details','cp-referrer-and-conversions-tracking'); ?></th>
	  <th style="text-align:left"><?php _e('Referrer','cp-referrer-and-conversions-tracking'); ?></th>
	  <th  class="cpnopr"><?php _e('Options','cp-referrer-and-conversions-tracking'); ?></th>
	</tr>
	</thead>
	<tbody id="the-list">
    <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
	  <tr class='<?php if (($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
        <th><input type="checkbox" name="c<?php echo $i-($current_page-1)*$records_per_page; ?>" value="<?php echo $events[$i]->id; ?>" /></th>
        <th><?php echo $events[$i]->id; ?></th>
		<td><?php echo $events[$i]->time; ?></td>
        <td><?php echo htmlentities($events[$i]->convname); ?></td>
        <td><?php echo apply_filters( $events[$i]->convname, $events[$i]->convdesc ); ?></td>
		<?php $ref = htmlentities($events[$i]->referrer);
              $reflast = htmlentities($events[$i]->referrerlast);
                   if ($ref)
                   {
                       echo '<td>';
                       echo $ref;
                       if ($reflast!= '' && $ref != $reflast)
                           echo '<br /><br /><span style="color:#aaaaaa"><b>Last referrer:</b> '.$reflast;
                       
                       $entry = isset($events[$i]->entry) ? $events[$i]->entry : '';
                       if (strlen($entry) > 33)
                           $entry = "...".substr($entry, strlen($entry) -30);
                       if ($entry != '')
                           echo '<br /><br /><strong>'.__('Entry page:','cp-referrer-and-conversions-tracking').'</strong><br><a href="'.esc_url($data["entry"]).'" target="_blank">'.esc_html($entry).'</a>'; 
              
                       echo '</td>';
                   }
                   else
                       echo '<td style="color: #cccccc">'.__('N/A - unable to find the referrer source','cp-referrer-and-conversions-tracking').'</td>';
                   ?>
		<td class="cpnopr" style="text-align:center;">
		  <input class="button" type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem('<?php echo $events[$i]->id; ?>');" />
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

 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({
                    dateFormat: 'yy-mm-dd'
                 });
 });

</script>














