<?php 
error_reporting(0);
include "../../../config.php";
$parse_uri = explode( 'tools', $_SERVER['SCRIPT_FILENAME'] );

if( file_exists($parse_uri[0] . 'wp-load.php') ){
	if( !function_exists('get_current_user_id') ){
		include  $parse_uri[0] . 'wp-load.php';
		$is_wordpress = true;
	}
}else{
	die('Cannot find wp-load file. Make sure to put this plugin inside "tools" folder, and tools folder should also be in the same level with wp-content folder.');
}

include __LIB."functions.php";

function check_url($url){
	//check, if a valid url is provided
	if(!filter_var($url, FILTER_VALIDATE_URL)){
	    return json_encode( 
	    	array(
	    		'status' => 'failed',
	    		'error' => "$url is not a valid url."
	    	)
	    );
	}

   //initialize curl
   $curlInit = curl_init($url);
   curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
   curl_setopt($curlInit,CURLOPT_HEADER,true);
   curl_setopt($curlInit,CURLOPT_NOBODY,true);
   curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

   //get answer
   $response = curl_exec($curlInit);

   curl_close($curlInit);

   	if ($response){
   		 return json_encode( 
	    	array(
	    		'status' => 'success'
	    	)
	    );
   }

    return json_encode( 
    	array(
    		'status' => 'failed',
    		'error' => "$url cannot be reached."
    	)
    );
}

function save_sentinel_settings($input){
	global $wpdb;

	$id = get_current_user_id();

	$sql = "SELECT COUNT(*) FROM $wpdb->tbl_sentinel WHERE wp_user_id=$id";

	$user_count = $wpdb->get_var( $sql );	

	$data = array(
		'wp_user_id' => $id,
		'email' => $input['email'],
		'scanning_schedule' => $input['scanning_schedule']
	);

	$format = array(
		'%d',
		'%s',
		'%s'
	);

	if( $user_count > 0 ){
		$res = $wpdb->update( "$wpdb->tbl_sentinel", $data, array('wp_user_id' => $id ) );
	}else{
		$res = $wpdb->insert( "$wpdb->tbl_sentinel", $data, $format );
	}
	

	return $res;
}

function save_scan($input, $is_wp_scan = false){
	date_default_timezone_set('Europe/Dublin');
  	ini_set('date.timezone', 'Europe/Dublin');

	global $wpdb;

	$id = get_current_user_id();

	$sql = "SELECT * FROM $wpdb->tbl_sentinel WHERE wp_user_id=$id";
	$settings = $wpdb->get_row( $sql , ARRAY_A );

	$last_scan = date("Y-m-d H:i");

	$next_scan = get_next_scan($last_scan);

	$data = array(
		"wp_user_id" => $id,
		"domain_list" => $input['url'],
		"last_scan_result" => $is_wp_scan ? $input['scan_result'] : urlencode($input['scan_result']),
		"last_scan" 	   => $last_scan,
		"next_scan" 	   => $next_scan,
		"wp_type"		   => isset($input['wp_type']) ? $input['wp_type'] : 'plugins',
		"scan_type"		   => $is_wp_scan ? 'wp_scan' : 'sentinel'
	);
	
	if( $wpdb->insert( $wpdb->tbl_sentinel_scans, $data ) )
    		return json_encode( array( "success" => 1, "next_scan" => $next_scan ) );
    
    return json_encode( array( "success" => 0, "statusText" => "Sorry, we're having a hard time saving your scan right now. Please try again later.", "error" => $wpdb->print_error() ) );
    

}

function get_wp_scans(){
	global $wpdb;
	$id = get_current_user_id();
	$sql = "SELECT * FROM $wpdb->tbl_sentinel_scans WHERE wp_user_id=$id AND scan_type='wp_scan'";

	$wp_scans = $wpdb->get_results( $sql );
	foreach ($wp_scans as $wp_scan) {
		$wp_scan->scan_result = json_decode($wp_scan->scan_result);
	}

	return json_encode($wp_scans);
}

function delete_scan($id){
	global $wpdb;

	$res = $wpdb->delete( $wpdb->tbl_sentinel_scans, array("id" => $id) );

	return $res;
}


