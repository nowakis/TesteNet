<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/

##############################################################################
## INCLUDES E CONEXÔES BANCO
##############################################################################

session_start();
require_once "funcoes.php";
require_once "class/class.Template.inc.php";
require_once "class/class.SessionFacade.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "prova";
$titulo     = "Prova";
$sub_titulo = "Prova";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

//$theme = ".";
//$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
/*
		$corpo="Olá <b>Fabio Nowaki</b>, tudo bom?<br>
		<br>
		Nós da <b>TesteNet</b> informamos ao Sr(a) que seu pagamento referente ao produto <b>$linha_produto_nome</b> foi confirmando com sucesso e nas próximas horas estará sendo enviado no seguinte endereço:<br>
		<br><b>
		$linha_endereco
		</b>
		<br>
		<br>
		Caso tenha alguma dúvida, acesse: <a href='http://mercadolivre.telemediajp.com/index.php?email=$email_cripto&senha=$senha_cripto' target='_blank'>TecnoMedia</a>
		<br><br>
		Ou acesse: http://mercadolivre.telemediajp.com/ e entre com os dados:<br>
		Login: <b>$linha_email</b><br>
		Senha: <b>$linha_senha</b><br>
		<br><br>
		Att
		<br><br>
		<b>Fábio<br>TecnoMedia</b>";


		$file   = "tab.png" ;  // File name or path Example : file/image.gif
		$html   = "<b><i>Tes Mail.class.php</i></b>" ;
		$message= $corpo;
		$subject= "Teste de Email" ;
		$to     = "testenetweb@gmail.com" ;
		$from   = "testenetweb@gmail.com";
		$return = "testenetweb@gmail.com" ;
		$mail   = &new Easy_Email($from, $to, $subject, $return) ;
		#$mail->simpleMail($message) ;   // Use this to send simple email
		$mail->htmlMail($html) ;        // Use this to send html email
		#$mail->simpleAttachment($file,$message) ;   // Use this to send simple email with attachment
		#$mail->htmlAttachment($file,$html) ;        // Use this to send html email with attachment

*/


/*
	include("class/sendmail.class.php");

	// Neue Instanz der Klasse erstellen (Ab jetzt kann auf die Funktionen der Klasse zugegriffen werden)
	$mail = new sendmail();

	$mail->SetCharSet("ISO-8859-1");
	$mail->from("Fabio","testenetweb@gmail.com");
	$mail->to("fabio.nowaki@gmail.com");
	#$mail->cc("fabio@nowakis.com");
	#$mail->bcc("fabio@nowakis.com");
	$mail->subject("Teste");

	// Angeben des Textes (Auch HTML möglich)
	// Beim eingeben des HTML Textes bitte <HTML><BODY></BODY></HTML> weglassen,
	// da dies automatisch hinzugefügt wird
	$mail->text($corpo);
	$mail->attachment("tab.png");
	$mail->attachment("rodape.htm");

	// Versenden der E-Mail
	$mail->send();
*/

/*

require("class/attach_mailer_class.php");

$test = new attach_mailer($name = "Fabio Nowaki", $from = "fabio@nowakis.com", $to = "fabio@nowakis.com", $cc = "fabio.nowaki@gmail.com", $bcc = "nowkis@gmail.com", $subject = "Teste envio de Email", $corpo);
$test->create_attachment_part("tab.png"); 
$test->create_attachment_part("rodape.htm");
$test->process_mail();
*/

#echo phpinfo();
#exit;

// example on using PHPMailer with GMAIL
/*
include("class/class.phpmailer.php");
include("class/class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

$mail             = new PHPMailer();

$body             = $mail->getFile('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail->IsSMTP();
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port

$mail->Username   = "testenetweb@gmail.com";  // GMAIL username
$mail->Password   = "fabio112233";            // GMAIL password

$mail->From       = "testenetweb@gmail.com";
$mail->FromName   = "TesteNet";
$mail->Subject    = "Teste de Email PHPMailer";
$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
$mail->WordWrap   = 50; // set word wrap

$mail->Body = "Hi, This is the HTML BODY "; //HTML Body
$mail->MsgHTML($body);

$mail->AddReplyTo("testenetweb@gmail.com","Suporte TesteNet");

#$mail->AddAttachment("/path/to/file.zip");             // attachment
#$mail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment

$mail->AddAddress("fabio@nowakis.com","Fábio Nowaki");

$mail->IsHTML(true); // send as HTML

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message has been sent";
}
*/


