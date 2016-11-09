<?php
return [
	'Home' => [
		'is_external' => true,  // boolean true if the action is an external or internal link, usuall starts with http://
		'action' => [__BASE_HOME_URL],
		'aliases' => [],
		'title' => 'Home',
		'icon' => 'fa fa-home'
	],
	'Dashboard' => [
		'is_external' => false, // boolean false if it will be ?action=some-slug-here
		'action' => ['index'],
		'aliases' => ['home', 'dashboard','index', ''],
		'title' => 'Dashboard',
		'icon' => 'fa fa-dashboard'
	],
	'My Scans' => [
		'is_external' => false,
		'action' => ['scanlists'],
		'aliases' => [],
		'title' => 'My Previous Scans',
		'icon' => 'fa fa-file-code-o'
	],
	'WordPress Vulnerabilities' => [
		'is_external' => false,
		'action' => ['wordpress-vulnerabilities'],
		'aliases' => [],
		'title' => 'Checks for any vulnerable version of WordPress, WordPress Themes and Plugins.',
		'icon' => 'fa fa-wordpress'
	],
	'Settings' => [
		'is_external' => false,
		'action' => ['settings'],
		'aliases' => [],
		'title' => 'Settings',
		'icon' => 'fa fa-gears'
	],
	'Logout' => [
		'is_external' => true,
		'action' => [wp_logout_url()],
		'aliases' => [],
		'title' => 'Logout',
		'icon' => 'fa fa-sign-out'
	]

];