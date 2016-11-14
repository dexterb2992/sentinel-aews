<!-- Your Page Content Here -->
<div class="box box-warning">
	<div class="box-header">
		<h4>
			My Scans
			<span class="pull-right">
				<small>Server time: <?php echo date("h:i a F d, Y"); ?></small> 
			</span>
		</h4>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-10">
				<table class="table data-table">
					<thead>
						<th>Name</th>
						<th>Last Scan</th>
						<th>Next Scan</th>
						<th>Scan Result</th>
					</thead>
					<?php 
						require __LIB."Pagination/Paginator.php";

						global $wpdb;

						$paginator = new Paginator($wpdb->prefix."sentinel_aews_scans", 'id', 10);
						$paginator->where_query("WHERE wp_user_id=".get_current_user_id());
						$paginator->process();

						
						foreach ($paginator->items() as $key => $item) {

							
							

							if( $item->scan_type == 'wp_scan' ){
								$icon = 'fa-wordpress';
								$title = "WordPress ".$item->wp_type;
								$color = $item->wp_type == 'plugins' ? 'text-blue' : 'text-yellow';
							}else{
								$icon = 'fa-globe';
								$title = "Domain";
								$color = "text-green";
							}

							$prefix = '<span class="pull-left" style="margin-right: 5px;" title="'.$title.'">
											<i class="fa '.$icon.' '.$color.'"></i>
										</span>';

							if( $title == 'Domain' ){

								$link = filter_var($item->domain_list, FILTER_VALIDATE_URL) ? $item->domain_list : 'http://'.$item->domain_list;
								$domain_name = "<a href=\"$link\" title=\"$link\" target=\"_blank\">$prefix $item->domain_list</p>";
							}else{
								$domain_name = '<p title="'.$item->domain_list.'">'.$prefix.$item->domain_list.'</p>';
							}

							$date_last_scan = new DateTime($item->last_scan);

							echo '<tr>
									<td style="max-width: 181px;overflow: hidden;">
										'.$domain_name.'
									</td>
									<td>'.$date_last_scan->format('h:i A F d, Y').'</td>
									<td>'.$item->next_scan.'</td>
									<td>
										<a href="'.url("?action=scan-results&id=".$item->id).'" target="_blank">View</a>
										<a href="javascript:void(0);" class="delete-scan text-red" data-id="'.$item->id.'">Delete</a>
									</td>
								</tr>';
						}

					?>
				</table>

			</div>
			
		</div>
		<?php 
			$appended_attributes = "action=".$_GET['action'];
			echo count($paginator->items()) > 0 ? $paginator->render($appended_attributes) : ' Sorry, we don\'t have anything to show right now. Click <a href="'.url("?action=index").'">here</a> to run your first scan.';
		?>
	</div>
</div>