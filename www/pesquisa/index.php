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
require_once "../funcoes.php";
require_once "../class/class.Template.inc.php";
require_once "../class/class.SessionFacade.php";
require_once "../banco.con.php";
require_once "autentica_usuario.php";

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";


if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$professor = addslashes(trim($_POST['professor']));
	$email     = addslashes(trim($_POST['email']));

	try {
		$banco->iniciarTransacao();

		if (strlen($professor)>0){
			$professor = $sessionFacade->recuperarProfessor($professor);
		}

		$pesquisa = new Pesquisa();

		if (is_object($professor)){
			$pesquisa->setProfessor($professor);
		}

		/* Perguntas / Respostas */
		for ($i=0; $i<20;$i++){
			$pergunta  = addslashes(trim($_POST['pergunta_'.$i]));
			$resposta  = addslashes(trim($_POST['resposta_'.$i]));

			if (strlen($pergunta)>0){
				$pesquisa->addPergunta(array($pergunta,$resposta));
			}
		}

		$pergunta  = addslashes(trim($_POST['pergunta_comentario']));
		$resposta  = addslashes(trim($_POST['comentario']));

		if (strlen($pergunta)>0){
			$pesquisa->addPergunta(array($pergunta,$resposta));
		}

		$sessionFacade->gravarPesquisa($pesquisa);

		$banco->efetivarTransacao();
		header("Location: concluido.php?msg_codigo=1&email=".$email);
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

/*         PROFESSOR         */

$usuario_id    = "";
$usuario_nome  = "";
$usuario_email = "";

try {
	if (strlen($_login_professor)>0){
		$professor      = $sessionFacade->recuperarProfessor($_login_professor);
		$pesquisa       = $sessionFacade->recuperarPesquisaTodos($professor);
		$fazer_pesquisa = $sessionFacade->recuperarFazerPesquisa($professor);
		if (count($pesquisa)>0 AND $fazer_pesquisa == 0){
			header("Location: ../logout.php?pesquisa=n");
			exit;
		}

		$usuario_id    = $professor->getId();
		$usuario_nome  = $professor->getNome();
		$usuario_email = $professor->getEmail();

	}else{
		if (isset($_GET["email"]) OR isset($_POST["email"])) {
			$usuario_email = "";
			if (strlen($_GET["email"])>0){
				$usuario_email = $_GET["email"];
			}
			if (strlen($_POST["email"])>0){
				$usuario_email = $_POST["email"];
			}
			$usuario_nome = $sessionFacade->recuperarNomePesquisa($usuario_email);
		}
	}
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}


$model->assign_vars(array(	'PROFESSOR'			=>	$usuario_id,
							'NOME_PROFESSOR'	=>	$usuario_nome,
							'EMAIL'				=>	$usuario_email
));	

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

#include "rodape.php";


?>