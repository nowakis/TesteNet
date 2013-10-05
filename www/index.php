<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/
//ini_set('session.cache_limiter', 'private');

//	$destroi	=	session_destroy();****************************************/

##############################################################################
## INCLUDES E CONEXÔES BANCO
##############################################################################

session_start();
require_once "funcoes.php";
require_once "class/class.Template.inc.php";
require_once "class/class.SessionFacade.php";
require_once "banco.con.php";
require_once "autentica_usuario.php";


##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	


if (isset($_GET['layout']))
	$layout=$_GET['layout'];
else $layout="";


//$layout="Cadastro"
$layout       ="inicio";
$titulo       ="MENU INICIAL";
$sub_titulo   ="MENU INICIAL";


include "cabecalho.php";



##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.".php"));

##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

if (isset($_GET['instituicao']) AND strlen(trim($_GET['instituicao']))>0){
	$_SESSION["login_instituicao"]	= $_GET['instituicao'];
	$_SESSION["login_md5"]			= md5($_GET['instituicao']."prova".$_login_login);
	$model->assign_block_vars('refresh_tela', array('PHP_SELF' => $_nome_programa.".php"));
}

if (strlen($_login_professor)>0){
	$aviso = 0;

	$qtde_cursos          = $sessionFacade->recuperarQtdeCursoDAO();
	$qtde_disciplinas     = $sessionFacade->recuperarDisciplinaQtdeTodosDAO();
	$qtde_perguntas       = $sessionFacade->recuperarPerguntaQtdeTodosDAO();
	$qtde_alunos          = $sessionFacade->recuperarQtdeAlunosDAO();
	$qtde_prova_agendada  = $sessionFacade->recuperarProvaQtdeDAO('agendada');
	$qtde_prova_realizada = $sessionFacade->recuperarProvaQtdeDAO('realizada');
	$qtde_prova_correcao  = $sessionFacade->recuperarProvaQtdeDAO('correcao');
	
	if ($qtde_cursos==0){
		$aviso++;
	}

	if ($qtde_disciplinas==0){
		$aviso++;
	}

	if ($qtde_perguntas==0){
		$aviso++;
	}

	if ($qtde_alunos==0){
		$aviso++;
	}

	if ($qtde_prova_agendada == 0 ){
		$msg_prova = "N&atilde;o h&aacute; nenhum prova agendada no momento. <br>Para agendar, <a href='prova.criar.php'>clique aqui</a>.";
	}else{
		$msg_prova = "H&aacute; $qtde_prova_agendada provas agendadas no momento. <br>Para visualiza-las, <a href='prova.lista.php?filtro=agendada'>clique aqui</a>.<br><br>Para agendar uma nova prova <a href='prova.criar.php'>clique aqui</a>.";
	}
	 
	if ($qtde_prova_correcao == 0 ){
		$msg_correcao = "N&atilde;o h&aacute; nenhuma prova pendente de correção até o momento.";
	}else{
		$msg_correcao = "H&aacute; $qtde_prova_correcao provas aguardando correção. <br>Para visualiza-las, <a href='prova.lista.php?filtro=correcao'>clique aqui</a>.";
	}

	$model->assign_block_vars('inicio_professor',array('NOME_USUARIO'					=> $_login_nome,
														'EMAIL_USUARIO'					=> $_login_email,
														'RESUMO_QTDE_CURSOS'			=> $qtde_cursos,
														'RESUMO_QTDE_DISCIPLINAS'		=> $qtde_disciplinas,
														'RESUMO_QTDE_PERGUNTAS'			=> $qtde_perguntas,
														'RESUMO_QTDE_ALUNOS'			=> $qtde_alunos,
														'RESUMO_QTDE_PROVAS_AGENDADAS'	=> $qtde_prova_agendada,
														'RESUMO_QTDE_PROVAS_REALIZADAS'	=> $qtde_prova_realizada,
														'AVISO_PROVAS'					=> $msg_prova,
														'AVISO_CORRECAO'				=> $msg_correcao
		));


	if ($aviso>0){
		$model->assign_block_vars('inicio_professor.aviso', array());
		if ($qtde_cursos==0){
			$model->assign_block_vars('inicio_professor.aviso.curso', array());
		}
		if ($qtde_disciplinas==0){
			$model->assign_block_vars('inicio_professor.aviso.disciplina', array());
		}
		if ($qtde_perguntas==0){
			$model->assign_block_vars('inicio_professor.aviso.pergunta', array());
		}
		if ($qtde_alunos==0){
			$model->assign_block_vars('inicio_professor.aviso.aluno', array());
		}
	}
}

