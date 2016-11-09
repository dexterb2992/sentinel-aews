<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Dublin');
ini_set('date.timezone', 'Europe/Dublin');

class Sentinel{
	function __construct()
	{
		$this->instantiate();
	}

	function instantiate(){
		// $parse_uri = explode( 'tools', $_SERVER['SCRIPT_FILENAME'] );
		$parse_uri = explode( 'tools', __FILE__ );

		if( file_exists($parse_uri[0] . 'wp-load.php') ){
			if( !function_exists('get_current_user_id') ){
				include  $parse_uri[0] . 'wp-load.php';
				$is_wordpress = true;
			}
		}else{
			die('Cannot find wp-load file. Make sure to put this plugin is inside "tools" folder, and tools folder should also be in the same level with wp-content folder.');
		}


		preg_match_all('~(.*?)/~', $_SERVER['SCRIPT_NAME'], $output);

		$v_foldername = "";

		foreach ($output[0] as $key => $value) {
			$v_foldername.= $value;
		}

		define('__FOLDER_NAME', $v_foldername);	

		define('__BASE_HOME_URL', "http://".$_SERVER['HTTP_HOST']."/");
		define('__BASE_URL', "//".$_SERVER['HTTP_HOST'].__FOLDER_NAME);
		define('__ABS_PATH', dirname(__FILE__)."/");
		define('__PUBLIC_PATH', __BASE_URL."public/");
		define('__CLASSES_FOLDER', __ABS_PATH."classes/");
		define('__PLUGINS_FOLDER', __ABS_PATH."plugins/");
		define('__VIEWS', __ABS_PATH."views/");
		define('__PARTIALS', __VIEWS."_partials/");
		define('__INCLUDES', __PARTIALS."_includes/");
		define('__LIB', __ABS_PATH."lib/");
		define('__CUSTOM', __PUBLIC_PATH.'custom/');

		define("HOST", 'http://stealthytools.com/forum-master/');

		define("__APP_NAME", "Sentinel AEWS");
		define("__APP_TITLE", "Sentinel AEWS");
		define('__APP_NAME_ABBREV', "<i><small class='font-xs'><b>s</b>AEWS</small></i>");

		define('__GOOGLE_CLIENT_NAME', 'automatedtoolkit-1278');
		define('__GOOGLE_API_KEY', 'AIzaSyCRqnTPkTHBhjBbpQCIKJzTcfOyhGMcjEc');

		if( get_current_user_id() < 1 ){
			echo "<pre style='color:red'>Please <a href='".wp_login_url(__BASE_URL)."'>login</a> to continue...</pre>";
			if( isset($_SESSION) ){
				session_destroy();
			}
			exit(0);
		}

		$this->create_tables();
		
	}

	function create_tables(){
		// Code for creating a table goes here
    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    global $wpdb;
	    global $charset_collate;

	    $wpdb->tbl_sentinel = "{$wpdb->prefix}sentinel_aews";
	    $wpdb->tbl_sentinel_scans = "{$wpdb->prefix}sentinel_aews_scans";

	    if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->tbl_sentinel}'") != "{$wpdb->tbl_sentinel}") {
			// if sentinel_aews table is not created, we can create the table here.
			ob_start();
	        $sql_create_table = "CREATE TABLE {$wpdb->prefix}sentinel_aews (
				id bigint(20) unsigned NOT NULL auto_increment,
				wp_user_id int,
				email varchar(255) NOT NULL,
				scanning_schedule varchar(20),
	            PRIMARY KEY  (id)
	        ) $charset_collate; ";
	     
	        $res = $wpdb->query($sql_create_table);

	        ob_flush();

	        // make sure to insert default settings
	        $user_info = get_userdata( get_current_user_id() );

	        $data = array(
	        	"wp_user_id" => get_current_user_id(),
	        	"email" => $user_info->user_email,
	        	"scanning_schedule" => "daily"
	        );

	        $res = $wpdb->insert($wpdb->tbl_sentinel, $data);

	        // var_dump($sql_create_table);
	    }

	    if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->tbl_sentinel_scans}'") != "{$wpdb->tbl_sentinel_scans}") {
			// if sentinel_aews_scans table is not created, we can create the table here.
			ob_start();
	        $sql_create_table = "CREATE TABLE {$wpdb->tbl_sentinel_scans} (
				id bigint(20) unsigned NOT NULL auto_increment,
				wp_user_id int,
				domain_list longtext,
				last_scan varchar(50),
				next_scan varchar(50),
				last_scan_result longtext,
				wp_type varchar(10) NOT NULL default 'plugins',
				scan_type varchar(10) NOT NULL default 'sentinel',
	            PRIMARY KEY  (id)
	        ) $charset_collate; ";
	     
	        $res = $wpdb->query($sql_create_table);

	        ob_flush();

	        // var_dump($sql_create_table);
	    }


	}
}

new Sentinel();