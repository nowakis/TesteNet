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

#$_descricao_programa = basename($_SERVER['PHP_SELF'],'.php');


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
##############            CADASTRAR / ALTERAR                	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$topico		= addslashes(trim($_POST['topico']));
	$disciplina	= addslashes(trim($_POST['disciplina']));
	$descricao	= addslashes(trim($_POST['descricao']));

	try {
		$obj_disciplina	 = $sessionFacade->recuperarDisciplina($disciplina);

		$topic = new Topico();
		$topic->setId($topico);
		$topic->setDisciplina($obj_disciplina);
		$topic->setDescricao($descricao);

		$sessionFacade->gravarTopico($topic);
		$banco->desconecta(); 
		header("Location: cadastro.topico.php?topico=".$topic->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Topico";
$sub_titulo = "Topico: Cadastrar";

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
	
if (isset($_GET['topico']) AND strlen(trim($_GET['topico']))>0){

	$topico = trim($_GET['topico']);
	try {
		$inst = $sessionFacade->recuperarTopico($topico); 

		if ( is_object($inst)){
			$topico		= $inst->getId();
			$disciplina	= $inst->getDisciplina()->getId();
			$curso		= $inst->getDisciplina()->getCurso()->getId();
			$descricao	= $inst->getDescricao();
		}else{
			array_push($msg_erro,"Topico não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

/*         CURSO / DISCIPLINA        */
try {
	$cursos = $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

$model->assign_vars(array(	'TOPICO'		=>	$topico,
							'DISCIPLINA'	=>	optionDisciplina($disciplinas,$disciplina),
							'CURSO'			=>	optionCurso($cursos,$curso),
							'DESCRICAO'		=>	$descricao,
							'BTN_NOME'		=>  (strlen($topico)>0)?"Confirmar Alterações":"Gravar"
));	

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

			
$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>