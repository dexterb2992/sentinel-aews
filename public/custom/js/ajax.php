<?php
session_start(); 
error_reporting(E_ALL);

include "ajax-functions.php";

$q = isset($_POST['q']) ? $_POST['q'] : '';

switch ($q) {
	case 'test':
		echo json_encode( array('ok') );
		break;

	case 'check_url':
		echo check_url("http://".$_POST['url']);    # Checks to see if site is up, runs http Get request
		break;

	case 'sucuri_scan':
		$res = sucuri_scan($_POST['url']);  # gets status of site if blacklisted  and/or containing malware
		echo $res['html'];
		break;

	case 'google_diagnostic':
		$res = google_diagnostic($_POST['url']);
		echo $res['html'];
		break;

	case 'ultratools_scan':
		$res = ultratools_scan($_POST['url']);
		echo $res['html']; 
		break;

	case 'save_sentinel_settings':
		echo save_sentinel_settings($_POST);
		break;

	case 'save_scan':
		$is_wp_scan = isset($_POST['is_wp_scan']) ? true : false;
		echo save_scan($_POST, $is_wp_scan);
		break;

	case "delete_scan":
		echo delete_scan($_POST['id']);
		break;

	case 'wp_scan':
		echo wp_scan($_POST);

		break;

	case 'get_wp_scans':
		echo get_wp_scans();
		break;
		
}







