<?php
$__is_CRON = true;
include "cron-config.php";

	include "views/_partials/_header.php";

	?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">

		<!-- Main content -->
		<section class="content">

		<?php include "views/cron.php"; ?>

		</section><!-- /.content -->

	</div><!-- /.content-wrapper -->
	<?php
	

	include "views/_partials/_footer.php"; 
		
