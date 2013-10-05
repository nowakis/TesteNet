<?php

//error_reporting(E_ALL);
error_reporting(E_STRICT);

date_default_timezone_set('America/Toronto');

include("class/class.phpmailer.php");


$mailer = new PHPMailer();$mailer->IsSMTP();
$mailer->Host = 'ssl://smtp.gmail.com:465';
$mailer->SMTPAuth = TRUE;
$mailer->Username = 'testenetweb@gmail.com';  
// Change this to your gmail adress
$mailer->Password = 'teste112233';  // Change this to your gmail password
$mailer->From = 'testenetweb@gmail.com';  // This HAVE TO be your gmail adress
$mailer->FromName = 'fake'; // This is the from name in the email, you can put anything you like here
$mailer->Body = 'This is the main body of the email';$mailer->Subject = 'This is the subject of the email';
$mailer->AddAddress('testenetweb@gmail.com');  // This is where you put the email adress of the person you want to mail
if(!$mailer->Send()){   echo "Message was not sent<br/ >";   echo "Mailer Error: " . $mailer->ErrorInfo;}else{   echo "Message has been sent";}

exit;

//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail             = new PHPMailer();

$body             = $mail->getFile('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail->IsSMTP();
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

$mail->SMTPKeepAlive = 'true'; 
$mail->Timeout = "60"; 
$mail->SMTPDebug = "true"; 
$mail->SMTPDebug = 2;

$mail->Username   = "testenetweb@gmail.com";  // GMAIL username
$mail->Password   = "teste112233";            // GMAIL password

$mail->AddReplyTo("testenetweb@gmail.com","First Last");

$mail->From       = "testenetweb@gmail.com";
$mail->FromName   = "First Last";

$mail->Subject    = "PHPMailer Test Subject via gmail";

//$mail->Body       = "Hi,<br>This is the HTML BODY<br>";                      //HTML Body
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->WordWrap   = 50; // set word wrap

$mail->MsgHTML($body);

$mail->AddAddress("fabio.nowaki@gmail.com", "John Doe");

#$mail->AddAttachment("images/phpmailer.gif");             // attachment

$mail->IsHTML(true); // send as HTML

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>
