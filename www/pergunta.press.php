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


##############################################################################
##############                      ALTERAR                   	##############
##############################################################################	
	
if (isset($_GET['pergunta']) AND strlen(trim($_GET['pergunta']))>0){
	$pergunta = trim($_GET['pergunta']);
}

if (isset($_GET['prova_pergunta']) AND strlen(trim($_GET['prova_pergunta']))>0){
	$prova_pergunta = trim($_GET['prova_pergunta']);
}

if (strlen($pergunta)>0 OR strlen($prova_pergunta)>0){

	try {
		if (strlen($pergunta)>0){
			$perg = $sessionFacade->recuperarPergunta($pergunta); 
		}else{
			$perg = $sessionFacade->recuperarProvaPergunta($prova_pergunta); 
		}

		if (is_object($perg)) {
			$pergunta				=	$perg->getId();
			$titulo_pergunta		=	$perg->getTitulo();
			$topico					=	(is_object($perg->getTopico()))?$perg->getTopico()->getId():"";
			$disciplina				=	(is_object($perg->getTopico()))?is_object($perg->getTopico()->getDisciplina())?$perg->getTopico()->getDisciplina()->getId():"":"";
			$tipo_pergunta			=	(is_object($perg->getTipoPergunta()))?$perg->getTipoPergunta()->getId():"";
			$dificuldade			=	$perg->getDificuldade('texto');
		}else{
			throw new Exception("Pergunta não encontrado!");
		}
		$banco->desconecta();
	}catch(Exception $e) {
		$banco->desconecta();
		array_push($msg_erro,$e->getMessage());
	}
}

if (is_object($perg)){
	$model->assign_vars(array(		'PERGUNTA'			=>	$pergunta,
									'DISCIPLINA'		=>	(is_object($perg->getTopico()))?is_object($perg->getTopico()->getDisciplina())?$perg->getTopico()->getDisciplina()->getNome():"":"",
									'TOPICO'			=>	(is_object($perg->getTopico()))?$perg->getTopico()->getDescricao():"",
									'TITULO_PERGUNTA'	=>	$titulo_pergunta,
									'DIFICULDADE'		=>	$dificuldade,
									'TIPO_PERGUNTA'		=>	(is_object($perg->getTipoPergunta()))?$perg->getTipoPergunta()->getDescricao():"",
									'TIPO_PERGUNTA_ID'	=>	(is_object($perg->getTipoPergunta()))?$perg->getTipoPergunta()->getId():"",
									'SEXOM'				=>	($sexo=="M")?' CHECKED ':'',
									'SEXOF'				=>	($sexo=="F")?' CHECKED ':''
									));	
				

	/* DISSERTATIVA */
	$qtde_item = 1;
	$qtde_inicio = 0;
	if (is_object($perg)){
		if (is_object($perg->getTipoPergunta())){
			if ($perg->getTipoPergunta()->getId()=="1"){
				for ($i=0; $i<$perg->getQtdeResposta(); $i++){
					$qtde_inicio = $i+1;
					$model->assign_block_vars('dissertativa',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																	'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																	'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																	'I'					=>	$i
																	));
				}
			}
		}
	}

	/* MULTIPLA-ESCOLHA  */
	$qtde_item = 10;
	$qtde_inicio = 0;
	if (is_object($perg)){
		if (is_object($perg->getTipoPergunta())){
			if ($perg->getTipoPergunta()->getId()=="2"){
				for ($i=0; $i<$perg->getQtdeResposta(); $i++){
					$qtde_inicio = $i+1;
					$model->assign_block_vars('multipla_escolha',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																		'CORRETA'			=>	$perg->getResposta($i)->getRespostaCorreta()=="1"?"CHECKED":"",
																		'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																		'I'					=>	$i,
																		'NUMERO'			=>	strtolower(chr($i+65))
																		));
				}
			}
		}
	}
	
	/* VERDADEIRO-FALSO  */
	$qtde_item = 10;
	$qtde_inicio = 0;
	if (is_object($perg)){
		if (is_object($perg->getTipoPergunta())){
			if ($perg->getTipoPergunta()->getId()=="3"){
				for ($i=0; $i<$perg->getQtdeResposta(); $i++){
					$qtde_inicio = $i+1;
					$model->assign_block_vars('verdadeiro_falso',array('RESPOSTA'			=>	$perg->getResposta($i)->getId(),
																		'RESPOSTA_TEXTO'	=>	$perg->getResposta($i)->getRespostaTexto(),
																		'CORRETA'			=>	$perg->getResposta($i)->getRespostaCorreta()=="1"?"CHECKED":"",
																		'CLASSE'			=>  ($i%2==0)?"class='odd'":"",
																		'I'					=>	$i,
																		'NUMERO'			=>	$i+1
																		));
				}
			}
		}
	}
	/* COMPLETE  */
	$qtde_item = 10;
	$qtde_inicio = 0;
	if (is_object($perg)){
		if (is_object($perg->getTipoPergunta())){
			if ($perg->getTipoPergunta()->getId()=="4"){
				for ($i=0; $i<$perg->getQtdeResposta(); $i++){
					$qtde_inicio = $i+1;
					$model->assign_block_vars('complete',array(	'RESPOSTA'				=>	$perg->getResposta($i)->getId(),
																'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
																'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getResposta($i)->getRespostaFilho())?$perg->getResposta($i)->getRespostaFilho()->getRespostaTexto():"",
																'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
																'I'						=>	$i+1,
																'NUMERO'				=>	$i+1
																));
				}
			}
		}
	}

	/* LACUNA  */
	$qtde_item = 10;
	$qtde_inicio = 0;
	if (is_object($perg)){
		if (is_object($perg->getTipoPergunta())){
			if ($perg->getTipoPergunta()->getId()=="5"){
				for ($i=0; $i<$perg->getQtdeResposta(); $i++){
					$qtde_inicio = $i+1;
					$model->assign_block_vars('lacuna',array('RESPOSTA'				=>	$perg->getResposta($i)->getId(),
															'RESPOSTA_TEXTO'		=>	$perg->getResposta($i)->getRespostaTexto(),
															'RESPOSTA_TEXTO_FILHO'	=>	is_object($perg->getResposta($i)->getRespostaFilho())?$perg->getResposta($i)->getRespostaFilho()->getRespostaTexto():"",
															'CLASSE'				=>  ($i%2==0)?"class='odd'":"",
															'I'						=>	$i+1,
															'NUMERO'				=>	strtolower(chr($i+65))
															));
				}
			}
		}
	}
}

fn_mostra_mensagens($model,$msg_ok,$msg_erro);

$model->pparse($_nome_programa);

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

?>
