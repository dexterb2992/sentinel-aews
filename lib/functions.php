<?php
## PHP helper functions

if( !defined('__BASE_URL') ){ 
	echo 'Make sure you include the Configuration file correctly.';
	exit;
}

// generates a valid url to a given path
function url($path){
	return __BASE_URL.$path;
}

// redirects to a given url
function redirect($url){
	echo '<script>window.location = "'.$url.'";</script>';
}

// returns public url
function public_path($path = ''){
	return __PUBLIC_PATH.$path;
}

// returns public directory
function public_dir($path = ''){
	return WP_SC_MR_ABS_PATH.'public/'.$path;
}

// returns classes directory
function class_dir($path = ''){
	return __CLASSES_FOLDER.$path;
}


// display $str in a nicer way
function pre($str){
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}

// shorthand for var_dump
function vd($var){
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

function clean_html($html){
	return stripslashes_deep( stripslashes( stripcslashes( $html ) ) );
}

// cleans POST request data
function clean_data(array $input){
	foreach ($input as $key => $value) {
		$input[$key] = mysql_real_escape_string($value);
	}
	return $input;
}

// checks the active sidebar item
function check_sidebar_selection($actions = array()){
	if( in_array(trim($_GET['action']), $actions) ){
		return 'active';
	}
	return '';
}

// use to include custom script files
// type = [custom, plugins, dist, ext]
function js_include($filename, $type){
	$src = "";
	switch ($type) {
		case 'custom':
			$src = __CUSTOM."js/";
			break;
		case 'plugins':
			$src = __PUBLIC_PATH."plugins/";
			break;
		case 'dist':
			$src = __PUBLIC_PATH."dist/js/";
			break;
		case 'bootstrap':
			$src = __PUBLIC_PATH."bootstrap/js/";
			break;
		case 'ext': // external urls
			$src = "";
			break;

		case 'gcm':
			$src = __PUBLIC_PATH."gcm/";
			break;
	}
	
	echo "<script src='{$src}{$filename}'></script>";
}

// use to include custom css files
// type = [custom, plugins, dist, ext]
function css_include($filename, $type){
	$src = "";
	switch ($type) {
		case 'custom':
			$src = __CUSTOM."css/";
			break;
		case 'plugins':
			$src = __PUBLIC_PATH."plugins/";
			break;
		case 'dist':
			$src = __PUBLIC_PATH."dist/css/";
			break;
		case 'bootstrap':
			$src = __PUBLIC_PATH."bootstrap/css/";
			break;
		case 'ext': // external urls
			$src = "";
			break;
	}
	
	echo "<link rel='stylesheet' href='{$src}{$filename}'>";
}

// returns the sidebar items
function get_sidebar_items(){
	$lists = include __LIB."config/sidebar.php";
	if( is_array($lists) ){
		foreach ($lists as $key => $list) {
			$action = $list['is_external'] == false ? url('?action=') : '';
			$action.= $list["action"][0];

			echo '<li class="'.check_sidebar_selection($list["action"]).'">
			        <a href="'.$action.'"><i class="'.$list["icon"].'"></i> <span>'.$key.'</span></a>
			    </li>';
		}
	}
}

function curl_post_html($url, $fields = array() ){
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
	// curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_ENCODING, "");
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
	curl_setopt($ch, CURLOPT_MAXREDIRS, 15);     


	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);

	return $result;
}

function DOMgetInnerHTML(DOMNode $element, $strip_tags = false) { 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child){ 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    if( $strip_tags == true ){
    	 return strip_tags($innerHTML); 
    }
    return $innerHTML;
} 


function getPageData($url){
	if( function_exists('curl_init') ){
		set_time_limit(0);
		$cookie = tempnam ("/tmp", "CURLCOOKIE");
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	    // curl_setopt($ch, CURLOPT_HEADER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
	    // curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_ENCODING, "");
	    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 15);     

		$html = curl_exec($ch);
		$status = curl_getinfo($ch);
		curl_close($ch);

		if($status['http_code']!=200){
		    if($status['http_code'] == 301 || $status['http_code'] == 302) {
		        list($header) = explode("\r\n\r\n", $html, 2);
		        $matches = array();
		        preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
		        $url = trim(str_replace($matches[1],"",$matches[0]));
		        $url_parsed = parse_url($url);
		        return (isset($url_parsed))? getPageData($url):'';
		    }
		}
		return $html;
	}else{
		return @file_get_contents($url);
	}
}

function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, urlencode($url));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function loggedin_user($id = 0){
	if( $id == 0 ) $id = get_current_user_id();
	$user_info = get_userdata( $id );
    return $user_info->data;
}

