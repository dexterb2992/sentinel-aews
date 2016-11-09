<?php 
error_reporting(1);
require __LIB.'PHPMailer-master/PHPMailerAutoload.php';

class MailContoller{
	function __construct($Subject = "No Subject", $Body = "", $AltBody = "", $recipient = array(), $attachments = array(), $ccs = array(), $bbcs = array())
	{
		$this->Subject = $Subject;
		$this->Body = $Body;
		$this->AltBody = $AltBody;
		$this->recipient = $recipient;
		$this->attachments = $attachments;
		$this->ccs = $ccs;
		$this->bbcs = $bbcs;
	}

	function send(){
		/* Configuration for Mail */


		$__mailer = array();
		$__mailer['Host'] = 'mail.stealthytools.com';  // Specify main and backup SMTP servers
		$__mailer['SMTPAuth'] = true;                               // Enable SMTP authentication
		$__mailer['Username'] = 'sentinel@stealthytools.com';                 // SMTP username
		$__mailer['Password'] = 'wala123321';                           // SMTP password
		$__mailer['SMTPSecure'] = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$__mailer['Port'] = 25;                                    // TCP port to connect to
		$__mailer['SenderAddress'] = 'sentinel@stealthytools.com';     // Email address to appear
		$__mailer['SenderName'] = 'no-reply@sentinel.stealthytools.com';						// Your Name as a sender


		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                     // Set mailer to use SMTP
		$mail->Host =  $__mailer['Host'];					// Specify main and backup SMTP servers
		$mail->SMTPAuth = $__mailer['SMTPAuth'];            // Enable SMTP authentication
		$mail->Username = $__mailer['Username'];            // SMTP username
		$mail->Password = $__mailer['Password'];            // SMTP password
		$mail->SMTPSecure = $__mailer['SMTPSecure'];        // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $__mailer['Port'];                    // TCP port to connect to

		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only

		$mail->setFrom($__mailer['SenderAddress'], $__mailer['SenderName']);
		$mail->addAddress($this->recipient['Address']);               // Name is optional
		$mail->addReplyTo($__mailer['SenderAddress'], $__mailer['SenderName']);

		if( !empty($this->attachments) ){
			foreach ($this->attachments as $attachments) {
				$mail->addAttachment($attachments);         // Add attachments
			}
		}
		
		if( !empty($this->ccs) ){
			foreach ($ccs as $cc) {
				$mail->addCC($cc);
			}
		}
		
		if( !empty($this->bbcs) ){
			foreach ($bbcs as $bbc) {
				$mail->addBCC($bbc);
			}
		}

		

		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $this->Subject;
		$mail->Body    = $this->Body;
		$mail->AltBody = $this->AltBody;

		if(!$mail->send()) {
			echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		   
		}

		return true;
	}
}

?>


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

				$recipient = array(
					'Name' => "Dexter Gwapo",
					"Address" => $_GET['email']
				);

				pre($recipient);

				$Subject = "Sentinel AEWS - Scan completed.";
				$Body = "<div><br/>
							Hello {$recipient['Name']}, <br/><br/>
							A scheduled scan for your domain/s have been completed. <br/>
							You can view the results <a href='http:".url("?action=scan-results&id=".$list['id'])."' target='_blank'>here</a> <br/> <br/> <br/>

							<br/> <br/>
							Regards, <br/> <br/>
							AutomatedToolkit

							<hr><i>If the link above doesn't work, you can copy the URL below to take you to view the scan results. <br/> <b>http:".url("?action=scan-results&id=".$list['id'])
							."</b> </i>

						</div>";
				$AltBody = ""; 
				$attachments = array();
				$ccs = array();
				$bbcs = array();

				$mailer = new MailContoller($Subject, $Body, $AltBody, $recipient, $attachments, $ccs, $bbcs);

				pre($mailer->send());
				

			?>
		</div>
	</div>
</div>