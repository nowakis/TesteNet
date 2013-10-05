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
include_once "class.Template.inc.php";
require_once('banco.inc.php');
require_once("autentica_usuario.php");
include_once "funcoes.php";

include_once "class.banco.php";
include_once "class.SessionFacade.php";

#$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');


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
	
	$disciplina		= addslashes(trim($_POST['disciplina']));
	$nome			= addslashes(trim($_POST['nome']));
	$curso			= addslashes(trim($_POST['curso']));
	$professor		= addslashes(trim($_POST['professor']));

	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta();

		$obj_instituicao = $sessionFacade->recuperarInstituicao($_login_instituicao);
		$obj_curso		 = $sessionFacade->recuperarCurso($curso);
		$obj_professor	 = $sessionFacade->recuperarProfessor($professor);

		$disc = new Disciplina();
		$disc->setId($disciplina);
		$disc->setNome($nome);
		$disc->setInstituicao($obj_instituicao);
		$disc->setCurso($obj_curso);
		$disc->setProfessor($obj_professor);

		$sessionFacade->gravarDisciplina($disc);
		$banco->desconecta(); 
		header("Location: cadastro.disciplina.php?disciplina=".$disc->getId()."&msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desconecta(); 
		//header("location: cadastrarCliente.php?msg=".$e->getMessage()); 
		array_push($msg_erro,$e->getMessage());
		#exit;
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Cadastro de Disciplina";
$sub_titulo = "Disciplina: Cadastrar";

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
	
if (isset($_GET['disciplina']) AND strlen(trim($_GET['disciplina']))>0){

	$disciplina = trim($_GET['disciplina']);
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 

	try {
		$banco->conecta(); 
		$disc = $sessionFacade->recuperarDisciplina($disciplina); 

		if ( is_object($disc)){
			$disciplina		= $disc->getId();
			$nome			= $disc->getNome();
			$curso			= $disc->getCurso()->getId();
			$professor		= is_object($disc->getProfessor())?$disc->getProfessor()->getId():"";
		}else{
			array_push($msg_erro,"Disciplina não encontrado!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
}

/*         PROFESSOR         */

$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco);
try {
	$banco->conecta();
	$professores = $sessionFacade->recuperarProfessorTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

/*         CURSOS            */

$banco = new BancodeDados(); 
$sessionFacade = new SessionFacade($banco);
try {
	$banco->conecta();
	$cursos = $sessionFacade->recuperarCursoTodosDAO();
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}


if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);


$model->assign_vars(array(	'DISCIPLINA'	=>	$disciplina,
							'NOME'			=>	$nome,
							'CURSO'			=>	optionCurso($cursos,$curso),
							'PROFESSOR'		=>	optionProfessor($professores,$professor)
));	



			
$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>