function get_sentinel_settings(){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->prefix}sentinel_aews WHERE wp_user_id=".get_current_user_id();
	$results = $wpdb->get_row( $sql, ARRAY_A );

	return $results;
}

function get_sentinel_settings_by_id($id){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->prefix}sentinel_aews WHERE wp_user_id=$id";
	$results = $wpdb->get_row( $sql, ARRAY_A );

	return $results;
}


function get_scan_lists(){
	global $wpdb;
	$sql = "SELECT ses.*,se.email FROM {$wpdb->prefix}sentinel_aews_scans as ses 
				inner join {$wpdb->prefix}sentinel_aews as se 
				on ses.wp_user_id = se.wp_user_id ";

	$results = $wpdb->get_results( $sql, ARRAY_A );
	return stripslashes_deep($results);

}

function get_last_scan_result($id){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->prefix}sentinel_aews_scans WHERE id=$id";
	$results = $wpdb->get_results( $sql, ARRAY_A );
	return stripslashes_deep($results[0]);
}

function get_next_scan($last_scan, $wp_user_id = 0){
	date_default_timezone_set('Europe/Dublin');
  	ini_set('date.timezone', 'Europe/Dublin');

  	if( $wp_user_id != 0 ){
  		$scanning_schedule = get_sentinel_settings_by_id($wp_user_id)['scanning_schedule'];
  	}else{
  		$scanning_schedule = get_sentinel_settings()['scanning_schedule'];
  	}

  	if( !in_array($scanning_schedule, array("hourly", "daily", "weekly", "2 minutes")) ){
  		$scanning_schedule = "daily";
  	}
	

	$start_date = $last_scan;  
	$date = strtotime($start_date);

	$increments = "";
	switch ($scanning_schedule) {
		case '2 minutes':
			$increments = "+2 minutes";
			break;

		case 'hourly':
			$increments = "+1 hour";
			break;
		
		case 'daily':
			$increments = "+1 day";
			break;

		case 'weekly':
			$increments = "+7 day";
			break;	
	}

	$date = strtotime($increments, $date);

	return date('h:i A F d, Y', $date);
}

function sucuri_scan($url){
	$source = "https://www.siteadvisor.com/";
	$u = "{$source}sitereport.html?url=$url";

	$data = getPageData($u);
	$send_email = 0;

	libxml_use_internal_errors(true);

	$dom = new DOMDocument;

	@$dom->loadHTML($data);

	$divs = $dom->getElementsByTagName("div");

	$divresults = "";

	// extract div tag
	foreach ($divs as $div) {
		$class = $div->getAttribute("class");
		$cleaned_class = str_replace('container', '', $class);

		if ($class == "container safe" || $class == "container warning" || $class == "container danger") {
			$divresults .= '<div id="sitereport"><div class="row '.$cleaned_class.'">'.DOMgetInnerHTML($div).'</div></div>';

			if (strpos($class, "warning") !== false || strpos($class, "danger") !== false) {
				$send_email = 1;
			}
		}
	}

	$search = [
		'src="/',
		'href="/',
		'class="status',
		'class="content'
	];

	$replacement = [
		'src="'.$source,
		'href="'.$source,
		'class="status col-md-4',
		'class="content col-md-8'
	];

	$divresults2 = str_replace($search, $replacement, $divresults);

	return array(
		'html' => $divresults2,
		'send_email' => $send_email
	);
}



function ultratools_scan($url){
	// checks to see if your domain is on a Real Time Spam Blacklist
	$host = "https://www.ultratools.com/tools/spamDBLookupResult";
	$fields = array(
		'domainName' => urlencode($url)
	);
	$data = curl_post_html($host, $fields);

	libxml_use_internal_errors(true);

	$dom = new DOMDocument;

	@$dom->loadHTML($data);

	$tables = $dom->getElementsByTagName("table");
	$htmltables = "";

	// extract title tag
	foreach ($tables as $table) {
		if( $table->getAttribute("class") == "resultstable" ){

			$htmltables = '<table class="'.$table->getAttribute('class').'">
							<thead>
								<tr>
									<th colspan="3">
										Spam Blaclist result for <a href="http://'.$url.'" target="_blank">'.$url.'</b>
									</th>
								</tr>
							</thead>
								'.DOMgetInnerHTML($table).'</table>';
		}
	}

	$send_email = 0;

	if( strpos($htmltables, "table-error") !== false ){
		$send_email = 1;
	}

	return array(
		"html" => $htmltables,
		"send_email" => $send_email
	);
}


