<?php
## This file is where we load any page
	include "config.php"; 

	require_once __LIB."ContentLoader.php";

	$page = isset( $_GET['action'] ) ? $_GET['action'] : 'index';

	## titles = array('filename' => 'Title')
	$titles = array(
		"index" => "Home",
		"settings" => "Settings",
		"scanlists" => "My Scans",
		"cron" => "Scanning Schedules",
		"wordpress-vulnerabilities" => "WordPress versions, WordPress Themes and Plugins Vulnerabilities",
		'try' => "Testing"
	);

	$loader = new ContentLoader($page, $titles[$page]);


