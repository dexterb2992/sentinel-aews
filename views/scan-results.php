<?php 
	if( !isset( $_GET['id'] ) ) redirect(url("?action=scanlists"));
	$list = get_last_scan_result($_GET['id']);
?>
<!-- Your Page Content Here -->
<div class="box box-warning">
	<div class="box-header">
		<h4>
			Scan Results
			<span class="pull-right">
				<small>Server time: <?php echo date("h:i a F d, Y"); ?></small> 
			</span>
		</h4>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<span class="label-info label">Last Scan: <?php echo $list['last_scan']; ?></span>
				<span class="label-warning label">Next Scan: <?php echo $list['next_scan']; ?></span>
				<br/>
				<!-- <span class="btn-xs btn btn-flat btn-info log-option pull-right" id="more_details" data-toggle="tooltip" data-original-title="Show more details" style="margin-top: -14px;">Show more details</span> -->
				<br/>
				<div <?php echo $list['scan_type'] == 'sentinel' ? 'class="process_logs"' : ''; ?> id="process_logs">
					<div class="<?php echo $list['scan_type'] == 'sentinel' ? 'entry' : ''; ?>">
						
						
						<?php 
							if($list['scan_type'] == 'sentinel'){
								echo '<div class="list">
										'.$list['domain_list'].'
									</div>';
								echo '<div class="result">';
								echo stripcslashes(urldecode($list['last_scan_result'])); 
								echo '</div>';
							}else{
								if( $list['last_scan_result'] != "" && $list['last_scan_result'] != null ){


									$results = json_decode($list['last_scan_result']);

									// this is to fixed differently saved data from wp_scan
									if( !is_object($results[0]) && isset($results[0][0]) ){
										$results = $results[0];
									}

									$type = $list['wp_type'] == 'plugins' ? 'info' : 'warning';

									echo '<div>Name: <strong>'.ucfirst($list['domain_list']).'</strong><br>
									    	Type: <span class="label label-'.$type.'">WordPress '.$list['wp_type'].'</span>
										</div>';
									?>
									<br/>
									<table class="table data-table">
										<thead>
											<tr>
												<th>Title</th>
												<th>Added</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php
												if( count($results) > 0 ):
													foreach ($results as $key => $result) {
														
														$status = $result->fixed_in == null ? 
																	'<span class="label-danger label">
																		Not yet resolved.
																	</span>' : 
																	'<span class="label-success label">
																		Fixed in version '.$result->fixed_in.'
																	</span>';
														$date_created = new DateTime($result->created_at);

														echo '<tr>
																<td>
																	<a href="http://wpvulndb.com/vulnerabilities/'.$result->id.'" target="_blank">
																		'.$result->title.'
																	</a>
																</td>
																<td>'.$date_created->format('Y-m-d').'</td>
																<td>'.$status.'</td>
															</tr>';
													}
												endif;
											?>
										</tbody>
									</table>
									<?php
								}
							}
						?>
						
					</div>
				</div>
				
			</div>
			<div class="col-md-2"></div>	
		</div>
	</div>
</div>