<?php

class CP_REFTRACK_Plugin extends CP_REFTRACK_BaseClass {

    private $menu_parameter = 'cp_reftrack';
    public $prefix = 'cp_reftrack';
    private $plugin_name = 'CP Referrer Tracking';
    private $componentid = 160;      
    private $plugin_URL = 'http://wordpress.dwbooster.com/';
    public $table_items = "cpreftrack_registeredrefs";
    public $table_messages = "cpreftrack_logs";
    public $table_conversions = "cpreftrack_conversions";
    public $no_ref_indicator = "cprefna37193";
    public $cookie_tracking_days = 1;
  

    function _install() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');       

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_messages."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_messages." (
                id VARCHAR(40) DEFAULT '' NOT NULL,           
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,                
                referrer VARCHAR(250) DEFAULT '' NOT NULL,
                data mediumtext,                
                UNIQUE KEY id (id)
            );";
            $wpdb->query($sql);
        }

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_items."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_items." (
                 id mediumint(9) NOT NULL AUTO_INCREMENT,

                 paramname VARCHAR(250) DEFAULT '' NOT NULL,
                 paramvalue VARCHAR(250) DEFAULT '' NOT NULL,
                 refname VARCHAR(250) DEFAULT '' NOT NULL,            

                 UNIQUE KEY id (id)
            );";
            $wpdb->query($sql);
        }

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_conversions."'");
        if (!count($results))
        {         
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_conversions." (
                id mediumint(9) NOT NULL AUTO_INCREMENT,      
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,                                
                convname VARCHAR(250) DEFAULT '' NOT NULL,            
                convdesc mediumtext,                
                referrer VARCHAR(250) DEFAULT '' NOT NULL,
                entry VARCHAR(250) DEFAULT '' NOT NULL,
                referrerlast VARCHAR(250) DEFAULT '' NOT NULL,
                UNIQUE KEY id (id)
            );";
            $wpdb->query($sql);
        }
        
    }


    /* Code for the admin area */

    public function plugin_page_links($links) {
        $customAdjustments_link = '<a href="http://wordpress.dwbooster.com/contact-us">'.__('Request custom changes').'</a>';
    	array_unshift($links, $customAdjustments_link);
        $settings_link = '<a href="admin.php?page='.$this->menu_parameter.'">'.__('Settings').'</a>';
    	array_unshift($links, $settings_link);
    	$help_link = '<a href="'.$this->plugin_URL.'">'.__('Help').'</a>';
    	array_unshift($links, $help_link);
    	return $links;
    }


    public function admin_menu() {
        add_options_page($this->plugin_name.' Options', $this->plugin_name, 'manage_options', $this->menu_parameter, array($this, 'settings_page') );
        add_menu_page( $this->plugin_name.' Options', $this->plugin_name, 'read', $this->menu_parameter, array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'General Settings', 'General Settings', 'edit_pages', $this->menu_parameter."_settings", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Tracking Logs', 'Tracking Logs', 'edit_pages', $this->menu_parameter."_list", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Tracking Stats', 'Tracking Stats', 'edit_pages', $this->menu_parameter."_report", array($this, 'settings_page') ); 
        add_submenu_page( $this->menu_parameter, 'Referral Sources', 'Referral Sources', 'edit_pages', $this->menu_parameter."_parameters", array($this, 'settings_page') );        
        add_submenu_page( $this->menu_parameter, 'Conversions', 'Conversions', 'edit_pages', $this->menu_parameter."_conversions", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Add Ons', 'Add Ons', 'edit_pages', $this->menu_parameter."_addons", array($this, 'settings_page') );               
        add_submenu_page( $this->menu_parameter, 'I Need Help', 'I Need Help', 'edit_pages', $this->menu_parameter."_support", array($this, 'settings_page') );        
    }


    public function settings_page() {
        global $wpdb;
        if ($this->get_param("page") == $this->menu_parameter.'_conversions')
            @include_once dirname( __FILE__ ) . '/cp-admin-int-conversions-list.inc.php';        
        else if ($this->get_param("page") == $this->menu_parameter.'_parameters')
            @include_once dirname( __FILE__ ) . '/cp-admin-int-parameters-list.inc.php';        
        else if ($this->get_param("page") == $this->menu_parameter.'_list')
            @include_once dirname( __FILE__ ) . '/cp-admin-int-message-list.inc.php';
        else if ($this->get_param("page") == $this->menu_parameter.'_report')
            @include_once dirname( __FILE__ ) . '/cp-admin-int-report.inc.php';
        else if ($this->get_param("page") == $this->menu_parameter.'_support')
        {
            echo("Redirecting to support page...<script type='text/javascript'>document.location='https://wordpress.dwbooster.com/contact-us';</script>");
            exit;
        }   
        else if ($this->get_param("page") == $this->menu_parameter.'_settings')
        {
            @include_once dirname( __FILE__ ) . '/cp-settings.inc.php';
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_addons')
        {
            @include_once dirname( __FILE__ ) . '/cp-addons.inc.php';
        }
        else
            @include_once dirname( __FILE__ ) . '/cp-admin-int-list.inc.php';
    }

   
    function insert_adminScripts($hook) {
        if (substr($this->get_param("page"),0,strlen($this->menu_parameter)) == $this->menu_parameter)
        {
            wp_enqueue_style('jquery-style', plugins_url('/css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__));
            wp_enqueue_style('cpapp-style', plugins_url('/css/style.css', __FILE__));
            wp_enqueue_style('cpapp-newadminstyle', plugins_url('/css/newadminlayout.css', __FILE__)); 

            wp_deregister_script( 'bootstrap-datepicker-js' );
            wp_register_script('bootstrap-datepicker-js', plugins_url('/js/nope.js', __FILE__));
            wp_deregister_script( 'wpsp_wp_admin_jquery7' );
            wp_register_script('wpsp_wp_admin_jquery7', plugins_url('/js/nope.js', __FILE__));
            
            wp_enqueue_script( "jquery-ui-datepicker", array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button","query-stringify") );            
            
        }
        if( 'post.php' != $hook  && 'post-new.php' != $hook )
            return;
        // space to include some script in the post or page areas if needed
    }

   
  
   
    function data_management() {
        global $wpdb;
     
        load_plugin_textdomain( 'cp-referrer-and-conversions-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        

        if ($this->get_param($this->prefix.'_csv') && is_admin() && current_user_can('manage_options'))
        {
            $this->export_csv();
            return;
        }        
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['CP_REFTRACK_post_edition'] ) && is_admin() && current_user_can('edit_posts') )
        {
            $this->save_edition();
            return;
        }          

        $referrer_browser = (isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:'');
        
        $entry_page = (isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:'');
 
        $local_domain = strtolower($this->get_site_url());
        $local_domain = str_replace ('//www.', '//', strtolower($local_domain) );
        $local_domain = str_replace ('https://', '', strtolower($local_domain) );
        $local_domain = str_replace ('http://', '', strtolower($local_domain) );
        
        $compare_ref = str_replace ('//www.', '//', strtolower($referrer_browser) );
        $compare_ref = str_replace ('https://', '', strtolower($compare_ref) );
        $compare_ref = str_replace ('http://', '', strtolower($compare_ref) );          
        
        // register IP if needed
        if ( (isset($_COOKIE['cprreftrack']) && $_COOKIE['cprreftrack'] != 'na')  || 
             (isset($_COOKIE['cprreftrack']) && $_COOKIE['cprreftrack'] == $this->no_ref_indicator && @$_SERVER["HTTP_REFERER"] == '' && !count($_GET)) 
           )
           {
                if ($referrer_browser != '' && (substr($compare_ref."/",0,strlen($local_domain)) != $local_domain) )
                    setCookie('cprreftracklatest', $referrer_browser, time()+( $this->cookie_tracking_days * 24 * 60 * 60),"/");
                return; // nothing new to add           
           }
        
        $referrer = '';
        // load registered parameters
        
        $found = false;
        $top = 0;
        $events_query = "SELECT * FROM ".$wpdb->prefix.$this->table_items;
        $events = $wpdb->get_results( $events_query );
        while (!$found && $top < count($events))
        {
            if (isset($_GET[$events[$top]->paramname]) && $_GET[$events[$top]->paramname] == $events[$top]->paramvalue)
            {
                $referrer = $events[$top]->refname;
                $found = true;
            }
            $top++;
        }
        
        if ($referrer == '')
        {
            if (substr($compare_ref."/",0,strlen($local_domain)) == $local_domain) 
                return;  // exclude local domain from reports
            
            $referrer = $referrer_browser;
        }
        
        // set proper cookie
        if ($referrer == '')
        {
            setCookie('cprreftrack', $this->no_ref_indicator, time()+( $this->cookie_tracking_days * 24 * 60 * 60),"/");
            return;
        }
        else
        {            
            if (get_option('cp_cpreftrack_rep_enable', '') != '')  // if tracking disabled
                return;
            
            $remote_addr = sanitize_text_field($_SERVER["REMOTE_ADDR"]);
            
            $logs = $wpdb->get_results($wpdb->prepare ("SELECT referrer FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%s", $remote_addr));
            if (count($logs)) // if exists keep the original referrer
            {
                setCookie('cprreftrack', $logs[0]->referrer, time()+( $this->cookie_tracking_days * 24 * 60 * 60),"/");
                return;
            }
            setCookie('cprreftrack', $referrer, time()+( $this->cookie_tracking_days * 24 * 60 * 60),"/");
            // if new, store log
            $wpdb->insert( $wpdb->prefix.$this->table_messages, array( 'id' => $remote_addr,
                                                                       'time' => current_time('mysql'),
                                                                       'referrer' => sanitize_text_field($referrer),
                                                                       'data' => serialize(array('entry' => $entry_page))
                                                                       ));
        }
 
    }
    
    public function referrer_filter ($ref)
    {
        return $this->get_referrer();
    }
    
    
    public function get_referrer ($ip = '', $cookie = '')
    {
        global $wpdb;
        if ($ip == '')
            $ip = sanitize_text_field($_SERVER["REMOTE_ADDR"]);

        $logs = $wpdb->get_results($wpdb->prepare ("SELECT referrer FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%s", $ip));
        if (count($logs))
            return $logs[0]->referrer;
        else 
            return "";        
    }
    
    
    public function get_entry ($ip = '', $cookie = '')
    {
        global $wpdb;
        if ($ip == '')
            $ip = sanitize_text_field($_SERVER["REMOTE_ADDR"]);

        $logs = $wpdb->get_results($wpdb->prepare ("SELECT referrer FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%s", $ip));
        if (count($logs))
        {
            $data = unserialize($events[$i]->data);
            return (isset($data["entry"]) ? $data["entry"] : "");
        }
        else 
            return "";        
    }
    
    
    public function register_conversion($conversion_name, $conversion_description, $referrer = '')
    {
        global $wpdb;        
        if ($referrer == '')
            $referrer = $this->get_referrer();
        $entry = $this->get_entry();
        $this->add_field_verify($wpdb->prefix.$this->table_conversions, 'entry');
        $wpdb->insert( $wpdb->prefix.$this->table_conversions, array( 'convname' => $conversion_name,
                                                                       'convdesc' => $conversion_description,
                                                                       'referrer' => $referrer,
                                                                       'referrerlast' => sanitize_text_field(!empty($_COOKIE['cprreftracklatest']) && $referrer!=$_COOKIE['cprreftracklatest']?$_COOKIE['cprreftracklatest']:''),
                                                                       'entry' => $referrer,
                                                                       'time' => current_time('mysql'),
                                                                       ));                                                                         
    }
    
    public function delete_old_logs()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare ("DELETE FROM ".$wpdb->prefix.$this->table_messages." WHERE time<%s", date("Y-m-d H:i:s", strtotime("-".get_option('cp_cpreftrack_rep_days', '90')." days") )));
    }


    function save_edition()
    {        
        global $wpdb;
        
        $this->verify_nonce ($_POST["nonce"], 'cpreftrack_actions_admin');
         
        if (isset($_POST["gotab"]) && @$_POST["gotab"] == '')             
        {
            update_option( 'cp_cpreftrack_rep_enable', sanitize_text_field(stripcslashes($_POST["cp_cpreftrack_rep_enable"]))); 
            update_option( 'cp_cpreftrack_rep_days', sanitize_text_field(stripcslashes($_POST["cp_cpreftrack_rep_days"])));
        }
    }
    

    function export_csv ()
    {
        if (!is_admin())
            return;
        global $wpdb;
        
        $this->item = intval($this->get_param("cal"));

        $filename = $this->generateSafeFileName(strtolower($this->get_option('form_name','export'))).'_'.date("m_d_y");

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$filename.".csv");
        
        if ($this->item)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_APPBOOK_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $cond = '';
        if ($this->get_param("search")) $cond .= " AND (data like '%".esc_sql($this->get_param("search"))."%' OR posted_data LIKE '%".esc_sql($this->get_param("search"))."%')";
        if ($this->get_param("dfrom")) $cond .= " AND (`time` >= '".esc_sql($this->get_param("dfrom"))."')";
        if ($this->get_param("dto")) $cond .= " AND (`time` <= '".esc_sql($this->get_param("dto"))." 23:59:59')";
        if ($this->item != 0) $cond .= " AND formid=".intval($this->item);


	    $events_query = "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC";
	    /**
	     * Allows modify the query of messages, passing the query as parameter
	     * returns the new query
	     */
	    $events_query = apply_filters( 'cpreftrack_csv_query', $events_query );
	    $events = $wpdb->get_results( $events_query );  

        if ($this->include_user_data_csv)
            $fields = array("ID", "Form ID", "Time", "IP Address", "email");
        else
            $fields = array("ID", "Time", "referrer");        
       
        $fields_exclude = explode(",",trim(get_option('cp_cpreftrack_bocsvexclude',"")));
        for($j=0; $j<count($fields_exclude); $j++)
           $fields_exclude[$j] = strtolower(trim($fields_exclude[$j]));
        
        $newfields = array();
        for($j=0; $j<count($fields); $j++)
           if (!in_array($fields[$j],$fields_exclude)) 
               $newfields[] = $fields[$j];
        $fields = $newfields;       
 
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($item->id,$this->get_option('form_name',''), $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($item->id, $item->time, $item->referrer);
            if ($item->data)
                $data = unserialize($item->data);
            else
                $data = array();            
            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
                if ($k != 'apps' && $k != 'itemnumber' && !in_array(strtolower($k),$fields_exclude))
                {
                   $fields[] = $k;
                   $value[] = $d;
                }
            $values[] = $value;
        }

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", ($fields[$i]));
            echo '"'.str_replace('"','""', $hlabel).'",';
        }    
        echo "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode($item[$i],',');
                $item[$i] = $this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]);
                echo '"'.str_replace('"','""', trim($item[$i])).'",';
            }
            echo "\n";
        }

        exit;
    }
    

    protected function iconv($from, $to, $text)
    {
        if (get_option('CP_APPB_CSV_CHARFIX',"") == "" && function_exists('iconv'))
            return iconv($from, $to, $text);
        else
            return $text;
    }
    
    
    function generateSafeFileName($filename) {
        $filename = strtolower(strip_tags($filename));
        $filename = str_replace(";","_",$filename);
        $filename = str_replace("#","_",$filename);
        $filename = str_replace(" ","_",$filename);
        $filename = str_replace("'","",$filename);
        $filename = str_replace('"',"",$filename);
        $filename = str_replace("__","_",$filename);
        $filename = str_replace("&","and",$filename);
        $filename = str_replace("/","_",$filename);
        $filename = str_replace("\\","_",$filename);
        $filename = str_replace("?","",$filename);
        return sanitize_file_name($filename);
    }
    
    
    public function isa_add_cron_recurrence_interval( $schedules ) {
     
        $schedules['cp_every_once_day'] = array(
                'interval'  => 60*60*24,
                'display'   => __( 'Once a day', 'cp-referrer-and-conversions-tracking' )
        );
         
        return $schedules;
    }  


    public function setId($id)
    {
        $this->item = intval($id);
    }

    
    public function getId()
    {
        return intval($this->item);
    }

 
    private function get_records_csv($formid, $form_name = "")
    {
        global $wpdb;

        $saved_item = $this->item;
        $this->item = $formid;
        
        $last_sent_id = get_option('cp_cpreftrack_last_sent_id_'.$formid, '0');
        $events = $wpdb->get_results(
                             $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE formid=%d AND id>%d ORDER BY id ASC",$formid,$last_sent_id)
                                     );

        if ($wpdb->num_rows <= 0) // if no rows, return empty
        {
            $this->item = $saved_item;
            return '';
        }

        if ($this->item)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_APPBOOK_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $buffer = '';
        if ($this->include_user_data_csv)
            $fields = array("Submission ID", "Form", "Time", "IP Address", "email");
        else
            $fields = array("Submission ID", "Form", "Time", "email");
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($item->id, $form_name, $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($item->id, $form_name, $item->time, $item->notifyto);
            $last_sent_id = $item->id;
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
                if ($k != 'apps' && $k != 'itemnumber')
                {
                   $fields[] = $k;
                   $value[] = $d;
                }
            $values[] = $value;
        }
        update_option('cp_cpreftrack_last_sent_id_'.$formid, $last_sent_id);

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $this->get_form_field_label($fields[$i],$form));
            $buffer .= '"'.str_replace('"','""', $hlabel).'",';
        }              
        $buffer .= "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode($item[$i],',');
                $item[$i] = $this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]);
                $buffer .= '"'.str_replace('"','""', $item[$i]).'",';
            }
            $buffer .= "\n";
        }
        
        $this->item = $saved_item;
        return $buffer;

    }


} // end class


?>