if (strlen($_login_aluno)>0){
	$aviso = 0;

	$qtde_cursos          = $sessionFacade->recuperarQtdeCursoDAO();
	$qtde_disciplinas     = $sessionFacade->recuperarDisciplinaQtdeTodosDAO();
	$qtde_perguntas       = $sessionFacade->recuperarPerguntaQtdeTodosDAO();
	$qtde_alunos          = $sessionFacade->recuperarQtdeAlunosDAO();
	$qtde_prova_agendada  = $sessionFacade->recuperarProvaQtdeDAO('agendada');
	$qtde_prova_realizada = $sessionFacade->recuperarProvaQtdeDAO('realizada');
	$comunicados          = $sessionFacade->recuperarComunicadoTodosDAO('novos');

	if ($qtde_cursos==0){
		$aviso++;
	}

	if ($qtde_disciplinas==0){
		$aviso++;
	}

	if ($qtde_perguntas==0){
		$aviso++;
	}

	if ($qtde_alunos==0){
		$aviso++;
	}

	if ($qtde_prova_agendada == 0 ){
		$msg_prova = "N&atilde;o h&aacute; nenhum prova agendada no momento.";
	}else{
		$msg_prova = "H&aacute; $qtde_prova_agendada prova(s) agendada(s) no momento. <br>Para visualiza-las, <a href='prova.lista.aluno.php?filtro=agendada'>clique aqui</a>.";
	}
	 
	if ($qtde_prova_correcao == 0 ){
	#	$msg_correcao = "N&atilde;o h&aacute; nenhuma prova pendente de correção até o momento.";
	}else{
	#	$msg_correcao = "H&aacute; $qtde_prova_correcao provas aguardando correção. <br>Para visualiza-las, <a href='#'>clique aqui</a>.";
	}

	$model->assign_block_vars('inicio_aluno',array(		'NOME_USUARIO'					=> $_login_nome,
														'EMAIL_USUARIO'					=> $_login_email,
														'RESUMO_QTDE_CURSOS'			=> $qtde_cursos,
														'RESUMO_QTDE_DISCIPLINAS'		=> $qtde_disciplinas,
														'RESUMO_QTDE_PERGUNTAS'			=> $qtde_perguntas,
														'RESUMO_QTDE_ALUNOS'			=> $qtde_alunos,
														'RESUMO_QTDE_PROVAS_AGENDADAS'	=> $qtde_prova_agendada,
														'RESUMO_QTDE_PROVAS_REALIZADAS'	=> $qtde_prova_realizada,
														'AVISO_PROVAS'					=> $msg_prova,
														'AVISO_CORRECAO'				=> $msg_correcao
		));


	if ($aviso>0){
		$model->assign_block_vars('inicio_aluno.aviso', array());
		if ($qtde_cursos==0){
			$model->assign_block_vars('inicio_aluno.aviso.curso', array());
		}
		if ($qtde_disciplinas==0){
			$model->assign_block_vars('inicio_aluno.aviso.disciplina', array());
		}
		if ($qtde_perguntas==0){
			$model->assign_block_vars('inicio_aluno.aviso.pergunta', array());
		}
		if ($qtde_alunos==0){
			$model->assign_block_vars('inicio_aluno.aviso.aluno', array());
		}
	}

	for($i= 0; $i < count($comunicados); $i++) { 
		$model->assign_block_vars('inicio_aluno.comunicado', array(	'COMUNICADO'		=>	$comunicados[$i]->getId(),
																	'TITULO_COMUNICADO'	=>	$comunicados[$i]->getTitulo(),
																	'DATA'				=>	$comunicados[$i]->getData()
		));
	}

	if (count($comunicados)==0){
		$model->assign_block_vars('inicio_aluno.comunicado_nao', array());
	}
}



$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>