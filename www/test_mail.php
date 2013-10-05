<?php

include_once('class/class.phpmailer.php');

$mail             = new PHPMailer(); // defaults to using php "mail()"

$body             = $mail->getFile('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail->From       = "testenetweb@gmail.com";
$mail->FromName   = "First Last";
$mail->SMTPKeepAlive = 'true'; 
$mail->Timeout = "60"; 
$mail->SMTPDebug = "true"; 

$mail->Subject    = "PHPMailer Test Subject via mail()";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$mail->AddAddress("testenetweb@gmail.com", "John Doe");

#$mail->AddAttachment("images/phpmailer.gif");             // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>
