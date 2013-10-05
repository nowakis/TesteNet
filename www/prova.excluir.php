<?php
/******************************************************************
Script .........: TesteNet
Por ............: Fabio Nowaki
Data ...........: 10/05/2008
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

if (isset($_GET['msg_codigo']) AND strlen(trim($_GET['msg_codigo']))>0) {
	$msg_codigo = trim($_GET['msg_codigo']);
}


##############################################################################
##############                   EXCLUIR                     	##############
##############################################################################	

if (isset($_POST['btn_acao']) AND strlen(trim($_POST['btn_acao']))>0) {
	
	$prova = addslashes(trim($_POST['prova']));

	try {
		$banco->iniciarTransacao();
		$pro = $sessionFacade->recuperarProva($prova); 
		$sessionFacade->excluirProva($pro);
		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: prova.lista.php?msg_codigo=1");
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
		array_push($msg_erro,$e->getMessage());
	}
}

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "prova";
$titulo     = "Exclusão de Prova";
$sub_titulo = "Exclusão de Prova";

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
	
if (isset($_GET['prova']) AND strlen(trim($_GET['prova']))>0){

	$prova = trim($_GET['prova']);

	try {
		$prov = $sessionFacade->recuperarProvaCorrecao($prova); 

		$obj_prova = $prov->getProva();

		if ( is_object($prov)){
			$prova				= $obj_prova->getId();
			$titulo_prova		= $obj_prova->getTitulo();
			$disciplina			= $obj_prova->getDisciplina()->getNome();
			$curso				= $obj_prova->getDisciplina()->getCurso()->getNome();
			$professor			= $obj_prova->getProfessor()->getNome();
			#$numero_perguntas	= $obj_prova->getNumeroPerguntas();
			$data				= $obj_prova->getData();
			$data_inicio		= $obj_prova->getDataInicio();
			$data_termino		= $obj_prova->getDataTermino();
			#$dificuldade		= $obj_prova->getDificuldade();
		}else{
			throw new Exception('Prova não encontrada!!!');
			#array_push($msg_erro,"Prova não encontrada!");
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

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'NOTA'				=>	$nota,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	$disciplina,
							'CURSO'				=>	$curso,
							'PROFESSOR'			=>	$professor
));	

if (is_object($prov)){
	for ($j=0;$j<$prov->getQtdeProvaRespondida();$j++){

		$prova_aluno = $prov->getProvaRespondida($j);

		$nota = $prova_aluno->getNota();

		if (strlen($nota)==0){
			$nota = "<b style='color:#EE9611; font-size:14px'> - </b>";
		}elseif ($nota>6){
			$nota = "<b style='color:#0000FF; font-size:14px'>".$nota."</b>";
		}else{
			$nota = "<b style='color:#FF0000; font-size:14px'>".$nota."</b>";
		}

		$model->assign_block_vars('prova_aluno', array('I'					=>	$j+1,
														'PROVA'				=>	$prov->getProva()->getId(),
														'ALUNO'				=>	$prova_aluno->getAluno()->getId(),
														'RA'				=>	$prova_aluno->getAluno()->getRA(),
														'NOME_ALUNO'		=>	$prova_aluno->getAluno()->getNome(),
														'NOTA'				=>	$nota,
														'DATA_INICIO'		=>	$prova_aluno->getDataInicio(),
														'DATA_TERMINO'		=>	$prova_aluno->getDataTermino(),
														'CLASSE'			=>	$j%2==0?"class='odd'":""
														));
	}
}


fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>