if (isset($_GET['prova']) AND strlen(trim($_GET['prova']))>0){

	$prova = trim($_GET['prova']);

	try {
		$prov_resp = $sessionFacade->recuperarProvaRespondida($prova,$_login_aluno); 
		$prov      = $prov_resp->getProva();

		if ( is_object($prov)){

			if (strlen($prov->getProvaNaoLiberada())>0){
				$erro_tmp = $prov->getProvaNaoLiberada();
				unset($prov);
				echo "<script language='javascript'>alert('$erro_tmp'); history.go(-1);</script>";
				exit;
				#throw new Exception($erro_tmp);
			}

			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getNome();
			$curso				= $prov->getDisciplina()->getCurso()->getNome();
			$professor			= $prov->getProfessor()->getNome();
			#$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			#$dificuldade		= $prov->getDificuldade();
			$nota				= $prov_resp->getNota();

			if (strlen($nota)==0){
				$nota = "<b style='color:#EE9611; font-size:14px'>PROVA NÃO FOI CORRIGIDA</b>";
			}elseif ($nota>6){
				$nota = "<b style='color:#0000FF; font-size:14px'>".$nota."</b>";
			}else{
				$nota = "<b style='color:#FF0000; font-size:14px'>".$nota."</b>";
			}

		}else{
			throw new Exception('Prova não encontrada!');
			#array_push($msg_erro,"Prova não encontrada!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Obrigado! A prova foi salva com sucesso e enviada ao professor.");
	}
}

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'NOTA'				=>	$nota,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	$disciplina,
							'CURSO'				=>	$curso,
							'PROFESSOR'			=>	$professor
));	

