<!-- Your Page Content Here -->
<div class="box box-warning">
	<div class="box-header">
		<h4>Home</h4>
	</div>
	<div class="box-body">
		<div class="row">
			<?php 
				date_default_timezone_set('Europe/Dublin');
				ini_set('date.timezone', 'Europe/Dublin');

				require __LIB."MailController.php";
				$scans = get_scan_lists();
				
				foreach ($scans as $key => $scan) {
					$domains = explode(",", $scan['domain_list']);
					foreach ($domains as $url) {

						$datenow = date("h:i A F d, Y");
						$scan_sched = $scan['next_scan'];

						if( $datenow != $scan_sched ){
							$scan_res = cron_scan_all($url, $scan, $scan['scan_type']);
							$new_result = $scan_res['html'];
							
							if( $scan_res['send_email'] == 1 ){

								$scan_title = "domain/s";
								$wp_type = "";

								if( $scan['scan_type'] == 'wp_scan' ){
									$dangers = implode('<br>', $scan_res['wp_vulnerabilities']);
									
									$wp_type = ucfirst( substr($scan['wp_type'], 0, strlen($sca['wp_type'])-1) );
									$scan_title = "WordPress $wp_type <strong>".ucfirst($scan['domain_list'])."</strong>";
									

								}

								$recipient = array(
									'Name' => loggedin_user($scan['wp_user_id'])->display_name,
									"Address" => $scan["email"]
								);

								$Subject = "Sentinel AEWS - Scan completed.";
								$Body = "<div style='background: #70e1f5;
									background:url(http:".__PUBLIC_PATH."custom/images/bg.png);
									background-size:contain;
									padding: 10px; text-align: justify; padding-left: 20px;font-family: inherit;
									padding-bottom: 20px;'>
									<br/>
											Hello <b>".loggedin_user($scan['wp_user_id'])->display_name."</b>, <br/><br/>
											A scheduled scan has been completed and has found some <span style='color:red;'>issues</span> on your $scan_title. <br/>
											You can view the results <a href='http:".url("?action=scan-results&id=".$scan['id'])."' target='_blank'>here</a> <br/> <br/> <br/>

											<br/> <br/>
											Regards, <br/> <br/>
											AutomatedToolkit
											<br/><br/>
											<hr><i>If the link above doesn't work, you can copy the URL below to take you to view the scan results. <br/> <b>http:".url("?action=scan-results&id=".$scan['id'])
											."</b> </i>

										</div>";
								$AltBody = ""; 
								$attachments = array();
								$ccs = array();
								$bbcs = array();

								$mailer = new MailContoller($Subject, $Body, $AltBody, $recipient, $attachments, $ccs, $bbcs);

								$mailer->send();
							}


						}
					}
				}

			?>
		</div>
	</div>
</div>