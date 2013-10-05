<?
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/html; charset=ISO-8859-1",true);

require_once "../funcoes.php";
require_once "../class/class.SessionFacade.php";
require_once "../banco.con.php";

$instituicoes = $sessionFacade->recurarInstituicaoTodosDAO();

for ($i=0; $i<count($instituicoes); $i++) {
	global $_login_instituicao;
	$_login_instituicao = $instituicoes[$i]->getId();
	$sessionFacade->enviaEmailProvaAluno();
}
/*
$mail             = new PHPMailer();
$body             = $mail->getFile('../emails/prova_aluno.html');

$data = date('d/m/Y H:i');
$body      = '<h3>Envio de Emails de Prova Concluído! '.$data.'</h3>';

$mail->From       = "testenetweb@gmail.com";
$mail->FromName   = "TesteNet";
$mail->Subject    = '"TesteNet - Envio de Emails de Prova Concluído - '.$data;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
$mail->MsgHTML($body);
$mail->AddAddress('testenetweb@gmail.com', 'Suporte TesteNet');
$mail->Send();
*/
?>