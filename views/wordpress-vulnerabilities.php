<!-- Your Page Content Here -->
<?php js_include('angular.min.js', 'custom'); ?>

<div class="box box-warning" ng-app="wpScan" ng-controller="wpScanCtrl">
	<div class="box-header">
		<h4>
			WordPress Vulnerabilities
			<span class="pull-right">
				<small>Server time: <?php echo date("h:i a F d, Y"); ?></small> 
			</span>
		</h4>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-10">
				<a class="btn-warning btn-md btn-flat pull-right btn" data-toggle="tooltip" data-original-title="View your previous WordPress Vulnerabilities scan." id="prev_wp_scan" ng-click="showPreviousScans()">
					<i class="fa fa-history"></i> Previous Scans
				</a>
				<a class="btn-info btn-md btn-flat pull-right btn r-margin2" data-toggle="tooltip" data-original-title="Check your WordPress plugins and themes installed on your blog for possible vulnerabilities." href="javascript:void(0)" id="new_wp_scan">
					<i class="fa fa-search"></i> New Scan
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10">
				<?php 
					require __LIB."WPVulnerabilities.php";

					$wpvnb = new WPVulnerabilities();
					
				?>
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#wordpresses" data-toggle="tab" aria-expanded="true">WordPresses</a>
						</li>
						<li>
							<a href="#plugins" data-toggle="tab" aria-expanded="false">Plugins</a>
						</li>
						<li>
							<a href="#themes" data-toggle="tab" aria-expanded="false">Themes</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="wordpresses">
							<?php echo $wpvnb->get_table_elements("wordpresses"); ?>
						</div>
						<div class="tab-pane" id="plugins">
							<?php echo $wpvnb->get_table_elements("plugins"); ?>
						</div>
						<div class="tab-pane" id="themes">
							<?php echo $wpvnb->get_table_elements("themes"); ?>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>

	<!-- Modal for new WP scan -->
	<div class="modal" id="wp_scan_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<div class="modal-title">
						<h4 class="modal-title">WP Scan</h4>
						<span>
						  <i>Check your WordPress plugins and themes installed on your blog for possible vulnerabilities.</i>
						</span>
					</div>
				</div>
				<div class="modal-body">
					<form class="form-vertical" name="wpScanForm">
						<div class="form-group">
							<label>Type</label>
							<select class="select form-control" name="type" id="wp_type" ng-model="wp_type" ng-options="x for x in wpTypeOptions" required></select>
							<label class="label-danger label" ng-show="wpScanForm.type.$touched && wpScanForm.type.$invalid">
								This field is required.
							</label>
						</div>
						<div class="form-group">
							<label>{{wp_type}} Slug</label>
							<input class="form-control" type="text" name="slug" id="slug" placeholder="{{wp_type | lowercase}}-slug" ng-model='slug' required />
							<label class="label-danger label" ng-show="wpScanForm.slug.$touched && wpScanForm.slug.$invalid">
								This field is required.
							</label>
						</div>
					</form>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div ng-show='themePluginError.status'>
							    <span class="label label-danger">{{ themePluginError.msg }}</span>
							</div>
							<div id="wpscan_result" ng-model='wpscanResult'>
								<table class="table">
									<thead>
										<tr>
											<th>Name</th>
											<th>Added</th>
											<th>Title</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="vulnerability in resultsVulnerabilities[0]">
											<td>{{ resultsVulnerabilities[0].name | lowercase }}</td>
											<td>{{ vulnerability.created_at | date:'yyyy-MM-dd' }}</td>
											<td>
												<a ng-href="{{resultsVulnerabilities[0].url+vulnerability.id}}" target="_blank">
													{{ vulnerability.title }}
												</a>
											</td>
											<td>
												<span class="label-danger label" ng-show="vulnerability.fixed_in == null">
													Not yet resolved.
												</span>
												<span class="label-success label" ng-show="vulnerability.fixed_in != null">
													Fixed in version {{ vulnerability.fixed_in }}
												</span>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-warning btn-flat" ng-disabled="wpScanForm.$invalid || AjaxLoading" ng-click="startScan(wp_type, slug)">
						<i class="fa fa-search" id="start_scan_loader"></i> Start Scan
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal" id="wp_scans_modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<div class="modal-title">
						<h4>Previous WordPress Scan</h4>
					</div>
				</div>
				<div class="modal-body">
					<table class="table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Type</th>
								<th>Last Scan</th>
								<th>Next Scan</th>
								<th>Scan Result</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="scan in wpScansListing">
								<td>{{ scan.domain_list }}</td>
								<td>
									<label class="label label-info" ng-show="scan.wp_type == 'plugins'">
										{{ scan.wp_type }}
									</label>
									<label class="label label-warning" ng-show="scan.wp_type != 'plugins'">
										{{ scan.wp_type }}
									</label>
								</td>
								<td>{{ scan.last_scan }}</td>
								<td>{{ scan.next_scan }}</td>
								<td>
									<a href="?action=scan-results&id={{ scan.id }}" target="_blank">View</a>
									<a ng-click="deleteScan($index)" data-index="{{$index}}" class="text-red link">Delete</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php js_include('script-angular-jquery.js', 'custom'); ?>