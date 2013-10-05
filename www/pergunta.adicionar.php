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

##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout     = "cadastro";
$titulo     = "Prova";
$sub_titulo = "Prova e Pergunta";

#include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$_nome_programa = basename($_SERVER['PHP_SELF'],'.php');

$theme = ".";
$model = new Template($theme);
$model->set_filenames(array($_nome_programa => $_nome_programa.'.htm'));
$model->assign_vars(array('_NOME_PROGRAMA' => $_nome_programa.'.php'));

try {
	$linha = 0;
	
	$disciplina = addslashes(trim($_GET['disciplina']));
	if (strlen($disciplina)==0){
		throw new Exception("Selecione a disciplina!"); 
	}

	$disciplina = $sessionFacade->recuperarDisciplina($disciplina);

	if (!$disciplina){
		throw new Exception("Disciplina não encontrada!"); 
	}

	$perguntas = $sessionFacade->recuperarPerguntaDisciplinaTodosDAO($disciplina);

	for($i= 0; $i < count($perguntas); $i++) { 

		$model->assign_block_vars('perguntas', array(	'PERGUNTA'			=>	$perguntas[$i]->getId(),
														'TITULO_PERGUNTA'	=>	$perguntas[$i]->getTituloReduzido(40),
														'TITULO_PERGUNTA_C'	=>	$perguntas[$i]->getTitulo()."\n\n",
														'TOPICO'			=>	$perguntas[$i]->getTopico()->getDescricao(),
														'TIPO_PERGUNTA'		=>	$perguntas[$i]->getTipoPergunta()->getDescricao(),
														'DIFICULDADE'		=>	$perguntas[$i]->getDificuldade('texto'),
														'DIFICULDADE_SO_TEXTO'=>$perguntas[$i]->getDificuldade('so_texto'),
														'CLASSE'			=>	$i%2==0?"class='odd'":""
		));
	}
	$banco->desconecta();
}catch(Exception $e) {
	$banco->desconecta();
	array_push($msg_erro,$e->getMessage());
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

?>
