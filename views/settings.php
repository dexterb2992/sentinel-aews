<!-- Your Page Content Here -->
<div class="box box-info">
	<div class="box-header">
		<h4>
			Settings
			<span class="pull-right">
				<small>Server time: <?php echo date("h:i a F d, Y"); ?></small> 
			</span>
		</h4>
	</div>
	<?php 
		$settings = get_sentinel_settings();
	?>
	<div class="box-body">
		<div class="row">
			<div class="col-md-6">
				<form id="sentinel_settings">
					<div class="form-group">
						<label for="email_address">Email Address</label>
						<input type="text" id="email_address" name="email" class="form-control" placeholder="Enter your email address" data-toggle="tooltip" data-original-title="We'll use your email address to notify you everytime your domains have been scanned" value="<?php echo $settings['email']; ?>" required>
					</div>
					<div class="form-group">
						<label for="scanning_schedule">Scanning Schedule</label>
						<select class="form-control" name="scanning_schedule">
							<?php 
								$options = ["hourly", "daily", "weekly"];
								foreach ($options as $option) {
									$is_selected = $option == $settings['scanning_schedule'] ? 'selected' : '';
									echo "<option value='$option' $is_selected>$option</option>";
								}
							?>
						</select>
					</div>
				</form>
				<div class="form-group">
					<button id="save_settings" class="btn btn-warning btn-flat pull-right">
						<i class="fa-refresh fa"></i> Update Settings
					</button>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label>Push Notifications</label> <br/>
					<button class="js-push-button btn btn-info" disabled>
			        	Enable Push Messages
			        </button>

			        <div class="js-curl-command"></div>

			        <?php 
						// js_include("config.js", "gcm");
						// js_include("demo.js", "gcm");
						// js_include("main.js", "gcm");
					?>
					<script src="./config.js"></script>
					<script src="./demo.js"></script>
					<script src="./main.js"></script>

					<script>
					  /* jshint ignore:start */
					  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					  ga('create', 'UA-53563471-1', 'auto');
					  ga('send', 'pageview');
					  /* jshint ignore:end */
					</script>
				</div>
			</div>
		</div>
	</div>

</div>