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

$msg_erro	= array();
$msg		= "";

if ($_login_unificado == "1"){
	header("Location: cadastro.professor.php?professor=".$_login_professor);
	exit;
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Professors Cadastrados";
$sub_titulo="Lista dos Professors Cadastradas";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

try {
	$professors = $sessionFacade->recuperarProfessorTodosDAO();
	for($i= 0; $i < sizeof($professors); $i++) { 
		$model->assign_block_vars('professor', array(	'PROFESSOR'		=>	$professors[$i]->getId(),
														'NOME'			=>	$professors[$i]->getNome(),
														'EMAIL'			=>	$professors[$i]->getEmail(),
														'LOGIN'			=>	$professors[$i]->getLogin(),
														'ATIVO'			=>	$professors[$i]->getAtivo()==1?"ATIVO":"INATIVO",
														'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";

?>