if (is_object($prov)){
	for ($j=0;$j<$prov->getQtdePerguntas();$j++){

		$perg = $prov->getPergunta($j);

		$pergunta = $prov_resp->getPerguntaRespondida( $prov_resp->getPerguntaId( $perg->getId() ));

		$nota_pergunta = $pergunta[2];
		if (strlen($nota_pergunta)==0){
			$nota_pergunta = "<b style='color:#EE9611; font-size:14px'> - </b>";
		}else{
			$nota_pergunta = "<b style='color:#0000FF; font-size:16px'>".number_format($nota_pergunta,2)."</b>";
		}

		$model->assign_block_vars('pergunta', array('I'					=>	$j+1,
													'PROVA_PERGUNTA'	=>	$perg->getId(),
													'TITULO_PERGUNTA'	=>	$perg->getTitulo(),
													'TOPICO'			=>	$perg->getTopico()->getDescricao(),
													'TIPO'				=>	$perg->getTipoPergunta()->getDescricao(),
													'DIFICULDADE'		=>	$perg->getDificuldade('texto'),
													'PESO'				=>	$perg->getPeso(),
													'VALOR_CORRIGIDO'	=>	$nota_pergunta,
													'CLASSE'			=>	$j%2==0?"class='odd'":""
													));

		$respostas = array();

		/* DISSERTATIVA */
		if ($perg->getTipoPergunta()->getId()=="1"){

			$respostas = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta(0)->getId() );

			$model->assign_block_vars('pergunta.dissertativa',array('PROVA_RESPOSTA'		=>	$perg->getResposta(0)->getId(),
																	'PROVA_PERGUNTA'		=>	$perg->getId(),
																	'ALUNO_RESPOSTA_TEXTO'	=>	$respostas[3]
																	));
		}

		/* MULTIPLA-ESCOLHA  */
		if ($perg->getTipoPergunta()->getId()=="2"){
			$model->assign_block_vars('pergunta.multipla_escolha',array());

			for ($i=0; $i<$perg->getQtdeResposta(); $i++){

				$respostas = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta($i)->getId() );

				$icone_correcao = "";

				if ($pergunta[2]){
					if ($perg->getResposta($i)->getRespostaCorreta() ) {
						$icone_correcao = '<img src="imagens/check3.png" align="absmiddle" alt="Resposta Correta">';
					}
				}else{
					if ($respostas[3]){
						$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
					}

					if ($respostas[3] AND $perg->getResposta($i)->getRespostaCorreta() ) {
						$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
					}

					if ($respostas[3] AND !$perg->getResposta($i)->getRespostaCorreta() ) {
						$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
					}
					if ($perg->getResposta($i)->getRespostaCorreta() ) {
						$icone_correcao = '<img src="imagens/check3.png" align="absmiddle" alt="Resposta Correta">';
					}
				}

				$model->assign_block_vars('pergunta.multipla_escolha.item',array('PROVA_PERGUNTA'		=>	$perg->getId(),
																				'PROVA_RESPOSTA'		=>	$perg->getResposta($i)->getId(),
																				'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
																				'RESPOSTA_CORRETA'		=>	$icone_correcao,
																				'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
																				'ALUNO_RESPOSTA_TEXTO'	=>	(strlen($respostas[3])>0)?"CHECKED":"",
																				'I'						=>	$i,
																				'NUMERO'				=>	 strtolower(chr($i+65))
																				));
			}
		}

		/* VERDADEIRO-FALSO  */
		if ($perg->getTipoPergunta()->getId()=="3"){
			$model->assign_block_vars('pergunta.verdadeiro_falso',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){

				$respostas = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta($i)->getId() );

				$icone_correcao = "";
				if ($respostas[4]){
					$icone_correcao = '<img src="imagens/check3.png" align="absmiddle" alt="Resposta Correta">';
				}else{
					$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
				}

				$model->assign_block_vars('pergunta.verdadeiro_falso.item',array('PROVA_PERGUNTA'	=>	$perg->getId(),
																				'PROVA_RESPOSTA'	=>	$perg->getResposta($i)->getId(),
																				'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																				'RESPOSTA_CORRETA'		=>	$icone_correcao,
																				'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																				'ALUNO_RESPOSTA_TEXTO'	=>	(strlen($respostas[3])>0)?"CHECKED":"",
																				'I'					=>	$i,
																				'NUMERO'			=>	$i+1
																				));
			}
		}

		/* COMPLETE  */
		if ($perg->getTipoPergunta()->getId()=="4"){
			$model->assign_block_vars('pergunta.complete',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){

				$respostas = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta($i)->getId() );

				$icone_correcao = "";
				if ($respostas[4]>0){
					$icone_correcao = '<img src="imagens/check3.png" align="absmiddle" alt="Resposta Correta">';
				}else{
					$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
				}

				$model->assign_block_vars('pergunta.complete.item',array('PROVA_PERGUNTA'		=>	$perg->getId(),
																		'PROVA_RESPOSTA'		=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
																		'RESPOSTA_TEXTO_FILHO'	=>	$respostas[3],
					'RESPOSTA_TEXTO_FILHO_CORRETA' => is_object($perg->getRespostaFilhoOrdem($i)->getRespostaFilho())?$perg->getRespostaFilhoOrdem($i)->getRespostaFilho()->getRespostaTexto():"",
																		'RESPOSTA_CORRETA'		=>	$icone_correcao,
																		'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
																		'I'						=>	$i,
																		'NUMERO'				=>	strtolower(chr($i+65))
																		));
			}
		}
			
		/* LACUNA  */
		if ($perg->getTipoPergunta()->getId()=="5"){
			$model->assign_block_vars('pergunta.lacuna',array());
			for ($i=0; $i<$perg->getQtdeResposta(); $i++){

				$respostas = $prov_resp->getRespostasPerguntaItem( $perg->getId(), $perg->getResposta($i)->getId() );

				#print_r($respostas);
				#echo "<br>";

				$montaCombo = "<select name='lacuna_resposta_correta_".$perg->getResposta($i)->getId()."' style='width:50px'>";
				for ($k=0; $k<$perg->getQtdeResposta(); $k++){
					if ( $respostas[3] == $perg->getResposta($k)->getRespostaFilho()->getId()) {
						$montaCombo .= "<option value='".strtolower(chr($k+65))."' ".$correto." SELECTED>".strtolower(chr($k+65))."</option>";
					}	
				}
				$montaCombo .= "</select>";

				$icone_correcao = "";
				if ($respostas[4]>0){
					$icone_correcao = '<img src="imagens/check3.png" align="absmiddle" alt="Resposta Correta">';
				}else{
					$icone_correcao = '<img src="imagens/delete2.png" align="absmiddle" alt="Resposta Correta">';
				}

				$model->assign_block_vars('pergunta.lacuna.item',array('PROVA_PERGUNTA'			=>	$perg->getId(),
																		'PROVA_RESPOSTA'		=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
																		'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getRespostaFilhoOrdem($i)->getRespostaFilho())?$perg->getRespostaFilhoOrdem($i)->getRespostaFilho()->getRespostaTexto():"",
																		'RESPOSTA_CORRETA'		=>	$icone_correcao,
																		"PROVA_RESPOSTA_COMBO"	=>	$montaCombo,
																		'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
																		'I'						=>	$i,
																		'NUMERO'				=>	strtolower(chr($i+65))
																		));
			}
		}


	}
	$model->assign_vars(array( 'QTDE_PERGUNTAS' => $prov->getQtdePerguntas()));
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>