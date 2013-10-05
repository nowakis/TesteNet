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

if (isset($_GET['filtro'])){
	$filtro = $_GET['filtro'];
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Provas Cadastradas";
$sub_titulo="Lista dos Provas Cadastradas";

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
	if ($filtro <> 'agendada' AND $filtro <> 'realizada'){
		$filtro = '';
	}

	$provas = $sessionFacade->recuperarProvaTodosDAO($filtro);
	for($i= 0; $i < count($provas); $i++) { 
		$model->assign_block_vars('prova', array(	'PROVA'		=>	$provas[$i]->getId(),
													'TITULO'	=>	$provas[$i]->getTitulo(),
													'CURSO'		=>	$provas[$i]->getDisciplina()->getCurso()->getNome(),
													'DISCIPLINA'=>	$provas[$i]->getDisciplina()->getNome(),
													'DATA'		=>	FormataData($provas[$i]->getData(),'às'),
													'DATA_INICIO'=>	FormataData($provas[$i]->getDataInicio()),
													'DATA_TERMINO'=>FormataData($provas[$i]->getDataTermino()),
													'CLASSE'	=>	$i%2==0?"class='odd'":""
		));
	}
	if (count($provas)==0){
		$msg_aux = 'cadastrada';
		if ($filtro == 'agendadas'){
			$msg_aux = 'agendada';
		}
		if ($filtro == 'realizadas'){
			$msg_aux = 'realizada';
		}
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma prova '.$msg_aux));
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
