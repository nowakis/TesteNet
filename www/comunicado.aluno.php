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

if (isset($_GET['leitura']) AND strlen(trim($_GET['leitura']))>0) {
	
	$comunicado = addslashes(trim($_GET['comunicado']));

	try {
		if (strlen($comunicado)==0){
			throw new Exception("Comunicado não encontrado!.",0);
		}
		$comun = $sessionFacade->recuperarComunicado($comunicado); 
		$aluno = $sessionFacade->recuperarAluno($_login_aluno);

		if (is_object($comun)){
			$cursos = $sessionFacade->confirmarLeituraComunicado($comun, $aluno);
		}else{
			throw new Exception("Comunicado não encontrado!.",0);
		}
		header("Location: comunicado.aluno.php?comunicado=".$comun->getId()."&msg_codigo=1");
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
$titulo     = "Cadastro de Comunicado";
$sub_titulo = "Comunicado: Cadastrar";

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
	
if (isset($_GET['comunicado']) AND strlen(trim($_GET['comunicado']))>0){

	$comunicado = trim($_GET['comunicado']);
	try {
		$comun = $sessionFacade->recuperarComunicado($comunicado); 

		if (is_object($comun)){
			$comunicado			= $comun->getId();
			$curso				= is_object($comun->getCurso())?$comun->getCurso()->getNome():"";
			$professor			= is_object($comun->getProfessor())?$comun->getProfessor()->getNome():"";
			$titulo_comunicado	= $comun->getTitulo();
			$data				= $comun->getData();
			$comentario			= $comun->getComentario();
			$obrigatorio		= $comun->getObrigatorio();

			if (strlen($msg_codigo)==0 AND $obrigatorio == '1'){
				array_push($msg_ok,"Confirme a leitura do comunicado clicando em 'Confirmar Leitura'");
			}

			$cursos		 = $sessionFacade->recuperarCursoTodosDAO();
		}else{
			array_push($msg_erro,"Comunicado não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Leitura confirmada!");
	}
}

$model->assign_vars(array(	'COMUNICADO'		=>	$comunicado,
							'CURSO'				=>	$curso,
							'PROFESSOR'			=>	$professor,
							'TITULO_COMUNICADO'	=>	$titulo_comunicado,
							'DATA'				=>	$data,
							'COMENTARIO'		=>	$comentario,
							'OBRIGATORIO'		=>	$obrigatorio==1?"checked":"",
							'NAO_OBRIGATORIO'	=>	$obrigatorio!=1?"checked":"",
							'BTN_NOME'			=>  (strlen($comunicado)>0)?"Confirmar Leitura":"OK"
));	

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

			
$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>