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

$layout     = "relatorio";
$titulo     = "Relatório de Provas";
$sub_titulo = "Relatório: Provas";

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
	$instituicao = $sessionFacade->recuperarInstituicao($_login_instituicao);

	$relatorio = new Relatorio();
	$relatorio->setInstituicao($instituicao);
	$relatorio->setRelatorio('prova');
	$resultado = $relatorio->gerarRelatorio($sessionFacade);

	for($i= 0; $i < count($resultado); $i++) { 
		$model->assign_block_vars('relatorio', array(	'ALUNO'		=>	$resultado[$i][0],
														'RA'		=>	$resultado[$i][1],
														'NOME_ALUNO'=>	$resultado[$i][2],
														'DATA'		=>	$resultado[$i][3],
														'PROVA'		=>	$resultado[$i][4],
														'CURSO'		=>	$resultado[$i][5],
														'DISCIPLINA'=>	$resultado[$i][6],
														'NOTA'		=>	$resultado[$i][7],
														'STATUS'	=>	$resultado[$i][8],
														'CLASSE'	=>	$i%2==0?"class='odd'":""
		));
	}

	if (count($resultado)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhuma resultado encontrado'.$msg_aux));
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
