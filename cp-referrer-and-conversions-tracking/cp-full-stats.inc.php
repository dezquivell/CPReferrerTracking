<?php

global $wpdb;

$this->item = 1; //intval($_GET["cal"]);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');

if ( !is_admin() || !$current_user_access )
{
    echo 'Direct access not allowed.';
    exit;
}

// pre-select time-slots
$selection = array();
$rows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." ORDER BY time DESC" );

$yearly_incoming = array();
$monthly_incoming = array();
$weekly_incoming = array();
$daily_incoming = array();

$currentdate = time();
foreach($rows as $item)
{        
    $dt_incoming = strtotime($item->time);
    $yearly_incoming["x".date("Y",$dt_incoming)]++;
    $monthly_incoming["x".date("Ym",$dt_incoming)]++;
    $weekly_incoming["x".date("YW",$dt_incoming)]++;
    $daily_incoming["x".date("Ymd",$dt_incoming)]++;                    
}

function getStatsBy($arr)
{
    $str = '';
    foreach($arr as $key => $value)
       $str .= '<div><b>'.substr($key,1).'</b> '.$value.' logs </div>';   
    return $str;  
}
function getStatsByMonthly($arr, $is_incoming = false)
{
    $str = '';
    $dt = ($is_incoming ? strtotime("-11 months") : time());
    for ($i=0;$i<12;$i++)
    {
        $key = "x".date("Ym",$dt);
        $str .= '<div><b>'.date("Y M",$dt).'</b> '.(isset($arr[$key])?$arr[$key]:0).' logs </div>'; 
        $dt = strtotime( "+1 month" ,$dt);    
    }   
    return $str;  
}
function getStatsByWeekly($arr, $is_incoming = false)
{
    $str = '';
    $dt = ($is_incoming ? strtotime("-11 weeks") : time());
    for ($i=0;$i<12;$i++)
    {
        $key = "x".date("YW",$dt);
        $str .= '<div><b>'.date("Y W",$dt).'</b> '.(isset($arr[$key])?$arr[$key]:'0').' logs </div>';         
        $dt = strtotime("+1 week",$dt);    
    }   
    return $str;  
}
function getStatsByDaily($arr, $is_incoming = false)
{
    $str = '';
    $dt = ($is_incoming ? strtotime("-29 days") : time());
    for ($i=0;$i<30;$i++)
    {
        $key = "x".date("Ymd",$dt);
        $str .= '<div><b>'.date("Y M d",$dt).'</b> '.(isset($arr[$key])?$arr[$key]:'0').' logs </div>'; 
        $dt = strtotime("+1 day",$dt);    
    }   
    return $str;  
}

echo '<div class="ahb-section-container"><table id="cTable" width="100%">';
echo '<tr><th colspan="4" style="background-color:#b0b0b0">Logs with referrers stats (date in which the log was registered)</th></tr>';
echo '<tr><th>Yearly Stats</th><th>Incoming - Monthly Stats (lastest 12 months)</th><th>Incoming - Weekly Stats (lastest 12 weeks)</th><th>Incoming - Daily Stats (lastest 30 days)</th></tr>';
echo '<tr><td>'.getStatsBy($yearly_incoming).'</td><td>'.getStatsByMonthly($monthly_incoming, true).'</td><td>'.getStatsByWeekly($weekly_incoming, true).'</td><td>'.getStatsByDaily($daily_incoming, true).'</td></tr>';
echo '</table></div>';



?>
<style>
#cTable th{background:#ccc}
#cTable td{vertical-align:top}
</style>