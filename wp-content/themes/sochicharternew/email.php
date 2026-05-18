<?php
header("Access-Control-Allow-Origin: https://sochicharter.ru");
//header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
?>
<?php
	global $inputname;
	global $inputphone;
	global $inputtype;
	global $issend;
?>
<?php
	$input = json_decode(file_get_contents('php://input'), true);

	if(!$input) {
		die('Empty data posted!');
	} else {
		$inputname  = filter_var($input['name'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW) ?? '';
		$inputphone = filter_var($input['phone'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW) ?? '';
		$inputtype  = filter_var($input['type'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW) ?? '';
	}
?>
<?php
require 'phpmailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->CharSet = "UTF-8";

//$mail->SMTPDebug = 3;                               	// Enable verbose debug output
$mail->isSMTP();                                      	// Set mailer to use SMTP
$mail->Host = 'smtp.beget.com';                   		// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               	// Enable SMTP authentication
$mail->Username = 'marketing@sochicharter.ru';		   	// SMTP username
$mail->Password = '!&5Ywh7d7%7T';						// SMTP password
//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 2525;                                    	// TCP port to connect to
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->setFrom('marketing@sochicharter.ru', 'Sochi Charter');
$mail->addAddress('tretyakru@mail.ru', 'Третьяк Александр');
$mail->isHTML(false);
$mail->Subject = 'Заявка Sochi Charter';
$mail->Body    = "\nИмя\n" . $inputname ."\nТел\n\n+7" . $inputphone . "\n\n" . $inputtype;

$issend = $mail->send();
?>
