<?php



namespace Model;



use Model\Config;
use \Mailgun\Mailgun;



class Email
{

	public static function systemEmail( $subject='Message from DART',$msg='',$html = false )
	{
		
		//HTML template for text emails
		ob_start();
		?>
			<!DOCTYPE html>
			<html lang="en-US">
			<head>
				<title><?=$subject?></title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta name="viewport" content="width=device-width" />
				<style type="text/css">@media (-webkit-min-device-pixel-ratio: 1.25), (min-resolution: 120dpi) {body[override] #logo{background-image:url('<?=Config::$instance->dart->logo->url ?>') !important;background-size:254px 40px !important;}}</style>
				</body>
			<body bgcolor="efefef" style="border: 0; margin: 0; padding: 0; min-width: 100%;background-color:#efefef;" >
			<table style="background-color:#ffffff;width:100%;"><tr><td><table style="width:90%;margin:0 auto;text-align:center;"><tr><td><p><img src="<?=Config::$instance->dart->logo->url ?>" style="width:254px;height:40px;" /></p></td></tr></table></td></tr></table>
			<table style="background-color:#ffffff;width:100%;"><tr><td><table style="width:90%;margin:0 auto 20px auto;"><tr><td>[[CONTENTS]]</td></tr></table></td></tr></table>
			<table style="width:100%;background-color:#efefef;border-top:solid 1px #e5e5e5;"><tr><td><table style="width:90%;margin:0 auto;text-align:center;"><tr><td><p>Questions?  Contact <a href="mailto:support@dartmusic.com">support@dartmusic.com</a></p></td></tr></table></td></tr></table>
			</body>
			</html>
		<?php
		$template = ob_get_contents();
		ob_end_clean();


		$mg = new Mailgun( Config::$instance->mailgun->apikey );


		try
		{
			$result = $mg->sendMessage(
				Config::$instance->mailgun->domain,
				array(
					'from'    => Config::$instance->email->dev,
					'to'      => Config::$instance->email->dev,
					'subject' => $subject,
					'text'    => ($html ? '' : $msg),
					'html'    => ($html ? $msg : str_replace('[[CONTENTS]]',preg_replace('/http([^\s]*)/i','<a href="http$1">http$1</a>',str_replace(array("\n","\r"),array('<br />','<br />'),$msg)),$template))
			) /*, array(
	            'attachment' => array('/path/to/file.txt', '/path/to/file.txt')
	              ) */
			);

		}
		catch ( Exception $e )
		{
			//Email it using standard mail function

			if ($html)
			{
				$headers = "FROM:" . Config::$instance->email->dev ."\r\n";
				$headers .= "Reply-To: " . Config::$instance->email->dev . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				mail(
					Config::$instance->email->dev,
					$subject,
					$msg,
					$headers
				);
			}
			else
			{
				mail(
					Config::$instance->email->dev,
					$subject,
					$msg,
					"FROM:info@dartmusic.com"
				);
			}

			// Mailgun errors are thrown as exceptions
			mail(
				Config::$instance->email->dev,
				'A mailgun error occurred',
				get_class($e) . ' - ' . $e->getMessage(),
				Config::$instance->email->dev
			);

			throw $e;
		}
	}


////////////// The Old Code //////////////

	// $mg = new Mailgun(Config::$instance->mailgun->apikey);

		// $mg->sendMessage(
		// 	Config::$instance->mailgun->domain,
		// 	array(
		// 		'from'    => Config::$instance->email->dev,
		// 		'to'      => Config::$instance->email->dev,
		// 		'subject' => $subject,
		// 		'text'    => $msg
		// 	)
		// );



}
