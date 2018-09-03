<?php
if (get_option('sentinel_push_notification') !== false || !empty(get_option('sentinel_push_notification'))) {
	var_dump(get_option('sentinel_push_notification'));
} else {
	if (!get_option('sentinel_push_notification')) {
		add_option('sentinel_push_notification', "NAA");
	} else {
		update_option('sentinel_push_notification', "UPDATED");
	}
	echo "GI SET NA! RELOAD PARA INTENSE!";
}


// echo public_path("custom/images/bg.png");
// $installed_themes = wp_get_themes();

// var_dump($installed_themes); die;

// $filtered = [];

// $slug = "";

// foreach ($installed_themes as $key => $theme) {
// 	$slug = explode("/", $key); 
// 	$filtered[$key] = [
// 		'name' => $theme->Name,
// 		'slug' => $key
// 	];
// }

// $current = wp_get_theme();
// var_dump($current);

// var_dump($filtered); die;

// $json_installed_themes = json_encode($all_plugins, JSON_UNESCAPED_SLASHES);

// var_dump($json_installed_themes);

?>

<script>
	// window.installedPluginsStr = '<?php echo $json_installed_themes; ?>';
	// window.installedPlugins = <?php //$all_plugins;?>;
</script>