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
	
	$prova				= addslashes(trim($_POST['prova']));
	$titulo_prova		= addslashes(trim($_POST['titulo_prova']));
	$curso				= addslashes(trim($_POST['curso']));
	$disciplina			= addslashes(trim($_POST['disciplina']));
	$numero_perguntas	= addslashes(trim($_POST['numero_perguntas']));
	$data_inicio		= addslashes(trim($_POST['data_inicio']));
	$data_termino		= addslashes(trim($_POST['data_termino']));
	$dificuldade		= addslashes(trim($_POST['dificuldade']));

	$qtde_topicos		= addslashes(trim($_POST['qtde_topicos']));

	try {
		$prov = new Prova();
		$prov->setId($prova);
		$prov->setTitulo($titulo_prova);
		$prov->setNumeroPerguntas($numero_perguntas);
		$prov->setData(date("d/m/Y H:i"));
		$prov->setDataInicio($data_inicio);
		$prov->setDataTermino($data_termino);
		$prov->setDificuldade($dificuldade);

		if (strlen($disciplina)>0){
			$disc = $sessionFacade->recuperarDisciplina($disciplina); 
			$prov->setDisciplina($disc);
		}

		$prof = $sessionFacade->recuperarProfessor($_login_professor); 
		$prov->setProfessor($prof);

		/* Topicos */
		for ($i=0; $i<$qtde_topicos;$i++){
			$topico = addslashes(trim($_POST['topico_'.$i]));
			if (strlen($topico)>0){
				$topic = $sessionFacade->recuperarTopico($topico); 
				if ( is_object($topic)){
					$prov->addTopico($topic);
				}
			}
		}

		$sessionFacade->gravarProva($prov);
		$banco->desconecta(); 
		header("Location: prova.criar.php?prova=".$prov->getId()."&msg_codigo=1");
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
$titulo     = "Cadastro de Prova";
$sub_titulo = "Prova: Cadastrar";

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
		$prov = $sessionFacade->recuperarProva($prova); 

		if ( is_object($prov)){
			$prova				= $prov->getId();
			$titulo_prova		= $prov->getTitulo();
			$disciplina			= $prov->getDisciplina()->getId();
			$professor			= $prov->getProfessor()->getId();
			$numero_perguntas	= $prov->getNumeroPerguntas();
			$data				= $prov->getData();
			$data_inicio		= $prov->getDataInicio();
			$data_termino		= $prov->getDataTermino();
			$dificuldade		= $prov->getDificuldade();
		}else{
			array_push($msg_erro,"Prova não encontrada!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}

try {
	$professores = $sessionFacade->recuperarProfessorTodosDAO();
	$cursos      = $sessionFacade->recuperarCursoTodosDAO();
	$disciplinas = $sessionFacade->recuperarDisciplinaTodosDAO();
}catch(Exception $e) {
	array_push($msg_erro,$e->getMessage());
}

if (strlen($msg_codigo)>0){
	if ($msg_codigo == 1){
		array_push($msg_ok,"Informações salvas com sucesso!");
	}
}

$model->assign_vars(array(	'PROVA'				=>	$prova,
							'TITULO_PROVA'		=>	$titulo_prova,
							'NUMERO_PERGUNTAS'	=>	$numero_perguntas,
							'DATA_INICIO'		=>	$data_inicio,
							'DATA_TERMINO'		=>	$data_termino,
							'DIFICULDADE'		=>	$dificuldade,
							'DISCIPLINA'		=>	optionDisciplina($disciplinas,$disciplina),
							'CURSO'				=>	optionCurso($cursos,$curso),
							'PROFESSOR'			=>	optionProfessor($professores,$professor)
));	

$lista_topicos = array();
if (is_object($prov)){
	for ($i=0;$i<$prov->getQtdeTopico();$i++){
		array_push($lista_topicos,$prov->getTopico($i)->getId());
	}
}

if (count($disciplinas)){
	$count=0;
	for ($i=0; $i<count($disciplinas); $i++){
		$id_disc = $disciplinas[$i]->getId();
		try {
			$topicos = $sessionFacade->recuperarTopicoTodosDAO($id_disc);
		}catch(Exception $e) {
			array_push($msg_erro,$e->getMessage());
		}
		$model->assign_block_vars('topicos',array( 'I' => $i ));
		for ($j=0; $j<count($topicos); $j++){
			if (in_array($topicos[$j]->getId(),$lista_topicos)>0) {
				$selecionado = " checked ";
			}else{
				$selecionado = " ";
			}
			$model->assign_block_vars('topicos.item', array('J'			=>	$count,
															'TOPICO'	=>	$topicos[$j]->getId(),
															'DESCRICAO'	=>	$topicos[$j]->getDescricao(),
															'CHECKED'	=>	$selecionado
														));
			$count++;
		}
	}
	$model->assign_vars(array( 'QTDE_TOPICOS' => $count));
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>