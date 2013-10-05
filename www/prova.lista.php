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
if ($filtro=='agendada'){
	$titulo="Provas Agendadas";
	$sub_titulo="Lista dos Provas Agendadas";
}
if ($filtro=='realizada'){
	$titulo="Provas Realizadas";
	$sub_titulo="Lista dos Provas Realizadas";
}
if ($filtro=='correcao'){
	$titulo="Provas Pendentes de Correção";
	$sub_titulo="Lista dos Provas Pendentes de Correção";
}

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
	if ($filtro <> 'agendada' AND $filtro <> 'realizada' AND $filtro <> 'correcao'){
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
		if ($filtro == 'agendada'){
			$msg_aux = 'agendada';
		}
		if ($filtro == 'realizada'){
			$msg_aux = 'realizada';
		}
		if ($filtro == 'correcao'){
			$msg_aux = 'pendente de correção';
		}
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma prova '.$msg_aux));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de provas: ".$i));
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
