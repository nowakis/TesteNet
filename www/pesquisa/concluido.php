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

#############################################################################
##############                      PAGINA                  	##############
##############################################################################	

$msg_erro		= array();
$msg_ok			= array();
$msg			= array();
$msg_codigo		= "";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

/*         PROFESSOR         */

try {
	if (strlen($_login_professor)>0){
		$professor = $sessionFacade->recuperarProfessor($_login_professor);
	}
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

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

if (is_object($professor)){
	$usuario_id    = $professor->getId();
	$usuario_nome  = $professor->getNome();
	$usuario_email = $professor->getEmail();
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