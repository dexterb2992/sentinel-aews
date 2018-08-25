<!-- Your Page Content Here -->
<div class="box box-warning">
	<div class="box-header">
		<h4>
			Home 
			<span class="pull-right">
				<small>Server time: <?php echo date("h:i a F d, Y"); ?></small> 
			</span>
		</h4>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<div class="form-group">
					<label for="websites">Websites</label>
					<textarea class="form-control" id="websites" placeholder="Enter your Domain names, one per line (Do not include 'http://')"></textarea>
				</div>
				<div class="form-group">
					<span class="label label-info" id="process_status"></span>
					<button class="bg-aqua-gradient btn btn-flat pull-right" id="start_tests">
						<i class="fa fa-hourglass-start"></i> Start Tests
					</button>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<span class="btn btn-xs btn-flat bg-red-gradient log-option" id="clear_logs" data-toggle="tooltip" data-original-title="Clear logs">Clear</span>
				<!-- <span class="btn-xs btn btn-flat btn-info log-option" id="more_details" data-toggle="tooltip" data-original-title="Show more details">Show more details</span> -->
				<div class="process_logs" id="process_logs"></div>
				
			</div>
			<div class="col-md-2"></div>	
		</div>
	</div>
</div>