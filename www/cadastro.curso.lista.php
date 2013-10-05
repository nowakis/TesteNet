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
$msg_ok		= array();
$msg		= "";

if (isset($_GET['excluir'])){
	$excluir = trim($_GET['excluir']);
	if (strlen($excluir)>0){
		try {
			$banco->iniciarTransacao();
			$curs = $sessionFacade->recuperarCurso($excluir); 
			$sessionFacade->excluirCurso($curs);
			$banco->efetivarTransacao();
			$banco->desconecta(); 
			header("Location: cadastro.curso.lista.php?msg_codigo=2");
			exit;
		} catch(Exception $e) { 
			$banco->desfazerTransacao();
			array_push($msg_erro,$e->getMessage());
		}
	}
}


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Cursos Cadastrados";
$sub_titulo="Lista dos Cursos Cadastradas";

include "cabecalho.php";


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
	if ($msg_codigo == 2){
		array_push($msg_ok,"Curso excluído com sucesso!");
	}
}

try {
	$cursos = $sessionFacade->recuperarCursoTodosDAO('opcional');
	for($i= 0; $i < sizeof($cursos); $i++) { 
		$model->assign_block_vars('curso', array(	'CURSO'			=>	$cursos[$i]->getId(),
													'NOME'			=>	$cursos[$i]->getNome(),
													'CLASSE'		=>	$i%2==0?"class='odd'":""
		));
	}
	if (count($cursos)==0){
		$model->assign_block_vars('naoencontrado', array('MSG' => 'Nenhum curso cadastrado!'));
	}else{
		$model->assign_block_vars('paginacao', array('CONTADOR' => "Total de Cursos cadastrados: ".$i));
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