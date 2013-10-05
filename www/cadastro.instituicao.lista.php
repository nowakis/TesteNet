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

$msg_erro	= "";
$msg		= "";


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Instituições Cadastrados";
$sub_titulo="Lista das Instituições Cadastradas";

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

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

try {
	$instituicoes = $sessionFacade->recurarInstituicaoTodosDAO();
	for($i= 0; $i < count($instituicoes); $i++) { 
		$model->assign_block_vars('instituicao', array(	'INSTITUICAO'	=>	$instituicoes[$i]->getId(),
														'NOME'			=>	$instituicoes[$i]->getNome(),
														'CLASSE'		=>	$i%2==0?"class='odd'":""
													));
	}

	if (count($instituicoes)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma instituição cadastrada!'));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de instituições cadastradas: ".$i));
	}

}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}


$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
