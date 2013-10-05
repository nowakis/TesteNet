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

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################

$layout     = "comunicado";
$titulo     = "Comunicado";
$sub_titulo = "Comunicado";

#include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.'.php'));


##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['comunicado']) AND strlen(trim($_GET['comunicado']))>0){

	$comunicado = trim($_GET['comunicado']);

	try {
		$comun = $sessionFacade->recuperarComunicado($comunicado); 

		if (is_object($comun)) {
			$comunicado				=	$comun->getId();
			$curso					=	(is_object($comun->getCurso()))?$comun->getCurso()->getNome():"";
			$professor				=	(is_object($comun->getProfessor()))?$comun->getProfessor()->getNome():"";
			$titulo_comunicado		=	$comun->getTitulo();
			$data					=	$comun->getData();
			$comentario				=	$comun->getComentario();
			$obrigatorio			=	($comun->getObrigatorio()=="1")?"Sim":"Não";
		}else{
			throw new Exception("Comunicado não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (is_object($comun)){
	$model->assign_vars(array(		'COMUNICADO'		=>	$comunicado,
									'CURSO'				=>	$curso,
									'PROFESSOR'			=>	$professor,
									'TITULO_COMUNICADO'	=>	$titulo_comunicado,
									'DATA'				=>	$data,
									'COMENTARIO'		=>	$comentario,
									'OBRIGATORIO'		=>	$obrigatorio
									));	
				
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

?>