function google_diagnostic($url){
	$url = urlencode($url);
	$u = "https://sb-ssl.google.com/safebrowsing/api/lookup?client=".__GOOGLE_CLIENT_NAME."&key=".__GOOGLE_API_KEY."&appver=1.5.2&pver=3.1&url=$url";

	$data = file_get_contents($u);
	$send_email = 0;

	// "phishing" | "malware" | "unwanted" | “phishing,malware” | "phishing,unwanted" | "malware,unwanted" | "phishing, malware, unwanted" | "ok"	
	$response = '<div class="col-md-6" id="status-overview-container">
          	<div><h4 class="status-not-dangerous">Not dangerous</h4>
          		<p>Google has not recently seen malicious content on '.$url.'</p>
          	</div>
          </div>';
	
	switch ($data) {
		case "phishing":
		case "malware":
		case "unwanted":
			$response = '<div class="col-md-6" id="status-overview-container"><div><h4 class="status-dangerous"><img src="https://www.google.com/transparencyreport/safebrowsing/diagnostic/images/dangerous.png" class="heading-icon">Dangerous</h4><p>Google detected '.$url.' for <strong class="status-dangerous">'.$data.'</strong>.</p></div></div>';
			$send_email = 1;
			break;
	}

	$response =  '<div class="row status-summary" id="overview">
		        <div class="col-md-2">
		           	<h4>
		            	Current status:
		           	</h4>
		        </div>'.$response.'
	        </div>';

	$result = array("html" => $response, "send_email" => $send_email);

	return $result;

}

function wp_scan($post, $data_type = 'json'){ // $data_type = json | array
	if( !class_exists('WPVulnerabilities') ){
		require __LIB.'WPVulnerabilities.php';
	}

	$wpvulnb = new WPVulnerabilities();
	$response = $wpvulnb->api($post['type'], $post['slug']);

	$data = array();

	$data['request'] = $post;
	$data['detail'] = $wpvulnb->more_details;

	$decoded_response = @get_object_vars(json_decode($response)); // converts object to array

	if( is_array( $decoded_response ) ){ // check if valid json

		// return $response;
		$data['response'] = $decoded_response;
	}else{
		// probably the response is error 404 html page
		$data['response'] = array(
		 	'status' => 404, 
		 	'statusText' => 'The WordPress '.str_replace('s', '', $post['type']).' you are looking for doesn\'t exist.'
		);
	}

	if( $data_type == 'json' )
		return json_encode($data);

	return $data;
	
}

function cron_scan_all($url, $scan, $scan_type = 'sentinel'){
	date_default_timezone_set('Europe/Dublin');
  	ini_set('date.timezone', 'Europe/Dublin');
  	
	global $wpdb;

	$send_email = 0;
	$data = "";
	$wp_vulnerabilities = array();

	if( $scan_type == 'sentinel' ){
		$google_diagnostic = google_diagnostic($url);
		$ultratools_scan = ultratools_scan($url);
		$sucuri_scan = sucuri_scan($url);

		if( $google_diagnostic['send_email'] == 1 || $ultratools_scan['send_email'] == 1 || $sucuri_scan['send_email'] == 1 ){
			$send_email = 1;
		}

		$data = $sucuri_scan['html'];
		$data.= $google_diagnostic['html'];
		$data.= $ultratools_scan['html'];
	}else{ //wp_scan
		$scan['slug'] = $scan['domain_list'];
		$scan['type'] = $scan['wp_type'];
		$wp_res = wp_scan($scan, 'array');

		pre($wp_res['response'][ $scan['domain_list'] ]->vulnerabilities);
		
		foreach ($wp_res['response'][ $scan['domain_list'] ]->vulnerabilities as $key => $vulnerability) {
			if( $vulnerability->fixed_in == "" || $vulnerability->fixed_in == null ){
				$send_email = 1;
				array_push($wp_vulnerabilities, $vulnerability->title);
			}
		}

		$data = json_encode($wp_res['response'][ $scan['domain_list'] ]->vulnerabilities);
	}
	

	// updates scan result
	$vals = array(
		"last_scan_result" => $data,
		"last_scan" => $scan['next_scan'],
		"next_scan" => get_next_scan($scan['next_scan'], $scan['wp_user_id'])
	);

	if( $data != "" ){
		$res = $wpdb->update( $wpdb->tbl_sentinel_scans, $vals, array("id" => $scan['id']) );

	}

	return array(
		'html' => $data,
		'send_email' => $send_email,
		'wp_vulnerabilities' => $wp_vulnerabilities
	);
}

function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}