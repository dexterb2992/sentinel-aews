<?php
error_reporting(E_ALL);

class CronConfig{
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

		define('__BASE_URL', "//".$_SERVER['HTTP_HOST'].__FOLDER_NAME);
		define('__ABS_PATH', dirname(__FILE__)."/");
		define('__PUBLIC_PATH', __BASE_URL."public/");
		define('__CLASSES_FOLDER', __ABS_PATH."classes/");
		define('__PLUGINS_FOLDER', __ABS_PATH."plugins/");
		define('__VIEWS', __ABS_PATH."views/");
		define('__PARTIALS', __VIEWS."_partials/");
		define('__INCLUDES', __PARTIALS."_includes/");
		define('__LIB', __ABS_PATH."lib/");

		define("HOST", 'http://stealthytools.com/forum-master/');

		define("__APP_NAME", "Sentinel AEWS");
		define("__APP_TITLE", "Sentinel AEWS");
		define('__APP_NAME_ABBREV', "<i><small class='font-xs'><b>s</b>AEWS</small></i>");

		define('__GOOGLE_CLIENT_NAME', 'automatedtoolkit-1278');
		define('__GOOGLE_API_KEY', 'AIzaSyCRqnTPkTHBhjBbpQCIKJzTcfOyhGMcjEc');
		
	}
}

$sentinel = new CronConfig();
$sentinel->instantiate();